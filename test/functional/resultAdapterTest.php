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
$env = 'soapTestServiceApi';
$debug = true;

include_once(dirname(__FILE__).'/../bootstrap/functional.php');

$c = new ckTestSoapClient();

// test executeConfiguredProperty (Ticket #8769)
$c->test_configuredProperty()
  ->isFaultEmpty()
  ->isType('', 'string')
  ->is('', 'MyCustomResult')
  ;

// test executeGetResult (Ticket #8474)
$c->nonLC_name_getResult()
  ->isFaultEmpty()
  ->isType('', 'string')
  ->is('', 'MyCustomResult')
  ;

// test executeMethodResult
$c->test_methodResult()
  ->isFaultEmpty()
  ->isType('', 'string')
  ->is('', 'TestResponse')
  ;

// test executeRenderResult
$c->test_renderResult()
  ->isFaultEmpty()
  ->isType('', 'string')
  ->is('', 'List: a, b, 1, 2')
  ;