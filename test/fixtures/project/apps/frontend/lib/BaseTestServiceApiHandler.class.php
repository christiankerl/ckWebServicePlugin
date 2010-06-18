<?php

/**
 * This is an auto-generated SoapHandler. All changes to this file will be overwritten.
 */
class BaseTestServiceApiHandler extends ckSoapHandler
{
  public function nonLC_name_getResult()
  {
    return sfContext::getInstance()->getController()->invokeSoapEnabledAction('nonLC_name', 'getResult', array());
  }

  public function test_noArg()
  {
    return sfContext::getInstance()->getController()->invokeSoapEnabledAction('test', 'noArg', array());
  }

  public function test_simple($boolVal, $intVal, $stringVal, $floatVal)
  {
    return sfContext::getInstance()->getController()->invokeSoapEnabledAction('test', 'simple', array($boolVal, $intVal, $stringVal, $floatVal));
  }

  public function test_complex($testDataVal)
  {
    return sfContext::getInstance()->getController()->invokeSoapEnabledAction('test', 'complex', array($testDataVal));
  }

  public function test_arraySimple($intArrayVal)
  {
    return sfContext::getInstance()->getController()->invokeSoapEnabledAction('test', 'arraySimple', array($intArrayVal));
  }

  public function test_arrayComplex($testDataArrayVal)
  {
    return sfContext::getInstance()->getController()->invokeSoapEnabledAction('test', 'arrayComplex', array($testDataArrayVal));
  }

  public function test_arrayArray($stringArrayOfArrayVal)
  {
    return sfContext::getInstance()->getController()->invokeSoapEnabledAction('test', 'arrayArray', array($stringArrayOfArrayVal));
  }

  public function test_headerSingle()
  {
    return sfContext::getInstance()->getController()->invokeSoapEnabledAction('test', 'headerSingle', array());
  }

  public function test_headerMulti()
  {
    return sfContext::getInstance()->getController()->invokeSoapEnabledAction('test', 'headerMulti', array());
  }

  public function test_exception()
  {
    return sfContext::getInstance()->getController()->invokeSoapEnabledAction('test', 'exception', array());
  }

  public function test_soapFault()
  {
    return sfContext::getInstance()->getController()->invokeSoapEnabledAction('test', 'soapFault', array());
  }

  public function test_configuredProperty()
  {
    return sfContext::getInstance()->getController()->invokeSoapEnabledAction('test', 'configuredProperty', array());
  }

  public function test_methodResult()
  {
    return sfContext::getInstance()->getController()->invokeSoapEnabledAction('test', 'methodResult', array());
  }

  public function test_renderResult()
  {
    return sfContext::getInstance()->getController()->invokeSoapEnabledAction('test', 'renderResult', array());
  }
}