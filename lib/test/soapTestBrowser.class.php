<?php
/**
 * This file is part of the ckWebServicePlugin
 *
 * @package    ckWebServicePlugin
 * @subpackage test
 * @author     Nicolas Martin <email.de.nicolas.martin@gmail.com>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT License
 * @version    SVN: $Id$
 */

/**
 * React like a standard SoapClient object, but allows introspection of the current symfony application
 *
 * @package    ckWebServicePlugin
 * @subpackage test
 * @author     Nicolas Martin <email.de.nicolas.martin@gmail.com>
 */
class testSoapBrowser extends sfTestBrowser
{
  protected  
    $test_soap_server,
    $test_soap_client;

  public function load(sfContext $context)
  {
    $route_parameters_array = $context->getRouting()->parse($this->uri);

    $context->getRequest()->getParameterHolder()->add($route_parameters_array);

    /* This line is still specific to my application, This method simply set
     * the 'app_ck_web_service_plugin_wsdl' config setting on-the-fly according
     * to the 'module' request parameter. It shall be removed */ 
    $context->getController()->initializeWsdl($context->getRequest());

    $server = new SoapServer(sfConfig::get('app_ck_web_service_plugin_wsdl'), array());
    $server->setClass(sfConfig::get('app_ck_web_service_plugin_handler', 'ckSoapHandler'));

    $this->test_soap_client = new testSoapClient(sfConfig::get('app_ck_web_service_plugin_wsdl'), array());
    $this->test_soap_client->setSoapServer($server);
  }

  public function __construct($uri, $options)
  {
    parent::__construct();

    $this->uri = $uri;

    $this->load($this->getContext(true));
  }

  public function __call($method, $arguments)
  {
    $this->load($this->getContext());

    return $this->test_soap_client->__soapCall($method, $arguments) ;
  }

  /**
   * No way to incercept theses methods via __call() so I had to wrap them manually
   */
  public function __getFunctions()
  {
    return $this->test_soap_client->__getFunctions();
  }

  public function __getLastResponse()
  {
    return $this->test_soap_client->__getLastResponse();
  }
}
