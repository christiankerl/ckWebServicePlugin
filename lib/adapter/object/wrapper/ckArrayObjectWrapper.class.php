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
 * ckArrayObjectWrapper is a ckObjectWrapper implementation, which can wrap arrays and Traversable objects and
 * can unwrap ckGenericArray objects. All elements of the array will also be wrapped or unwrapped.
 *
 * @package    ckWebServicePlugin
 * @subpackage adapter
 * @author     Christian Kerl <christian-kerl@web.de>
 */
class ckArrayObjectWrapper extends ckObjectWrapper
{
  /* (non-PHPdoc)
   * @see lib/adapter/object/wrapper/ckObjectWrapper#canWrap()
   */
  public function canWrap($object)
  {
    return is_array($object) || $object instanceof Traversable;
  }

  /* (non-PHPdoc)
   * @see lib/adapter/object/wrapper/ckObjectWrapper#canUnwrap()
   */
  public function canUnwrap($object)
  {
    return $object instanceof ckGenericArray;
  }

  /* (non-PHPdoc)
   * @see lib/adapter/object/wrapper/ckObjectWrapper#wrapObject()
   */
  public function wrapObject($object)
  {
    $result = array();

    foreach($object as $key => $variable)
    {
      $result[$key] = self::wrap($variable);
    }

    return $result;
  }

  /* (non-PHPdoc)
   * @see lib/adapter/object/wrapper/ckObjectWrapper#unwrapObject()
   */
  public function unwrapObject($object)
  {
    foreach($object as $key => $variable)
    {
      $object[$key] = self::unwrap($variable);
    }

    return $object->toArray();
  }
}