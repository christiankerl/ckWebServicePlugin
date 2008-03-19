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


/**
 * WSDL Generator for PHP5
 *
 * @package   wsdl.writer
 * @author    David Giffin <david@giffin.org>
 *
 */
class BaseWsdlWriter extends DomDocument
{
    private $wsdlMethods    = array();
    private $wsdlDefinition = null;
    
    /**
     * WsdlWriter Constructor
     *
     * @param string The Object File name
     * @param string The Base Url for the Web Service
     */
    public function __construct(WsdlDefinition $wsdlDefinition)
    {
        // Items before parent Constructor don't stick!
        parent::__construct();

        // DomDocument::$formatOutput Format the WSDL Doc
        $this->formatOutput = true; 
        $this->encoding     = "UTF-8";

        // Set the WsdlWriter Properties
        $this->wsdlMethods  = array();
        $this->wsdlDefinition = $wsdlDefinition;
        
        // Create the Base WSDL Doc
        $this->seedXML();
    }

    /**
     * Save the WSDL File to Disk
     *
     * @return void
     */
    public function save()
    {
        
        // Call DomDocument::save()
        parent::save($this->getFileName());
    }

    
    /**
     * Add a Method to the Wsdl File
     *
     * @param AutoWsdlMethod $wsdlMethod The Method to Add
     */
    public function addMethod($wsdlMethod)
    {
        $this->wsdlMethods[] = $wsdlMethod;
    }


    /**
     * Add a Complex Type to the WSDL
     *
     * @param WsdlType $wsdlType The Type to Add
     */
    public function addComplexType(WsdlType &$wsdlType)
    {
        if (!$wsdlType->isArray()) {
            $this->addType($wsdlType);
        } else {
            $this->addArrayType($wsdlType);
        }
    }


    //----------------------------------------------
    // Begin Private Methods
    //----------------------------------------------

    
    /**
     * Create the Base WSDL XML Nodes
     *
     * @return void
     */
    private function seedXML()
    {
        
        $node = $this->createElement('definitions');
        $node->setAttribute('name', $this->getDefinitionName());

        // Assign Soap Namespace Stuff
        $targetNameSpace = $this->getTargetNameSpace();
        
        $node->setAttribute('targetNamespace', $targetNameSpace);
        $node->setAttribute('xmlns:tns', $targetNameSpace);
        $node->setAttribute('xmlns:impl', $targetNameSpace);
        $node->setAttribute('xmlns:xsd1', $targetNameSpace);
        $node->setAttribute('xmlns:xsd', "http://www.w3.org/2001/XMLSchema");
        $node->setAttribute('xmlns:wsdl', "http://schemas.xmlsoap.org/wsdl/");
        $node->setAttribute('xmlns:soap', "http://schemas.xmlsoap.org/wsdl/soap/");
        $node->setAttribute('xmlns:soapenc', "http://schemas.xmlsoap.org/soap/encoding/");
        $node->setAttribute('xmlns', "http://schemas.xmlsoap.org/wsdl/");

        $this->appendChild($node);

    }

    
    /**
     * Create the guts of the WSDL File
     *
     * @return void
     */
    public function doCreateWsdl()
    {
        $methods  = $this->wsdlMethods;
        
        foreach ($methods as &$method) {
            
            // Do The Operations
            $this->addOperation($method->getOperation());

            // Do Messages
            $this->addMessage($method->getMessageRequest());
            $this->addMessage($method->getMessageResponse());

        }
        
        $this->addService();
    }

    /**
     * Add a WSDL Complex Type to the Document
     *
     * Creates a Complex Type with it's Sequence and Elements
     * 
     * @param  WsdlType $wsdlType The Type to Add
     * @return void
     */
    private function addType(WsdlType &$wsdlType)
    {
        $wsdlSequence = $this->createElement("sequence");

        // Create WSDL Elements 
        $elements     = $wsdlType->getElements();
        foreach ($elements as &$element) {
            $wsdlElement = $this->createElement("element");
            $wsdlElement->setAttribute("name", $element->name);
            if (WsdlType::isPrimitiveType($element->type)) {
                $wsdlElement->setAttribute("type", "xsd:" . $element->type);
            } else {
                $wsdlElement->setAttribute("type", "tns:" . $element->type);
            }
            
            // Add WSDL Element to the Sequence
            $wsdlSequence->appendChild($wsdlElement);
        }

        // Create the WSDL Complex Type
        $complexType = $this->createElement("complexType");
        $complexType->setAttribute("name", $wsdlType->getName());
        $complexType->appendChild($wsdlSequence);

        $wsdlSchema   = $this->getSchema();
        $wsdlSchema->appendChild($complexType);  
    }


