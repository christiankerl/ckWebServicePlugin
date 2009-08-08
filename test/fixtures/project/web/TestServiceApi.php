<?php


require_once(dirname(__FILE__).'/../config/ProjectConfiguration.class.php');

$configuration = ProjectConfiguration::getApplicationConfiguration('frontend', 'soapTestServiceApi', false);
sfContext::createInstance($configuration)->dispatch();
