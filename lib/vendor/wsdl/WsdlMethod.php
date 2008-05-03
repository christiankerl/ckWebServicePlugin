<?php
/**
 * @package  wsdl.writer
 *
 * @version   $$
 * @author    David Giffin <david@giffin.org>
 * @since     PHP 5.0.2
 * @copyright Copyright (c) 2000-2005 David Giffin : LGPL - See LICENCE
 *
 */

require_once("WsdlPart.php");

/**
 * WSDL Generator for PHP5
 *
 * @package   wsdl.writer
 * @author    David Giffin <david@giffin.org>
 *
 */
class WsdlMethod
{

    private $name        = null;
    private $desc        = null;
    private $header      = false;
    private $reqHeaders  = array();

    private $params      = array();
    private $returnType  = null;
    private $returnDesc  = null;

    /**
     * Set the Name of the Method
     *
     * @param string $name The Name of the Method
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get the Name of the Method
     *
     * @return string The Name of the Method
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the Description of the Method
     *
     * @param string $desc The description
     */
    public function setDesc($desc)
    {
        $this->desc = $desc;
    }

    /**
     * Set whether the Method represents a SOAP Body or Header element
     *
     * @param boolean $usage True for header, False for body
     */
    public function setIsHeader($header)
    {
        $this->header = $header;
    }
    
    /**
     * Get whether the Method represents a SOAP Body or Header element
     *
     * @return boolean True for header, False for body
     */
    public function getIsHeader()
    {
        return $this->header;
    }
    
    /**
     * Set the required SOAP headers for this method
     *
     * The headers list is supplied as strings which are later resolved into
     * WsdlMethod references
     *
     * @param array $headers String array of required header names
     */
    public function setRequiredHeaders($headers)
    {
        $this->reqHeaders = $headers;
    }
    
    /**
     * Resolve required SOAP header symbols into WsdlMethod references
     *
     * Should only be called once per method or an error will occur
     * 
     * @param array $wsdlMethods Available WSDL methods to use for resolution
     */
    public function resolveHeaders($wsdlMethods)
    {
        $reqHeaders = $this->reqHeaders;
        
        $this->reqHeaders = array();
        
        foreach ($reqHeaders as $reqHeader)
            foreach ($wsdlMethods as &$wsdlMethod)
                if ($wsdlMethod->getName() == $reqHeader)
                    $this->reqHeaders[] = &$wsdlMethod;
    }
    
    /**
     * Add a Parameter to the Method
     *
     * @param string $varType The PHP type
     * @param string $varName The Name of the Parameter
     * @param string $varDesc The Description of the Paramater
     */
    public function addParameter($varType, $varName, $varDesc)
    {
        $param = new StdClass();
        
        $param->type = $varType;
        $param->name = $varName;
        $param->desc = $varDesc;
        
        $this->params[] = $param;    
    }

    /**
     * Set the Return for this Method
     *
     * @param string $varType The PHP Variable Type
     * @param string $varDesc The Variable Description
     */
    public function setReturn($varType, $varDesc)
    {
        $this->returnType = $varType;
        $this->returnDesc = $varDesc;
    }
    

    /**
     * Get the WSDL Request Message Information
     *
     * @return object The Request Message Data 
     */
    public function getMessageRequest()
    {
        $messageName = ucfirst($this->name) . "Request";
        $message = new StdClass();
        $message->name  = $messageName;
        $message->parts = array();
        
        // Header parts
        foreach ($this->reqHeaders as &$header) {
            $headerMessage = $header->getMessageRequest();
            $message->parts = array_merge($message->parts, $headerMessage->parts);
        }
        
        // Body parts
        foreach ($this->params as &$param) {
            $part = new WsdlPart();
            $part->setName(($this->header)? $param->type : $param->name);
            $part->setType($param->type);
            $part->setIsHeader($this->header);
            
            $message->parts[] = $part;
        }
        return $message;
    }

    /**
     * Get the WSDL Response Message Information
     *
     * @return object The Response Message Data 
     */
    public function getMessageResponse()
    {
        $messageName = ucfirst($this->name) . "Response";
        $message = new StdClass();
        $message->name  = $messageName;
        $message->parts = array();
        
        // Header parts
        foreach ($this->reqHeaders as &$header) {
            $headerMessage = $header->getMessageResponse();
            if ($headerMessage != null)
                $message->parts = array_merge($message->parts, $headerMessage->parts);
        }
        
        // Body part
        if ($this->returnType) {
            $part = new WsdlPart();
            $part->setName(($this->header)? $this->returnType : "return");
            $part->setType($this->returnType);
            $part->setIsHeader($this->header);
    
            $message->parts[] = $part;
        }
        
        return (count($message->parts) > 0)? $message : null;
    }


    /**
     * Get the WSDL Operation Information
     *
     * @return object The Operation Information
     */
    public function getOperation()
    {
        $order     = "";
        $operation = new StdClass();
        
        $operation->name          = $this->name;
        $operation->inputMessage  = null;
        $operation->outputMessage = null;

        $delim = "";
        foreach ($this->params as &$param) {
            $order .= $delim . $param->name;
            $delim  = " ";
        }

        // Input Message
        $request  = $this->getMessageRequest();
        if ($request) {
            $operation->inputMessage = $request;
        }
        
        // Output Message
        $response = $this->getMessageResponse();
        if ($response) {
            $operation->outputMessage = $response;
        }
        
        // Set the Parameter Order
        $operation->parameterOrder = $order;
        
        return $operation;
    }
}

?>