    /** 
     * Add a WSDL Complex Array Type to the Document
     *
     * Creates an Array Complex Type which references another Complex Type
     *
     * @param WsdlType $wsdlType The Type to Add
     * @return void
     */
    private function addArrayType(WsdlType &$wsdlType)
    {

        // Create the WSDL Complex Content
        $complexContent = $this->createElement("complexContent");

        // Create WSDL Attribute
        $attribute = $this->createElement("attribute");
        $attribute->setAttribute("ref", "soapenc:arrayType");
        
        if (WsdlType::isPrimitiveType($wsdlType->getBaseName())) {
            $attribute->setAttribute("wsdl:arrayType", "xsd:" . $wsdlType->getArrayTypeName());
        } else {
            $attribute->setAttribute("wsdl:arrayType", "tns:" . $wsdlType->getArrayTypeName());
        }

        // Create WSDL Restriction
        $restriction = $this->createElement("restriction");
        $restriction->setAttribute("base", "soapenc:Array");
        
        // Create the WSDL ComplexType
        $complexType = $this->createElement("complexType");
        $complexType->setAttribute("name", $wsdlType->getName());
       
        // Create the WSDL XML Nesting... 
        $restriction->appendChild($attribute);
        $complexContent->appendChild($restriction);
        $complexType->appendChild($complexContent);
        
        // Append the Type WSDL To the WSDL Schema 
        $wsdlSchema = $this->getSchema();
        $wsdlSchema->appendChild($complexType);

    }


    /**
     * Add the WSDL Service Definition
     *
     * @return void
     */
    private function addService()
    {
        $service = $this->getService();
        
        $port = $this->createElement("port");
        $port->setAttribute("name", $this->getPortName());
        $port->setAttribute("binding", "tns:" . $this->getBindingName());

        $soapAddress = $this->createElement("soap:address");
        $soapAddress->setAttribute("location", $this->getSoapUrl());

        $port->appendChild($soapAddress);

        $service->appendChild($port);
        
    }

    /**
     * Create the portType and Binding Operations WSDL Block
     *
     * @param object $operation The operation to add
     * @return void
     */
    private function addOperation($operation)
    {
        $this->addPortTypeOperation($operation);
        $this->addBindingOperation($operation);
    }    


    /**
     * Create the Binding Operations WSDL Block
     *
     * @param object $operation The operation to add
     * @return void
     */
    private function addBindingOperation($operation)
    {
        $binding  = $this->getBinding();
        
        $definition    = $this->getDefinitionName();
        $soapAction    = "urn:{$definition}#{$definition}Server#{$operation->name}"; 
        
        // Create a new WSDL Operation
        $wsdlOperation = $this->createElement("operation");
        $wsdlOperation->setAttribute("name", $operation->name);

        // Create the Soap:Operation for this Operation
        $wsdlSoapOperation = $this->createElement("soap:operation");
        $wsdlSoapOperation->setAttribute("soapAction", $soapAction);
        
        // Add Soap Operation to the WSDL Operation
        $wsdlOperation->appendChild($wsdlSoapOperation);
        
        // Input Bindings        
        if ($operation->inputMessage)
            $wsdlOperation->appendChild(
                $this->addBindingOperationDirection("input", $operation->inputMessage));
        
        // Output Bindings
        if ($operation->outputMessage)
            $wsdlOperation->appendChild(
                $this->addBindingOperationDirection("output", $operation->outputMessage));
        
        // Add WSDL Operation to WSDL Bindings
        $binding->appendChild($wsdlOperation);
    }
    
