<?php

include(dirname(__FILE__).'/../../bootstrap/functional.php');

// create a new test browser
$browser = new sfTestBrowser();

$browser->
  get('/math/index')->
  isStatusCode(200)->
  isRequestParameter('module', 'math')->
  isRequestParameter('action', 'index')->
  checkResponseElement('body', '!/This is a temporary page/');
