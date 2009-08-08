<?php
/**
 * This file is part of the ckWebServicePlugin
 *
 * @package   ckWebServicePlugin
 * @author    Christian Kerl <christian-kerl@web.de>
 * @copyright Copyright (c) 2008, Christian Kerl
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 * @version   SVN: $Id$
 */

/**
 * Plugin configuration.
 *
 * @package    ckWebServicePlugin
 * @subpackage config
 * @author     Christian Kerl <christian-kerl@web.de>
 */
class ckWebServicePluginConfiguration extends sfPluginConfiguration
{
  /**
   * @see sfPluginConfiguration
   */
  public function initialize()
  {
    $this->dispatcher->connect('component.method_not_found', array('ckComponentEventListener', 'listenToComponentMethodNotFoundEvent'));

    spl_autoload_register(array(new ckGenericObjectAdapterFactory(sfConfig::get('sf_cache_dir')), 'autoload'));

    ckObjectWrapper::addObjectWrapper(new ckDefaultObjectWrapper());
    ckObjectWrapper::addObjectWrapper(new ckGenericObjectAdapterWrapper());
    ckObjectWrapper::addObjectWrapper(new ckArrayObjectWrapper());
    ckObjectWrapper::addObjectWrapper(new ckDoctrineRecordWrapper());

  }
}