    /**
     * Create an Input/Output element inside a SOAP Binding operation
     *
     * @param string $direction Name of the directional element
     * @param object $message Message to bind
     * @return void
     */
    private function addBindingOperationDirection($direction, $message)
    {
        $nameSpace     = $this->getTargetNameSpace();
        $encodingStyle = "http://schemas.xmlsoap.org/soap/encoding/";

        $wsdlDir       = $this->createElement($direction);
        
        // Enumerate SOAP:Header and SOAP:Body parts
        $headerParts   = array();
        $bodyParts     = array();
        
        foreach ($message->parts as $part)
        {
            if ($part->getIsHeader())
                $headerParts[] = $part->getName();
            else
                $bodyParts[] = $part->getName();
        }
        
        // Create the WSDL SOAP:Headers
        foreach ($headerParts as $partName)
        {
            $wsdlSoapHeader = $this->createElement("soap:header");
            $wsdlSoapHeader->setAttribute("message", "tns:" . $message->name);
            $wsdlSoapHeader->setAttribute("part", $partName);
            $wsdlSoapHeader->setAttribute("use", "encoded");
            $wsdlSoapHeader->setAttribute("namespace", $nameSpace);
            $wsdlSoapHeader->setAttribute("encodingStyle", $encodingStyle);
            
            $wsdlDir->appendChild($wsdlSoapHeader);
        }
        
        // Create the WSDL Soap:Body
        $wsdlSoapBody = $this->createElement("soap:body");
        if (count($bodyParts))
            $wsdlSoapBody->setAttribute("parts", implode(' ', $bodyParts));
        $wsdlSoapBody->setAttribute("use", "encoded");
        $wsdlSoapBody->setAttribute("namespace", $nameSpace);
        $wsdlSoapBody->setAttribute("encodingStyle", $encodingStyle);
        
        $wsdlDir->appendChild($wsdlSoapBody);
        
        return $wsdlDir;
    }

    /**
     * Create the portType Operations WSDL Block
     *
     * @param object $operation The operation to add
     * @return void
     */
    private function addPortTypeOperation($operation)
    {
        $portType = $this->getPortType();
        
        // Create a new WSDL Operation
        $wsdlOperation = $this->createElement("operation");
        $wsdlOperation->setAttribute("name", $operation->name);

        if ($operation->inputMessage) {
            $wsdlInput = $this->createElement("input");
            $wsdlInput->setAttribute("message", "tns:" . $operation->inputMessage->name);
            
            $wsdlOperation->appendChild($wsdlInput);
            
            if ($operation->parameterOrder) {
                $wsdlOperation->setAttribute("parameterOrder", $operation->parameterOrder);
            }
        }
        
        if ($operation->outputMessage) {
            $wsdlOutput = $this->createElement("output");
            $wsdlOutput->setAttribute("message", "tns:" . $operation->outputMessage->name);
            
            $wsdlOperation->appendChild($wsdlOutput);
        }
        
        
        $portType->appendChild($wsdlOperation);
    }
    
    /**
     * Create a WSDL Message Block with it's Parts
     *
     * @param string $messageName The message name
     * @return void
     */
    private function addMessage($message)
    {
        if (!$message) {
            return;
        }
        
        $partNodes = array();
        
        // Create a new WSDL Message
        $wsdlMessage = $this->createElement("message");
        $wsdlMessage->setAttribute("name", $message->name);
        
        foreach ($message->parts as &$part) {
            // Create a new WSDL Part
            $wsdlPart = $this->createElement("part");
            $wsdlPart->setAttribute('name', $part->getName());
            $wsdlPart->setAttribute('type', $part->getType());
            
            // Add the Part to the WSDL Message Block 
            $wsdlMessage->appendChild($wsdlPart); 
        }
        
        // Add the Message the WSDL Definitions Block 
        $this->getDefinition()->appendChild($wsdlMessage);
    }
   
   
    /**
     * Get the Definition Tag
     *
     * @return DomNode The Definition Dom Node
     */
    private function getDefinition()
    {
        return $this->getElementsByTagName('definitions')->item(0);
    }
   
    /**
     * Get the Schema Tag
     *
     * @return DomNode The Schema Dom Node
     */
    private function getSchema()
    {
        $schema = $this->getElementsByTagName('schema')->item(0);
        if (!$schema) {
            $schema = $this->createElement('schema');
            $schema->setAttribute("xmlns", "http://www.w3.org/2001/XMLSchema");
            $schema->setAttribute("targetNamespace", $this->getTargetNameSpace());
            $this->getTypes()->appendChild($schema);
            $schema = $this->getElementsByTagName('schema')->item(0);
        }
        return $schema;
    }
    
