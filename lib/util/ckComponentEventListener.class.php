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
 * ckComponentEventListener handles sfComponent's method_not_found event.
 *
 * @package    ckWebServicePlugin
 * @subpackage util
 * @author     Christian Kerl <christian-kerl@web.de>
 */
class ckComponentEventListener
{
  /**
   * Listens to the component.method_not_found event.
   *
   * @param sfEvent $event An sfEvent instance
   *
   * @return bool True, if the method is the 'isSoapRequest', false otherwise
   */
  public static function listenToComponentMethodNotFoundEvent(sfEvent $event)
  {
    if($event['method'] == 'isSoapRequest')
    {
      $event->setReturnValue(self::isSoapRequest());
      return true;
    }
    else
    {
      return false;
    }
  }

  /**
   * Determine whether the current request is a SOAP request.
   *
   * @return boolean True if the current request is a SOAP request, false otherwise
   */
  public static function isSoapRequest()
  {
    $controller = sfContext::getInstance()->getController();
    return $controller instanceof ckWebServiceController;
  }
}