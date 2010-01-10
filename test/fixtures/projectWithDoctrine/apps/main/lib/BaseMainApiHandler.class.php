<?php

/**
 * This is an auto-generated SoapHandler. All changes to this file will be overwritten.
 */
class BaseMainApiHandler extends ckSoapHandler
{
  public function getFixtureModel()
  {
    return sfContext::getInstance()->getController()->invokeSoapEnabledAction('main', 'getFixtureModel', array());
  }

  public function passFixtureModel($articles)
  {
    return sfContext::getInstance()->getController()->invokeSoapEnabledAction('main', 'passFixtureModel', array($articles));
  }
}