    /**
     * Get the Types Tag
     *
     * @return DomNode The Types Dom Node
     */
    private function getTypes()
    {
        $types = $this->getElementsByTagName('types')->item(0);
        if (!$types) {
            $types = $this->createElement('types');
            $types->setAttribute("xmlns", "http://schemas.xmlsoap.org/wsdl/");
            $this->getDefinition()->appendChild($types);
            $types = $this->getElementsByTagName('types')->item(0);
        }
        return $types;
    }

    /**
     * Get the portType Tag
     *
     * @return DomNode The portType Dom Node
     */
    private function getPortType()
    {
        if (!$this->getElementsByTagName('portType')->item(0)) {
            $portType = $this->createElement('portType');
            $portType->setAttribute("name", $this->getPortTypeName());
            $this->getDefinition()->appendChild($portType);
        }
        return $this->getElementsByTagName('portType')->item(0);
    }



    /**
     * Get the Service Tag
     *
     * @return DomNode The Service Dom Node
     */
    private function getService()
    {
        if (!$this->getElementsByTagName('service')->item(0)) {
            $service = $this->createElement('service');
            $service->setAttribute("name", $this->getServiceName());
            $this->getDefinition()->appendChild($service);
        }
        return $this->getElementsByTagName('service')->item(0);
    }


    /**
     * Get the Service Tag
     *
     * @return DomNode The Service Dom Node
     */
    private function getBinding()
    {
        if (!$this->getElementsByTagName('binding')->item(0)) {
            // Create the Binding Element
            $binding = $this->createElement('binding');
            $binding->setAttribute("name", $this->getBindingName());
            $binding->setAttribute("type", "tns:" . $this->getPortTypeName());
            
            // Set Soap:Binding to rpc
            $soapBinding = $this->createElement('soap:binding');
            $soapBinding->setAttribute("style", "rpc");
            $soapBinding->setAttribute("transport", "http://schemas.xmlsoap.org/soap/http");

            // Add Soap:Binding to Binding Element
            $binding->appendChild($soapBinding);
            
            // Add it to the Document
            $this->getDefinition()->appendChild($binding);
        }
        return $this->getElementsByTagName('binding')->item(0);
    }


    /**
     * Get the Name of the PortType
     *
     * @return string The Name of the PortType
     */
    private function getPortTypeName()
    {
        return $this->getDefinitionName() . "PortType";
    }


    /**
     * Get the Name of the Port
     *
     * @return string The Name of the Port
     */
    private function getPortName()
    {
        return $this->getDefinitionName() . "Port";
    }


    /**
     * Get the Name of the Service
     *
     * @return string The Name of the Service
     */
    private function getServiceName()
    {
        return $this->getDefinitionName() . "Service";
    }

    /**
     * Get the Name of the Binding
     *
     * @return string The Name of the Binding
     */
    private function getBindingName()
    {
        return $this->getDefinitionName() . "Binding";
    }


    /**
     * Get the Name of the WSDL Definition
     *
     * @return string The name of the definition
     */
    public function getDefinitionName()
    {
        return $this->getWsdlDefinition()->getDefinitionName();
    }


    /**
     * Get the name of the File to Write
     *
     * @return string The file name to write
     */
    public function getClassName()
    {
        $className = $this->getWsdlDefinition()->getClassFileName();
        $className = basename($className, ".inc");
        $className = basename($className, ".php");

        return $className;
    }

    /**
     * Get the name of the File to Write
     *
     * @return string The file name to write
     */
    public function getFileName()
    {
        return $this->getWsdlDefinition()->getWsdlFileName();
    }


    /**
     * Get the Name Space URL
     *
     * @return string the Target Name Space URL 
     */
    public function getTargetNameSpace()
    {
        return $this->getWsdlDefinition()->getNameSpace();
    }


    /**
     * Get the Soap URL used to Access this Service
     *
     * @return string the Soap URL 
     */
    public function getSoapUrl()
    {
        return $this->getWsdlDefinition()->getEndPoint();
    }
    
    
    /**
     * Get the WSDL Definition
     *
     * @return WsdlDefinition The WSDL Definition Object
     */
    public function getWsdlDefinition()
    {
       return $this->wsdlDefinition;
    }
   

}


