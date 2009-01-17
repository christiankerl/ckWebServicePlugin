<?php

/**
 * This is an auto-generated SoapHandler. All changes to this file will be overwritten.
 */
class BaseMathApiHandler extends ckSoapHandler
{
  public function math_multiply($a, $b)
  {
    return sfContext::getInstance()->getController()->invokeSoapEnabledAction('math', 'multiply', array($a, $b));
  }

  public function SimpleMultiply($a, $b)
  {
    return sfContext::getInstance()->getController()->invokeSoapEnabledAction('math', 'simpleMultiply', array($a, $b));
  }

  public function SimpleMultiplyWithHeader($a, $b)
  {
    return sfContext::getInstance()->getController()->invokeSoapEnabledAction('math', 'simpleMultiplyWithHeader', array($a, $b));
  }

  public function ArrayMultiply($factors)
  {
    return sfContext::getInstance()->getController()->invokeSoapEnabledAction('math', 'arrayMultiply', array($factors));
  }

  public function ComplexMultiply($input)
  {
    return sfContext::getInstance()->getController()->invokeSoapEnabledAction('math', 'complexMultiply', array($input));
  }
}