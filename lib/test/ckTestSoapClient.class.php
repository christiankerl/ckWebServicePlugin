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
 * ckTestSoapClient provides methods to test a local symfony webservice application.
 *
 * @package    ckWebServicePlugin
 * @subpackage test
 * @author     Christian Kerl <christian-kerl@web.de>
 */
class ckTestSoapClient extends SoapClient
{
  /**
   * A sfTestBrowser to dispatch requests to the tested application.
   *
   * @var sfTestBrowser
   */
  protected $browser;

  /**
   * The target namespace of the xsd types, especially of the soap header types.
   *
   * @var string
   */
  protected $namespace;

  /**
   * The last raw soap request string.
   *
   * @var string
   */
  protected $lastRequest;

  /**
   * The last raw soap response string.
   *
   * @var string
   */
  protected $lastResponse;

  /**
   * The result of the last soap request.
   *
   * @var mixed
   */
  protected $lastResult;

  /**
   * The soap headers for the next request.
   *
   * @var array
   */
  protected $requestHeaders = array();

  /**
   * The soap headers of the last response.
   *
   * @var array
   */
  protected $responseHeaders = array();

  /**
   * Constructor initializing the client with given soap options.
   *
   * @param array $options An array of soap options
   */
  public function __construct($options = array())
  {
    $wsdl = sfConfig::get('app_ck_web_service_plugin_wsdl');
    $this->browser = new sfTestBrowser();
    $this->namespace = $this->getNamespaceFromWsdl($wsdl);

    parent::__construct($wsdl, $this->getOptions($options));
  }

  /**
   * @see sfTestBrowser::test()
   *
   * @return lime_test
   */
  public function test()
  {
    return $this->browser->test();
  }

  /**
   * Gets the result of the last soap request.
   *
   * @return mixed The result of the last soap request
   */
  public function getResult()
  {
    return $this->lastResult;
  }

  /**
   * Adds required settings to a given soap option array.
   *
   * @param array $presets An array of soap option presets
   *
   * @return array A modified soap option preset array
   */
  public function getOptions($presets = array())
  {
    $presets['trace'] = 1;

    return $presets;
  }

  /**
   * Adds a soap header for the next request.
   *
   * @param string $header The name of the soap header
   * @param mixed  $data   The data of the soap header
   *
   * @return ckTestSoapClient
   */
  public function addRequestHeader($header, $data)
  {
    $this->requestHeaders[] = new SoapHeader($this->namespace, $header, $data);

    return $this;
  }

  /**
   * Gets the soap headers for the next request.
   *
   * @return array An array with soap headers for the next request
   */
  public function getRequestHeaders()
  {
    return $this->requestHeaders;
  }

  /**
   * Gets the soap headers of the last response.
   *
   * @return array An array with soap headers of the last response
   */
  public function getResponseHeaders()
  {
    return $this->responseHeaders;
  }

  /**
   * Sets the target namespace of the xsd types, especially the soap header types.
   *
   * @param string $namespace
   *
   * @return ckTestSoapClient
   */
  public function setNamespace($namespace)
  {
    $this->namespace = $namespace;

    return $this;
  }

  /**
   * Dispatches a soap request via a sfTestBrowser to the tested application.
   *
   * @see SoapClient::__doRequest()
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
   * @see SoapClient::__getLastRequest()
   *
   * @return string
   */
  public function __getLastRequest()
  {
    return $this->lastRequest;
  }

  /**
   * @see SoapClient::__getLastResponse()
   *
   * @return string
   */
  public function __getLastResponse()
  {
    return $this->lastResponse;
  }

  /**
   * Calls a given soap action with the given parameters.
   *
   * @param string $method     The soap action name
   * @param array  $parameters The parameters
   *
   * @return ckTestSoapClient
   */
  public function __call($method, $parameters)
  {
    $this->lastResult      = null;
    $this->lastSoapFault   = null;
    $this->responseHeaders = array();

    try
    {
      $this->lastResult = parent::__soapCall($method, $parameters, array(), $this->getRequestHeaders(), $this->responseHeaders);
    }
    catch(SoapFault $f)
    {
      $this->lastSoapFault = $f;
    }

    $this->requestHeaders = array();

    return $this;
  }

  /**
   * Tests the result or a child object identified by the given selector against a given value.
   *
   * @param string $selector A result property/index selector, separate selectors for nested properties/indexes with a '.', if the selector is empty the result object is used
   * @param mixed  $value    A value to test against
   *
   * @return ckTestSoapClient
   */
  public function is($selector, $value)
  {
    return $this->isObject($this->getResult(), $selector, 'value', $value);
  }

