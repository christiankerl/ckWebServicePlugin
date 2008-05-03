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

require_once("BaseWsdlWriter.php");
require_once("WsdlMethod.php");
require_once("DocBlockParser.php");

/**
 * WSDL Generator for PHP5
 *
 * @package   wsdl.writer
 * @author    David Giffin <david@giffin.org>
 *
 */
class WsdlWriter extends BaseWsdlWriter
{
    private $baseUrl     = null;
    private $endPointUrl = null;
    private $fileName    = null;
    private $wsdlMethods = array();
    
    /**
     * WsdlWriter Constructor
     *
     * @param string The Object File name
     * @param string The Base Url for the Web Service
     */
    public function __construct(WsdlDefinition $wsdlDefinition)
    {
        // Items before parent Constructor don't stick!
        parent::__construct($wsdlDefinition);
        
    }


    public function classToWsdl()
    {
        $className = $this->getClassName();
        
        // Get the Methods from the Class
        $wsdlMethods = $this->getMethods($className);

        // Find the Complex Types
        $complexTypes = WsdlType::getComplexTypes($wsdlMethods);

        foreach ($complexTypes as &$complexType) {
            $this->addComplexType($complexType);
        }

        // Add Methods to the WSDL File
        foreach ($wsdlMethods as $wsdlMethod) {
			if (!$wsdlMethod->getIsHeader())
				$this->addMethod($wsdlMethod);
        }
        $this->doCreateWsdl();

        return $this->saveXML();
    }


    /**
     * Get the WSDL Methods from the Class File
     *
     * @return array An array of methods
     */
    private function getMethods($className)
    {
        $reflect     = new ReflectionClass($className);
        $methods     = $reflect->getMethods();
        $wsdlMethods = array();

        foreach ($methods as &$method) {
            if (!$method->isPublic() || $method->isProtected()
			|| substr($method->getName(), 0, 2) == '__') {
                continue;
            }
            // print "Found Method: " . $method->getName() . "\n";
            $wsdlMethods[] = $this->getWsdlMethod($method);
        }
		
		foreach ($wsdlMethods as $wsdlMethod)
			$wsdlMethod->resolveHeaders($wsdlMethods);
			
        return $wsdlMethods;
        
    }


    /**
     * Get a Service information for a Method
     *
     * @param  ReflectionMethod $method The method to get the Service Information
     * @return WsdlMethod  The Service Information object
     */
    private function getWsdlMethod(ReflectionMethod $method)
    {
        $doc     = $method->getDocComment();
        $wsdlMethod = new WsdlMethod();
        $wsdlMethod->setName($method->getName());
        $wsdlMethod->setDesc(DocBlockParser::getDescription($doc));
        
        $params = DocBlockParser::getTagInfo($doc);
        
        for ($i = 0, $c = count($params); $i < $c; $i++) {
            foreach ($params[$i]  as $tag => $param) {
                switch ($tag) {
                    case "@param":
                        if (isset($param['type']) && isset($param['name'])) {
                            $wsdlMethod->addParameter($param['type'], $param['name'], $param['desc']);
                        }
                        break;
                    case "@return":
                        $wsdlMethod->setReturn($param['type'], $param['desc']);
						break;
					case "@internal":
						if (trim(strtolower($param['usage'])) == 'soapheader')
							$wsdlMethod->setIsHeader(true);
							
						if (substr(trim(strtolower($param['usage'])), 0, 13) == 'soaprequires ')
							$wsdlMethod->setRequiredHeaders(explode(' ', substr(trim($param['usage']), 13)));
						
						break;
                    default:
                        break;
                }
            }
        }
        return $wsdlMethod; 
    }



}


?>
