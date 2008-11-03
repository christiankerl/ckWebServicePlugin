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
 * ckSoapHandler is the standard handler for webservice requests.
 *
 * @package    ckWebServicePlugin
 * @subpackage controller
 * @author     Christian Kerl <christian-kerl@web.de>
 */
class ckSoapHandler
{
  const HEADER_PREFIX = 'Element';

  /**
   * The current context.
   *
   * @var sfContext
   */
  protected $context;

  /**
   * Standard constructor.
   *
   */
  public function __construct()
  {
    $this->context = sfContext::getInstance();
  }

  /**
   * Gets an array of soap header names from the configuration.
   *
   * @return array An array of soap header names
   */
  protected function getSoapHeaders()
  {
    return sfConfig::get('app_ck_web_service_plugin_soap_headers', array());
  }

  /**
   * Checks if the given name identifies a soap header.
   *
   * @param string $name A name
   *
   * @return boolean True, if the given name identifies a soap header, false otherwise
   */
  protected function isSoapHeader($name)
  {
    return array_key_exists($this->getSoapHeaderName($name), $this->getSoapHeaders());
  }

  /**
   * Removes the header prefix from a given string, if it is present.
   *
   * @param string $name A name
   *
   * @return string The name without the header prefix
   *
   * @see HEADER_PREFIX
   */
  protected function getSoapHeaderName($name)
  {
    $result = $str;

    if(ckString::endsWith($result, self::HEADER_PREFIX))
    {
      $result = substr($result, 0, - strlen(self::HEADER_PREFIX));
    }

    return $result;
  }

  /**
   * Dispatches the 'webservice.handle_header' event.
   *
   * @param string $name A soap header name
   * @param mixed  $data A soap header data object
   *
   * @return mixed The processed header data object
   */
  protected function dispatchSoapHeaderEvent($name, $data)
  {
    $event = $this->context->getEventDispatcher()->notifyUntil(new sfEvent($this, 'webservice.handle_header', array('header' => $name, 'data' => $data)));

    if(!$event->isProcessed())
    {
      $this->log(sprintf("SoapHeader '%s' unhandled.", $header_name));
    }

    return !is_null($event->getReturnValue()) ? $event->getReturnValue() : $data;
  }

  /**
   * Redirects webservice calls back to the ckWebServiceController.
   *
   * @param string $method    The method name
   * @param array  $arguments The method arguments
   *
   * @return mixed The result of the called method
   */
  public function __call($method, $arguments)
  {
    if($this->isSoapHeader($method))
    {
      return $this->dispatchSoapHeaderEvent($this->getSoapHeaderName($method), $arguments[0]);
    }
    else
    {
      $parts = explode('_', $method, 2);

      $module = $parts[0];
      $action = isset($parts[1]) && strlen($parts[1]) > 0 ? $parts[1] : 'index';

      return $this->context->getController()->invokeSoapEnabledAction($module, $action, $arguments);
    }
  }

  /**
   * Writes a given message to the log after inserting the class name at the beginning.
   *
   * @param string $message A message
   */
  protected function log($message)
  {
    if (sfConfig::get('sf_logging_enabled'))
    {
      $this->context->getEventDispatcher->notify(new sfEvent($this, 'application.log', $message));
    }
  }
}