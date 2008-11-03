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
 * ckWebServiceController is the endpoint for the webservice interface of your web application.
 *
 * @package    ckWebServicePlugin
 * @subpackage controller
 * @author     Christian Kerl <christian-kerl@web.de>
 */
class ckWebServiceController extends sfWebController
{
  protected $soap_server = null;

  protected $soap_headers = array();

  protected $isFirstForward = false;

  protected $resultAdapter = null;

  /**
   * Initializes this controller.
   *
   * @param sfContext $context A sfContext implementation instance
   */
  public function initialize($context)
  {
    parent::initialize($context);

    $this->dispatcher->connect('controller.change_action', array($this, 'listenToControllerChangeActionEvent'));
  }

  /**
   * Retrieves the render mode depending on the result adapter of the current module.
   *
   * @return int A render mode
   */
  public function getRenderMode()
  {
    return $this->getResultAdapter()->getRenderMode();
  }

  public function getResultAdapter()
  {
    if(is_null($this->resultAdapter))
    {
      $result = sfConfig::get(sprintf('mod_%s_%s_result', $this->context->getModuleName(), $this->context->getActionName()), array());
      $class  = isset($result['class']) ? $result['class'] : 'ckMemberResultAdapter';
      $param  = isset($result['param']) ? $result['param'] : array();

      $adapter = new $class($param);

      $this->resultAdapter = $adapter instanceof ckAbstractResultAdapter ? $adapter : new ckMemberResultAdapter();
    }

    return $this->resultAdapter;
  }

  /**
   * Wrapper method around ckWebServiceController::handle() for compatibility with standard controller generator.
   *
   * @see function handle
   */
  public function dispatch()
  {
    $this->handle();
  }

  /**
   * Hides sfWebController::redirect() because redirect is not supported.
   *
   * @see sfWebController
   */
  public function redirect($url, $delay = 0, $statusCode = 302)
  {

  }

  /**
   * Starts the soap server to handle webservice requests.
   *
   */
  public function handle()
  {
    // retrieve configuration
    $wsdl = sfConfig::get('app_ck_web_service_plugin_wsdl');

    $options = sfConfig::get('app_ck_web_service_plugin_soap_options', array());
    $handler = sfConfig::get('app_ck_web_service_plugin_handler', 'ckSoapHandler');
    $persist = sfConfig::get('app_ck_web_service_plugin_persist', SOAP_PERSISTENCE_SESSION);

    $this->soap_headers = sfConfig::get('app_ck_web_service_plugin_soap_headers', array());

    if(!isset($options['classmap']) || !is_array($options['classmap']))
    {
      $options['classmap'] = array();
    }

    foreach($this->soap_headers as $header_name => $header_options)
    {
      if(isset($header_options['class']))
      {
        $options['classmap'][$header_name] = $header_options['class'];
      }
    }

    if (sfConfig::get('sf_logging_enabled'))
    {
      $this->context->getLogger()->info(sprintf('{%s} Starting SoapServer with \'%s\' and Handler \'%s\'.', __CLASS__, $wsdl, $handler));
    }

    // construct the server
    $this->soap_server = new SoapServer($wsdl, $options);

    // set the handler
    $this->soap_server->setClass($handler);

    // set the persistence mode
    $this->soap_server->setPersistence($persist);

    // start the server
    $this->soap_server->handle();
  }

  /**
   * Invokes an action requested by a webservice call and returns the action result.
   *
   * @param string $moduleName A module name
   * @param string $actionName An action name
   * @param array  $parameters The paramters to pass to the action
   *
   * @throws SoapFault thrown if any exception is thrown during the action call.
   *
   * @return mixed The result of the called action
   */
  public function invokeSoapEnabledAction($moduleName, $actionName, $parameters)
  {
    $moduleName = ckString::lcfirst($moduleName);
    $actionName = ckString::lcfirst($actionName);

    $request = $this->context->getRequest();

    $request->setParameter('ck_web_service_plugin.param', $parameters);

    try
    {
      if (sfConfig::get('sf_logging_enabled'))
      {
        $this->context->getLogger()->info(sprintf('{%s} Forwarding to \'%s/%s\'.', __CLASS__, $moduleName, $actionName));
      }

      $this->isFirstForward = true;

      // use forward to invoke the action, so we have to pass the filter chain
      $this->forward($moduleName, $actionName);

      // get the last executed action
      $actionInstance = $this->getActionStack()->getLastEntry()->getActionInstance();

      // if we have been redirected to the 404 module, we raise an exception
      if($actionInstance->getModuleName() == sfConfig::get('sf_error_404_module') && $actionInstance->getActionName() == sfConfig::get('sf_error_404_action'))
      {
        throw new sfError404Exception(sprintf('{%s} SoapFunction \'%s_%s\' not found.', __CLASS__, $moduleName, $actionName));
      }

      return $this->getResultAdapter()->getResult($actionInstance);
    }
    catch(Exception $e)
    {
      // we return all exceptions as soap faults to the remote caller
      throw new SoapFault('1', $e->getMessage(), '', $e->getTraceAsString());
    }
  }

  /**
   * Listens to the controller.change_action event.
   *
   * @param sfEvent $event An sfEvent instance
   */
  public function listenToControllerChangeActionEvent(sfEvent $event)
  {
    if($event->getSubject() === $this && $this->isFirstForward)
    {
      $this->isFirstForward = false;

      if(!sfConfig::get(sprintf('mod_%s_%s_enable', $event['module'], $event['action']), false))
      {
        throw new sfError404Exception(sprintf('{%s} SoapFunction \'%s_%s\' not found.', __CLASS__, $event['module'], $event['action']));
      }
    }
  }
}