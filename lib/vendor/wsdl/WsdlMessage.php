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
class AutoWsdlMethod
{

    private $name               = null;
    private $description        = null;

    private $params             = array();
    private $returnType         = null;
    private $returnDescription  = null;

    public function setName($name)
    {
        $this->name = $name;
    }

    public function setDescription($desc)
    {
        $this->description = $desc;
    }

    public function addParameter($varType, $varName, $varDescription)
    {
        $param = new StdClass();
        
        $param->type = $varType;
        $param->name = $varName;
        $param->description = $varDescription;
        
        $this->params[] = $param;    
    }

    public function setReturn($varType, $varDescription)
    {
        $this->returnType = $varType;
        $this->returnDescription = $varDescription;
    }
}

?>
