<?php

/**
 * This is an auto-generated SoapHandler. All changes to this file will be overwritten.
 */
class BaseORMTestApiHandler extends ckSoapHandler
{
  public function orm_getObjectDoctrine()
  {
    return sfContext::getInstance()->getController()->invokeSoapEnabledAction('orm', 'getObjectDoctrine', array());
  }

  public function orm_setObjectDoctrine($article)
  {
    return sfContext::getInstance()->getController()->invokeSoapEnabledAction('orm', 'setObjectDoctrine', array($article));
  }

  public function orm_getObjectPropel()
  {
    return sfContext::getInstance()->getController()->invokeSoapEnabledAction('orm', 'getObjectPropel', array());
  }

  public function orm_setObjectPropel($article)
  {
    return sfContext::getInstance()->getController()->invokeSoapEnabledAction('orm', 'setObjectPropel', array($article));
  }
}