  /**
   * Tests the type of the result or a child object identified by the given selector against a given type.
   *
   * @param string $selector A result property/index selector, separate selectors for nested properties/indexes with a '.', if the selector is empty the result object is used
   * @param string $type     A type to test against
   *
   * @return ckTestSoapClient
   */
  public function isType($selector, $type)
  {
    return $this->isObject($this->getResult(), $selector, 'type', $type);
  }

  /**
   * Tests the element count of the result or a child object identified by the given selector against a given count.
   *
   * @param string $selector A result property/index selector, separate selectors for nested properties/indexes with a '.', if the selector is empty the result object is used
   * @param int    $count    A count to test against
   *
   * @return ckTestSoapClient
   */
  public function isCount($selector, $count)
  {
    return $this->isObject($this->getResult(), $selector, 'count', $count);
  }

  /**
   * Tests a soap header or a child object identified by the given selector against a given value.
   *
   * @param string $selector A soap header selector, separate selectors for nested properties/indexes with a '.'
   * @param mixed  $value    A value to test against
   *
   * @return ckTestSoapClient
   */
  public function isHeader($selector, $value)
  {
    return $this->isObject($this->getResponseHeaders(), $selector, 'value', $value);
  }

  /**
   * Tests the type of a soap header or a child object identified by the given selector against a given value.
   *
   * @param string $selector A soap header selector, separate selectors for nested properties/indexes with a '.'
   * @param string $type     A type to test against
   *
   * @return ckTestSoapClient
   */
  public function isHeaderType($selector, $type)
  {
    return $this->isObject($this->getResponseHeaders(), $selector, 'type', $type);
  }

  /**
   * Tests the element count of a soap header or a child object identified by the given selector against a given count.
   *
   * @param string $selector A soap header selector, separate selectors for nested properties/indexes with a '.'
   * @param int    $count    A count to test against
   *
   * @return ckTestSoapClient
   */
  public function isHeaderCount($selector, $count)
  {
    return $this->isObject($this->getResponseHeaders(), $selector, 'count', $count);
  }

  /**
   * Tests a given object or a child object identified by a given selector with a given test type against a given value.
   *
   * @param mixed  $object   The object to test
   * @param string $selector The child object selector
   * @param string $type     The test type, one of 'value', 'type' or 'count'
   * @param mixed  $value    The value to test against
   */
  protected function isObject($object, $selector, $type, $value)
  {
    $type = in_array($type, array('value', 'type', 'count')) ? $type : 'value';

    foreach(explode('.', $selector) as $index)
    {
      $object = $this->getChildObject($object, $index);
    }

    switch($type)
    {
      case 'value':
        $this->test()->is($object, $value, sprintf('response object \'%s\' is \'%s\'', $selector, $value));
        break;
      case 'type':
        $this->test()->isa_ok($object, $value, sprintf('response object \'%s\' is a \'%s\'', $selector, $value));
        break;
      case 'count':
        $this->test()->is(count($object), $value, sprintf('response object \'%s\' contains \'%s\' elements', $selector, $value));
        break;
      default:
        $this->test()->fail('unknown check type');
        break;
    }

    return $this;
  }

  /**
   * Trys to get a child object of a given object indentified by a given index.
   * If the given object is an array, the index is assumed to be a key.
   * If the given object is an object, the index is assumed to be a property.
   * If the given index is an empty string the given object is returned.
   *
   * @param mixed $object An object or array to get a child object from
   * @param mixed $index  An identifier for the child object
   *
   * @return mixed The child object or the given object if the index is an empty string or null if nothing is found
   */
  protected function getChildObject($object, $index)
  {
    if(is_array($object) && array_key_exists($index, $object))
    {
      return $object[$index];
    }
    else if(is_object($object) && property_exists($object, $index))
    {
      return $object->$index;
    }
    else if(strlen($index) == 0)
    {
      return $object;
    }
    else
    {
      return null;
    }
  }

  /**
   * Trys to get the target namespace of the xsd types from the schema definition of a given wsdl file, if it fails 'http://localhost/' is returned.
   *
   * @param string $wsdl A wsdl file
   *
   * @return string The target namespace of the xsd types
   */
  protected function getNamespaceFromWsdl($wsdl)
  {
    $xpath = new DOMXPath(DOMDocument::load($wsdl));
    $list = $xpath->query('/wsdl:definitions/wsdl:types/xsd:schema');
    return $list->length > 0 && $list->item(0)->hasAttribute('targetNamespace') ? $list->item(0)->getAttribute('targetNamespace') : 'http://localhost/';
  }
}