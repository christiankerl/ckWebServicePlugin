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
 * ckArrayObjectWrapper
 *
 * @package    ckWebServicePlugin
 * @subpackage adapter
 * @author     Christian Kerl <christian-kerl@web.de>
 */
class ckArrayObjectWrapper extends ckObjectWrapper
{
  public function canWrap($object)
  {
    return is_array($object) || $object instanceof Traversable;
  }

  public function canUnwrap($object)
  {
    return $object instanceof ckGenericArray;
  }

  public function wrapObject($object)
  {
    $result = array();

    foreach($object as $key => $variable)
    {
      $result[$key] = self::wrap($variable);
    }

    return $result;
  }

  public function unwrapObject($object)
  {
    foreach($object as $key => $variable)
    {
      $object[$key] = self::unwrap($variable);
    }

    return $object->toArray();
  }
}