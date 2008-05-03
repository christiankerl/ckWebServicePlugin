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

require_once("WsdlMethod.php");
require_once("WsdlProperty.php");
require_once("WsdlType.php");
require_once("WsdlWriter.php");
require_once("DocBlockParser.php");

/**
 * WSDL Generator for PHP5
 *
 * @package   wsdl.writer
 * @author    David Giffin <david@giffin.org>
 *
 */
class BaseWsdl
{
    /** The Name of the Class we are Reflecting */
    private $classDir  = null;

    /** The Name of the Class we are Reflecting */
    private $className = null;

    private $endPoint  = null;
    
    /**
     * The Main method: Start the Script!
     *
     */
    public function main($argv)
    {
        if (count($argv) < 3) {
            $this->doUsage($argv[0]);
            exit();
        }

        $wsdlFileName  = null;
        $endPoint      = null;
        $wsdlMethods   = array();
        $classFileName = $argv[1];
        $nameSpaceUrl  = $argv[2];
        
        if (isset($argv[3])) {
            $wsdlFileName  = $argv[3];
        }
        
        if (isset($argv[4])) {
            $endPoint      = $argv[4];
        }

        print $this->createWsdl($classFileName, $nameSpaceUrl, $wsdlFileName, $endPoint);
    }


    public function createWsdl($classFileName, $nameSpaceUrl, $wsdlFile = null, $endPoint = null)
    {
        // Require the Class we are Parsing
        require_once($classFileName);

        $this->classDir = dirname($classFileName);
        
        // Strip Extension to get Class Name
        $this->className = $classFileName;
        $this->className = basename($this->className, ".inc");
        $this->className = basename($this->className, ".php");

        print "Creating Wsdl for Class: {$this->className}\n"; 

        // Calculate the EndPoint for this service
        $this->endPoint  = $nameSpaceUrl . $endPoint;
        if (!$endPoint) {
            $this->endPoint = $nameSpaceUrl . $this->className . ".php";
        }
        
        // Get the WSDL File Name
        if (!$wsdlFile) {
            $wsdlFile = $this->className . ".wsdl";
        }
        
        // Create a WSDL File
        $wsdl = new WsdlWriter($wsdlFile, $nameSpaceUrl, $this->endPoint);
        
        // Get the Methods from the Class
        $wsdlMethods = $this->getMethods();

        // Find the Complex Types
        $complexTypes = WsdlType::getComplexTypes($wsdlMethods);

        foreach ($complexTypes as &$complexType) {
            $wsdl->addComplexType($complexType);
        }

        // Add Methods to the WSDL File
        foreach ($wsdlMethods as $wsdlMethod) {
            $wsdl->addMethod($wsdlMethod);
        }

        // Write it to Disk
        $wsdl->save();

        return $wsdl->saveXML();
    }


    /**
     * Get the WSDL Methods from the Class File
     *
     * @return array An array of methods
     */
    private function getMethods()
    {
        $reflect     = new ReflectionClass($this->className);
        $methods     = $reflect->getMethods();
        $wsdlMethods = array();

        foreach ($methods as &$method) {
            if (!$method->isPublic() || $method->isProtected()) {
                continue;
            }
            $wsdlMethods[] = $this->getWsdlMethod($method); 
        }
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
                    default:
                        break;
                }
            }
        }
        return $wsdlMethod; 
    }


    /**
     * Print the Usage of this Script
     * 
     * @param string $progname The Program Name
     * @return void
     */
    public function doUsage($progname)
    {
$out =<<<USAGE

usage: $progname <Class File> <Name Space URL> <WSDL File Name> <End Point File>
               
example:

  $progname TestClass.inc http://127.0.0.1/soap/
                
This program will take a class file and find
all methods that are declared "public" and
create a WSDL file for use with the embeded
SOAP server included in PHP5. The WSDL will
be output to a file: <base class name>.wsdl
The Name Space URL will be appended with
the name of the WSDL file.

If 'End Point File' is specified it is
concatenated to the 'Name Space URL'

USAGE;

    print $out;

    }

}


?>
