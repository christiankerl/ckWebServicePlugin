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

require_once("WsdlType.php");

/**
 * WSDL Generator for PHP5
 *
 * @package   wsdl.writer
 * @author    David Giffin <david@giffin.org>
 *
 */
class WsdlPart
{

    private $isComplexType = false;
    private $name = null;
    private $type = null;
    private $headerPart = false;


    public function setName($name)
    {
        $this->name = $name;
    }

    public function setType($type)
    {
        if (WsdlType::isComplexType($type)) {
            $this->isComplexType = true;
        }
        $this->type = $type;
    }

    public function getBaseType()
    {
        return $this->type;
    }

    public function getType()
    {
        if ($this->isComplexType) {
            if (WsdlType::isArrayTypeClass($this->type)) {
                return "tns:" . WsdlType::getArrayComplexTypeName($this->type);
            }
            return "tns:" . $this->type;
        }
        return "xsd:" . $this->type;
    }

    public function getName()
    {
        return $this->name;
    }

    public function isComplexType()
    {
        return $this->isComplexType;
    }

    /**
     * Set whether this part will be sent as a SOAP header in the SOAP binding
     *
     * @param boolean $header True if header part, false if body part
     */
    public function setIsHeader($header)
    {
        $this->headerPart = $header;
    }    

    /**
     * Get whether this part will be sent as a SOAP header in the SOAP binding
     *
     * @param boolean True if header part, false if body part
     */
    public function getIsHeader()
    {
        return $this->headerPart;
    }    
}

?>
