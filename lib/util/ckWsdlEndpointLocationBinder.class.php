<?php
/**
 * This file is part of the ckWebServicePlugin
 *
 * @package   ckWebServicePlugin
 * @author    Christian Kerl <christian-kerl@web.de>
 * @copyright Copyright (c) 2010, Christian Kerl
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 * @version   SVN: $Id: functional.php 20950 2009-08-08 20:45:00Z chrisk $
 */

/**
 * ckWsdlEndpointLocationBinder allows to modify the endpoint location in a wsdl file.
 *
 * Based on code from: http://tohenk.wordpress.com/2009/09/17/dynamic-web-service-using-ckwebserviceplugin/
 *
 * @package    ckWebServicePlugin
 * @subpackage util
 * @author     Christian Kerl <christian-kerl@web.de>
 */
class ckWsdlEndpointLocationBinder
{
  private $wsdl;
  private $dom;

  /**
   * Initializes the ckWsdlEndpointLocationBinder with the given wsdl file.
   *
   * @param string $wsdl A wsdl file path
   */
  public function __construct($wsdl)
  {
    $this->wsdl = $wsdl;
  }

  /**
   * Sets the wsdl:port > soap:address location attribute to the given
   * endpoint location.
   *
   * @param string $endpoint The endpoint location
   */
  public function bind($endpoint)
  {
    $this->dom = new DOMDocument();
    $this->dom->load($this->wsdl);

    // query the soap:address node to change location
    $xpath = new DOMXPath($this->dom);
    $nodes = $xpath->query('/wsdl:definitions/wsdl:service/wsdl:port/soap:address');

    foreach ($nodes as $node)
    {
      if ($node->hasAttribute('location'))
      {
        $node->setAttribute('location', $endpoint);
      }
    }
  }

  /**
   * Serializes the updated wsdl file. This method has to be called after
   * ckWsdlEndpointLocationBinder::bind()!
   *
   * @return string The serialized xml string
   */
  public function getXml()
  {
    if(is_null($this->dom))
    {
      throw new LogicException('ckWsdlEndpointLocationBinder::bind() has to be called first!');
    }

    return $this->dom->saveXML();
  }
}