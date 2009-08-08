<?php

/**
 * test actions.
 *
 * @package    project
 * @subpackage test
 * @author     Christian Kerl <christian-kerl@web.de>
 * @version    SVN: $Id$
 */
class testActions extends sfActions
{
  /**
   * @WSMethod(webservice='TestServiceApi')
   *
   * @return bool
   */
  public function executeNoArg($request)
  {
    $this->result = true;
  }

  /**
   * Test action for simple type mapping.
   *
   * @WSMethod(webservice='TestServiceApi')
   *
   * @param bool   $boolVal
   * @param int    $intVal
   * @param string $stringVal
   * @param float  $floatVal
   *
   * @return bool
   */
  public function executeSimple($request)
  {
    if( is_bool($request->getParameter('boolVal')) &&
        is_int($request->getParameter('intVal')) &&
        is_string($request->getParameter('stringVal')) &&
        is_double($request->getParameter('floatVal')))
    {
        $this->result = true;
    }
    else
    {
      throw new sfException('SimpleTypeMappingException');
    }
  }

  /**
   * Test action for complex type mapping.
   *
   * @WSMethod(webservice='TestServiceApi')
   *
   * @param TestData $testDataVal
   *
   * @return TestData
   */
  public function executeComplex($request)
  {
    $object = $request->getParameter('testDataVal');

    if($object instanceof TestData && is_string($object->content))
    {
      $this->result = $object;
    }
    else
    {
      throw new sfException('ComplexTypeMappingException');
    }
  }

  /**
   * Test action for simple array type mapping.
   *
   * @WSMethod(webservice='TestServiceApi')
   *
   * @param int[] $intArrayVal
   *
   * @return string[]
   */
  public function executeArraySimple($request)
  {
    $in   = $request->getParameter('intArrayVal');
    $test = array(1, 2, 3, 4);

    if(is_array($in) && $in == $test)
    {
      $this->result = array('a', 'b');
    }
    else
    {
      throw new sfException('SimpleArrayTypeMappingException');
    }
  }

  /**
   * Test action for complex array type mapping.
   *
   * @WSMethod(webservice='TestServiceApi')
   *
   * @param TestData[] $testDataArrayVal
   *
   * @return TestData[]
   */
  public function executeArrayComplex($request)
  {
    $in = $request->getParameter('testDataArrayVal');

    if(is_array($in) && $in[0] instanceof TestData)
    {
      $this->result = $in;
    }
    else
    {
      throw new sfException('ComplexArrayTypeMappingException');
    }
  }

  /**
   * Test action for simple array of array type mapping.
   *
   * @WSMethod(webservice='TestServiceApi')
   *
   * @param string[][] $stringArrayOfArrayVal
   *
   * @return string[][]
   */
  public function executeArrayArray($request)
  {
    $in = $request->getParameter('stringArrayOfArrayVal');

    if(is_array($in) && is_array($in[0]) && is_string($in[0][0]))
    {
      $this->result = $in;
    }
    else
    {
      throw new sfException('SimpleArrayOfArrayTypeMappingException');
    }
  }

  /**
   * Test action for handling a single SoapHeader.
   *
   * @WSMethod(webservice='TestServiceApi')
   * @WSHeader(name='AuthHeader', type='AuthData')
   */
  public function executeHeaderSingle($request)
  {
    if(!$this->getUser()->isAuthenticated())
    {
      throw new sfException('HeaderHandlingException');
    }
  }

  /**
   * Test action for handling multiple SoapHeaders.
   *
   * @WSMethod(webservice='TestServiceApi')
   * @WSHeader(name='AuthHeader', type='AuthData')
   * @WSHeader(name='ExtraHeader', type='ExtraHeaderData')
   */
  public function executeHeaderMulti($request)
  {
    if(!$this->getUser()->isAuthenticated())
    {
      throw new sfException('HeaderHandlingException');
    }
  }

  /**
   * Test action for throwing custom Exceptions.
   *
   * @WSMethod(webservice='TestServiceApi')
   */
  public function executeException($request)
  {
    if($this->isSoapRequest())
    {
      throw new sfException('TestException');
    }
  }

  /**
   * Test action for throwing custom SoapFaults.
   *
   * @WSMethod(webservice='TestServiceApi')
   */
  public function executeSoapFault($request)
  {
    if($this->isSoapRequest())
    {
      throw new SoapFault('Server', 'TestSoapFault');
    }
  }

  /**
   * Test action for the ckMethodResultAdapter.
   *
   * @WSMethod(webservice='TestServiceApi')
   *
   * @return string
   */
  public function executeMethodResult($request)
  {
    $this->result = 'T3stR3spons3';
  }

  /**
   * Test action for the ckRenderResultAdapter.
   *
   * @WSMethod(webservice='TestServiceApi')
   *
   * @return string
   */
  public function executeRenderResult($request)
  {
    if($this->isSoapRequest())
    {
      $this->setLayout(false);
    }

    $this->result = array('a', 'b', 1, 2);

    return sfView::SUCCESS;
  }

  public function getFilteredResult()
  {
    return str_replace('3', 'e', $this->result);
  }
}
