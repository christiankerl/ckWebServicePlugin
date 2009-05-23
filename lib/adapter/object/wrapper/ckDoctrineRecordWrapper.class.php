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
 * ckDoctrineRecordWrapper
 *
 * @package    ckWebServicePlugin
 * @subpackage adapter
 * @author     Christian Kerl <christian-kerl@web.de>
 */
class ckDoctrineRecordWrapper extends ckGenericObjectAdapterWrapper
{
  public function canWrap($object)
  {
    return parent::canWrap($object) && $object instanceof Doctrine_Record;
  }

  public function canUnwrap($object)
  {
    return false;
  }
}