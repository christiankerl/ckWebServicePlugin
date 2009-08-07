<?php
/**
 * This file is part of the ckWebServicePlugin
 *
 * @package   ckWebServicePlugin
 * @author    Christian Kerl <christian-kerl@web.de>
 * @copyright Copyright (c) 2009, Christian Kerl
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 * @version   SVN: $Id$
 */

/**
 * ckObjectWrapper provides methods to wrap objects, so they can be included in a SOAP response,
 * and proivdes methods to unwrap objects, if they are loaded from a SOAP request.
 *
 * @package    ckWebServicePlugin
 * @subpackage adapter
 * @author     Christian Kerl <christian-kerl@web.de>
 */
abstract class ckObjectWrapper
{
  const MODE_WRAP = 1;
  const MODE_UNWRAP = 2;

  protected static $wrappers = array();

  /**
   * Prepends a given ckObjectWrapper object to the registry list.
   *
   * @param ckObjectWrapper $wrapper A ckObjectWrapper instance
   */
  public static function addObjectWrapper(ckObjectWrapper $wrapper)
  {
    array_unshift(self::$wrappers, $wrapper);
  }

  /**
   * Gets a ckObjectWrapper, which can be used to wrap a given object with the given wrap mode.
   *
   * @param $object The object to wrap.
   * @param $mode The wrap mode.
   *
   * @return ckObjectWrapper The ckObjectWrapper, which can be used to wrap the object
   */
  protected static function getObjectWrapper($object, $mode)
  {
    foreach(self::$wrappers as $wrapper)
    {
      if(($mode == self::MODE_WRAP && $wrapper->canWrap($object)) || ($mode == self::MODE_UNWRAP && $wrapper->canUnwrap($object)))
      {
        return $wrapper;
      }
    }

    throw new RuntimeException('NoObjectWrapperFoundException');
  }

  /**
   * Wraps a given object so it can be included in a SOAP response.
   *
   * @param $object The plain object to wrap
   *
   * @return mixed The wrapped version of the given object, processable by the SOAP server
   */
  public static function wrap($object)
  {
    return self::getObjectWrapper($object, self::MODE_WRAP)->wrapObject($object);
  }

  /**
   * Unwraps a given object, which was loaded from a SOAP request.
   *
   * @param $object The wrapped object from a SOAP request
   *
   * @return mixed The plain object
   */
  public static function unwrap($object)
  {
    return self::getObjectWrapper($object, self::MODE_UNWRAP)->unwrapObject($object);
  }

  /**
   * Checks if the ckObjectWrapper can be used to wrap the given object.
   *
   * @param $object The object to wrap
   *
   * @return bool True, if the ckObjectWrapper can be used to wrap the given object, false otherwise
   */
  public abstract function canWrap($object);

  /**
   * Checks if the ckObjectWrapper can be used to unwrap the given object.
   *
   * @param $object The object to unwrap
   *
   * @return bool True, if the ckObjectWrapper can be used to unwrap the given object, false otherwise
   */
  public abstract function canUnwrap($object);

  /**
   * Wraps a given object.
   *
   * @param $object The object to wrap
   *
   * @return mixed The wrapped object
   */
  public abstract function wrapObject($object);

  /**
   * Unwraps a given object.
   *
   * @param $object The object to unwrap
   *
   * @return mixed The unwrapped object
   */
  public abstract function unwrapObject($object);
}
