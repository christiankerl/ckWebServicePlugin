<?php
/**
 * This file is part of the ckWebServicePlugin
 *
 * @package   ckWebServicePlugin
 * @author    Christian Kerl <christian-kerl@web.de>
 * @copyright Copyright (c) 2008, Christian Kerl
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 * @version   SVN: $Id$
 */

$app = 'frontend';
$env = 'prod';
$debug = true;

include_once(dirname(__FILE__).'/../bootstrap/functional.php');

$b = new sfBrowser('myfancywebservice.com');
$t = new sfTestFunctional($b);

$t->get('/TestServiceApi.wsdl')->
  with('request')->begin()->
    isParameter('module', 'ckWsdl')->
    isParameter('action', 'bind')->
    isParameter('service', 'TestServiceApi')->
  end()->
  with('response')->begin()->
    isStatusCode()->
    checkElement('definitions service port address[location="http://myfancywebservice.com/TestServiceApi.php"]')->
  end()->
  with('view_cache')->begin()->
    isCached(true, false)->
  end()
  ;

$t->get('/NonExistingService.wsdl')->
  with('request')->begin()->
    isParameter('module', 'ckWsdl')->
    isParameter('action', 'bind')->
    isParameter('service', 'NonExistingService')->
  end()->
  with('response')->begin()->
    isStatusCode(404)->
  end()
  ;