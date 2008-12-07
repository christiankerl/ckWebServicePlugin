<?php
##IP_CHECK##
require_once(dirname(__FILE__).'/../config/ProjectConfiguration.class.php');

$configuration = ProjectConfiguration::getApplicationConfiguration('tutorial', 'soaptest', false);
sfContext::createInstance($configuration)->dispatch();
