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
 * ckDefaultObjectWrapper
 *
 * @package    ckWebServicePlugin
 * @subpackage adapter
 * @author     Christian Kerl <christian-kerl@web.de>
 */
class ckDefaultObjectWrapper extends ckObjectWrapper
{
  public function canWrap($object)
  {
    return true;
  }

  public function canUnwrap($object)
  {
    return true;
  }

  public function wrapObject($object)
  {
    return $object;
  }

  public function unwrapObject($object)
  {
    return $object;
  }
}