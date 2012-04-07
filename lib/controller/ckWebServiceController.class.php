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

  protected $resultAdapter = null;

  /**
   * Initializes the controller.
   *
   * @param sfContext $context A sfContext instance
   */
  public function initialize($context)
  {
    parent::initialize($context);
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

  /**
   * Gets the result adapter for the current action, if no instance exists, one is created.
   *
   * @return ckAbstractResultAdapter A result adapter for the current action
   */
  public function getResultAdapter()
  {
    if(is_null($this->resultAdapter))
    {
      $result = sfConfig::get(sprintf('mod_%s_%s_result', strtolower($this->context->getModuleName()), $this->context->getActionName()), array());
      $class  = isset($result['class']) ? $result['class'] : 'ckPropertyResultAdapter';
      $param  = isset($result['param']) ? $result['param'] : array();

      $adapter = new $class($param);

      $this->resultAdapter = $adapter instanceof ckAbstractResultAdapter ? $adapter : new ckPropertyResultAdapter();
    }

    return $this->resultAdapter;
  }

  /**
   * Gets the raw soap request, which was send to the server either from '$HTTP_RAW_POST_DATA' or from 'php://input'.
   *
   * @return string
   */
  protected function getSoapRequest()
  {
    return isset($GLOBALS['HTTP_RAW_POST_DATA']) ? $GLOBALS['HTTP_RAW_POST_DATA'] : file_get_contents('php://input');
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

    $soap_headers = sfConfig::get('app_ck_web_service_plugin_soap_headers', array());

    if(!isset($options['classmap']) || !is_array($options['classmap']))
    {
      $options['classmap'] = array();
    }

    foreach($soap_headers as $header_name => $header_options)
    {
      if(isset($header_options['class']))
      {
        $options['classmap'][$header_name] = $header_options['class'];
      }
    }

    if (sfConfig::get('sf_logging_enabled'))
    {
       $this->context->getEventDispatcher()->notify(new sfEvent($this, 'application.log', array(sprintf('Starting SoapServer with "%s" and handler "%s".', $wsdl, $handler))));
    }

    // construct the server
    $this->soap_server = new SoapServer($wsdl, $options);

    // set the handler
    $this->soap_server->setClass($handler);

    // set the persistence mode
    $this->soap_server->setPersistence($persist);

    // start the server
    $this->soap_server->handle($this->getSoapRequest());
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

    $request->setRequestFormat('soap');
    $request->setParameter(ckSoapParameterFilter::PARAMETER_KEY, $parameters);

    try
    {
      if (sfConfig::get('sf_logging_enabled'))
      {
         $this->dispatcher->notify(new sfEvent($this, 'application.log', array(sprintf('Forwarding to "%s/%s".', $moduleName, $actionName))));
      }

      try
      {
        // use forward to invoke the action, so we have to pass the filter chain
        $this->forward($moduleName, $actionName);
      }
      catch(sfStopException $e)
      {
      }

      // get the last executed action
      $actionInstance = $this->getActionStack()->getLastEntry()->getActionInstance();

      // if we have been redirected to the 404 module, we raise an exception
      if($actionInstance->getModuleName() == sfConfig::get('sf_error_404_module') && $actionInstance->getActionName() == sfConfig::get('sf_error_404_action'))
      {
        throw new sfError404Exception(sprintf('{%s} SoapFunction "%s_%s" not found.', __CLASS__, $moduleName, $actionName));
      }

      return $this->getResultAdapter()->getResult($actionInstance);
    }
    catch(SoapFault $e)
    {
      // soap fault with custom fault codes thrown in the actions will be left untouched
      throw $e;
    }
    catch(Exception $e)
    {
      // we return all other exceptions as soap faults to the remote caller
      throw $this->getSoapFaultFromException($e);
    }
  }

  protected function getSoapFaultFromException(Exception $exception)
  {
    if(sfConfig::get('sf_debug'))
    {
      return new SoapFault('Server', $exception->getMessage(), get_class($exception), $exception->getTraceAsString());
    }
    else
    {
      return new SoapFault('Server', 'Internal Server Error');
    }
  }
}
