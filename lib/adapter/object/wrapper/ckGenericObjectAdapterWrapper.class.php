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
 * ckGenericObjectAdapterWrapper
 *
 * @package    ckWebServicePlugin
 * @subpackage adapter
 * @author     Christian Kerl <christian-kerl@web.de>
 */
class ckGenericObjectAdapterWrapper extends ckObjectWrapper
{
  protected static $cache = array();

  public function canWrap($object)
  {
    return is_object($object) && !($object instanceof ckGenericObjectAdapter);
  }

  public function canUnwrap($object)
  {
    return $object instanceof ckGenericObjectAdapter;
  }

  public function wrapObject($object)
  {
    $hash = spl_object_hash($object);

    if(!isset(self::$cache[$hash]))
    {
      self::$cache[$hash] = new ckGenericObjectAdapter($object);
    }

    return self::$cache[$hash];
  }

  public function unwrapObject($object)
  {
    return $object->getObject();
  }
}