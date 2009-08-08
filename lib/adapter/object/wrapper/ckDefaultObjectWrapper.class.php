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
 * ckDefaultObjectWrapper is a pass-through implementation of ckObjectWrapper
 *
 * @package    ckWebServicePlugin
 * @subpackage adapter
 * @author     Christian Kerl <christian-kerl@web.de>
 */
class ckDefaultObjectWrapper extends ckObjectWrapper
{
  /* (non-PHPdoc)
   * @see lib/adapter/object/wrapper/ckObjectWrapper#canWrap()
   */
  public function canWrap($object)
  {
    return true;
  }

  /* (non-PHPdoc)
   * @see lib/adapter/object/wrapper/ckObjectWrapper#canUnwrap()
   */
  public function canUnwrap($object)
  {
    return true;
  }

  /* (non-PHPdoc)
   * @see lib/adapter/object/wrapper/ckObjectWrapper#wrapObject()
   */
  public function wrapObject($object)
  {
    return $object;
  }

  /* (non-PHPdoc)
   * @see lib/adapter/object/wrapper/ckObjectWrapper#unwrapObject()
   */
  public function unwrapObject($object)
  {
    return $object;
  }
}