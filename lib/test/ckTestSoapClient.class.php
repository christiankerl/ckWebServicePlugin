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
   * The lime_test shared amongst all instances of ckTestSoapClient.
   *
   * @var lime_test
   */
  protected static $test;

  /**
   * A sfBrowser to dispatch requests to the tested application.
   *
   * @var sfBrowser
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
   * The soap fault of the last request.
   *
   * @var SoapFault
   */
  protected $lastSoapFault;

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
    $this->browser = new sfBrowser();
    $this->namespace = $this->getNamespaceFromWsdl($wsdl);

    parent::__construct($wsdl, $this->getOptions($options));
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
   * Gets the lime_test shared amongst all instances of ckTestSoapClient.
   *
   * @return lime_test A lime_test instance
   */
  public function test()
  {
    if(is_null(self::$test))
    {
      self::$test = new lime_test(null, new lime_output_color());
    }

    return self::$test;
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
   * Gets the soap fault of the last response.
   *
   * @return SoapFault The last soap fault
   */
  public function getFault()
  {
    return $this->lastSoapFault;
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
   * Dispatches a soap request via a sfTestBrowser to the tested application.
   *
   * @see SoapClient::__doRequest()
   */
  public function __doRequest($request, $location, $action, $version)
  {
    $this->lastRequest = strval($request);

    $GLOBALS['HTTP_RAW_POST_DATA'] = $this->lastRequest;

    $this->browser->setHttpHeader('soapaction', strval($action));

    sfFilter::$filterCalled = array();

    $this->lastResponse = strval($this->browser->post('/')->getResponse()->getContent());

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

    $this->test()->diag(sprintf('%s(%s)', $method, !empty($parameters) ? '...' : ''));

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
   *
   * @return ckTestSoapClient
   */
  protected function isObject($object, $selector, $type, $value)
  {
    $type = in_array($type, array('value', 'type', 'count')) ? $type : 'value';

    foreach(explode('.', $selector) as $index)
    {
      $object = $this->getChildObject($object, $index);
    }

    $selector = !strlen($selector) ? '.' : $selector;

    switch($type)
    {
      case 'value':
        $this->test()->is($object, $value, sprintf('response object "%s" is "%s"', $selector, $value));
        break;
      case 'type':
        $this->test()->isa_ok($object, $value, sprintf('response object "%s" is a "%s"', $selector, $value));
        break;
      case 'count':
        $this->test()->is(count($object), $value, sprintf('response object "%s" contains "%s" elements', $selector, $value));
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
    if(strlen($index) == 0)
    {
      return $object;
    }
    else if(is_array($object) || $object instanceof ArrayAccess)
    {
      return $object[$index];
    }
    else if(is_object($object))
    {
      $refClass = new ReflectionClass($object);

      if(($property = $refClass->getProperty($index)) && $property->isPublic())
      {
        return $property->getValue($object);
      }
      else if(($method = $refClass->getMethod('get'.ucfirst($index))) && $method->isPublic())
      {
        return $method->invoke($object);
      }
      else
      {
        return $object->$index;
      }
    }
    else
    {
      return null;
    }
  }

  /**
   * Tests if the last response contained no soap fault.
   *
   * @return ckTestSoapClient
   */
  public function isFaultEmpty()
  {
    if(!is_null($this->getFault()))
    {
      $this->test()->fail('response contains a soap fault');
    }

    return $this;
  }

  /**
   * Tests if the last response contained a soap fault, if a message is given the soap fault message is tested against it.
   *
   * @param string $message A message to test against
   *
   * @return ckTestSoapClient
   */
  public function hasFault($message = null)
  {
    $f = $this->getFault();

    if($f === null)
    {
      $this->test()->fail('response contains no soap fault');
    }
    else
    {
      // START copy from sfTestBrowser.class.php
      if (null !== $message && preg_match('/^(!)?([^a-zA-Z0-9\\\\]).+?\\2[ims]?$/', $message, $match))
      {
        if ($match[1] == '!')
        {
          $this->test()->unlike($f->getMessage(), substr($message, 1), sprintf('response soap fault message does not match regex "%s"', $message));
        }
        else
        {
          $this->test()->like($f->getMessage(), $message, sprintf('response soap fault message matches regex "%s"', $message));
        }
      }
      else if (null !== $message)
      {
        $this->test()->is($f->getMessage(), $message, sprintf('response soap fault message is "%s"', $message));
      }
      // END copy from sfTestBrowser.class.php
    }

    return $this;
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