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
 * ckTestSoapClient
 *
 * @package    ckWebServicePlugin
 * @subpackage test
 * @author     Christian Kerl <christian-kerl@web.de>
 */
class ckTestSoapClient extends SoapClient
{
  /**
   * Enter description here...
   *
   * @var sfBrowser
   */
  protected $browser;

  /**
   * Enter description here...
   *
   * @var string
   */
  protected $lastRequest;

  /**
   * Enter description here...
   *
   * @var string
   */
  protected $lastResponse;

  /**
   * Enter description here...
   *
   * @var mixed
   */
  protected $lastResult;

  /**
   * Enter description here...
   *
   * @var array
   */
  protected $requestHeaders = array();

  /**
   * Enter description here...
   *
   * @var array
   */
  protected $responseHeaders = array();

  /**
   * Enter description here...
   *
   * @param sfBrowser $browser
   * @param array     $options
   */
  public function __construct(sfBrowser $browser, $options = array())
  {
    parent::__construct(sfConfig::get('app_ck_web_service_plugin_wsdl'), $this->getOptions($options));
    $this->browser = $browser;
  }

  /**
   * Enter description here...
   *
   * @return mixed
   */
  public function getResult()
  {
    return $this->lastResult;
  }

  /**
   * Enter description here...
   *
   * @param array $presets
   *
   * @return array
   */
  public function getOptions($presets = array())
  {
    $presets['trace'] = 1;

    return $presets;
  }

  /**
   * Enter description here...
   *
   * @return array
   */
  public function getRequestHeaders()
  {
    return $this->requestHeaders;
  }

  /**
   * Enter description here...
   *
   * @return array
   */
  public function getResponseHeaders()
  {
    return $this->responseHeaders;
  }

  /**
   * Enter description here...
   *
   * @param string $request
   * @param string $location
   * @param string $action
   * @param string $version
   *
   * @return mixed
   */
  public function __doRequest($request, $location, $action, $version)
  {
    $this->lastRequest = $request;

    $GLOBALS['HTTP_RAW_POST_DATA'] = $this->lastRequest;

    $this->browser->setHttpHeader('soapaction', strval($action));

    $this->lastResponse = $this->browser->post('/')->getResponse()->getContent();

    return $this->lastResponse;
  }

  /**
   * Enter description here...
   *
   * @return string
   */
  public function __getLastRequest()
  {
    return $this->lastRequest;
  }

  /**
   * Enter description here...
   *
   * @return string
   */
  public function __getLastResponse()
  {
    return $this->lastResponse;
  }

  /**
   * Enter description here...
   *
   * @param string $method
   * @param array  $parameters
   *
   * @return ckTestSoapClient
   */
  public function __call($method, $parameters)
  {
    $this->lastResult    = null;
    $this->lastSoapFault = null;

    try
    {
      $this->lastResult = parent::__soapCall($method, $parameters, array(), $this->getRequestHeaders(), $this->responseHeaders);
    }
    catch(SoapFault $f)
    {
      $this->lastSoapFault = $f;
    }

    return $this;
  }
}