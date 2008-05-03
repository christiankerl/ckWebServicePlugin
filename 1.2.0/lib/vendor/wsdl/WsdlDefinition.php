<?php
/**
 * Wsdl Definition
 *
 * @package  wsdl.writer
 *
 * @version   $Id: WsdlDefinition.inc,v 0.0 2005/04/20 16:27:43 user Exp $
 * @since     PHP 5.0
 * @author   David Giffin
 * @copyright Copyright (c) 2005 David Giffin
 *
 */

/**
 * WsdlDefinition
 *
 * Wsdl Definition
 *
 * @author   David Giffin
 * @package  wsdl.writer
 *
 */
class WsdlDefinition
{
 
    /** @var string */
    private $classFileName;

 
    /** @var string */
    private $wsdlFileName;

 
    /** @var string */
    private $definitionName;

 
    /** @var string */
    private $endPoint;

 
    /** @var string */
    private $nameSpace;

 
    /** @var string */
    private $baseUrl;

    
 
    /**
     * Get the value of classFileName
     *
     * @return string The value of classFileName
     */
    public function getClassFileName()  
    {
        return $this->classFileName;
    }

    /**
     * Set the value of classFileName
     *
     * @param string $classFileName The value of classFileName
     */
    public function setClassFileName($classFileName)  
    {
        require_once($classFileName);
        $this->classFileName = $classFileName;
    }

 
    /**
     * Get the value of wsdlFileName
     *
     * @return string The value of wsdlFileName
     */
    public function getWsdlFileName()  
    {
        return $this->wsdlFileName;
    }

    /**
     * Set the value of wsdlFileName
     *
     * @param string $wsdlFileName The value of wsdlFileName
     */
    public function setWsdlFileName($wsdlFileName)  
    {
        $this->wsdlFileName = $wsdlFileName;
    }

 
    /**
     * Get the value of definitionName
     *
     * @return string The value of definitionName
     */
    public function getDefinitionName()  
    {
        return $this->definitionName;
    }

    /**
     * Set the value of definitionName
     *
     * @param string $definitionName The value of definitionName
     */
    public function setDefinitionName($definitionName)  
    {
        $this->definitionName = $definitionName;
    }

 
    /**
     * Get the value of endPoint
     *
     * @return string The value of endPoint
     */
    public function getEndPoint()  
    {
        return $this->endPoint;
    }

    /**
     * Set the value of endPoint
     *
     * @param string $endPoint The value of endPoint
     */
    public function setEndPoint($endPoint)  
    {
        $this->endPoint = $endPoint;
    }

 
    /**
     * Get the value of nameSpace
     *
     * @return string The value of nameSpace
     */
    public function getNameSpace()  
    {
        return $this->nameSpace;
    }

    /**
     * Set the value of nameSpace
     *
     * @param string $nameSpace The value of nameSpace
     */
    public function setNameSpace($nameSpace)  
    {
        $this->nameSpace = $nameSpace;
    }

 
    /**
     * Get the value of baseUrl
     *
     * @return string The value of baseUrl
     */
    public function getBaseUrl()  
    {
        return $this->baseUrl;
    }

    /**
     * Set the value of baseUrl
     *
     * @param string $baseUrl The value of baseUrl
     */
    public function setBaseUrl($baseUrl)  
    {
        $this->baseUrl = $baseUrl;
    }

}
