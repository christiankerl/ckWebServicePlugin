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
 * ckDoctrineRecordWrapper is a ckObjectWrapper implementation, which can wrap Doctrine_Record objects into ckGenericObjectAdapters.
 *
 * @package    ckWebServicePlugin
 * @subpackage adapter
 * @author     Christian Kerl <christian-kerl@web.de>
 */
class ckDoctrineRecordWrapper extends ckGenericObjectAdapterWrapper
{
  /* (non-PHPdoc)
   * @see lib/adapter/object/wrapper/ckGenericObjectAdapterWrapper#canWrap()
   */
  public function canWrap($object)
  {
    return parent::canWrap($object) && $object instanceof Doctrine_Record;
  }

  /* (non-PHPdoc)
   * @see lib/adapter/object/wrapper/ckGenericObjectAdapterWrapper#canUnwrap()
   */
  public function canUnwrap($object)
  {
    return false;
  }
}