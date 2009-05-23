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
 * ckObjectWrapper
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

  public static function addObjectWrapper(ckObjectWrapper $wrapper)
  {
    array_unshift(self::$wrappers, $wrapper);
  }

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

  public static function wrap($object)
  {
    return self::getObjectWrapper($object, self::MODE_WRAP)->wrapObject($object);
  }

  public static function unwrap($object)
  {
    return self::getObjectWrapper($object, self::MODE_UNWRAP)->unwrapObject($object);
  }

  public abstract function canWrap($object);

  public abstract function canUnwrap($object);

  public abstract function wrapObject($object);

  public abstract function unwrapObject($object);
}
