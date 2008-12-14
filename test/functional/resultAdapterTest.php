<?php
/**
 * This file is part of the ckWebServicePlugin
 *
 * @package   ckWebServicePlugin
 * @author    Christian Kerl <christian-kerl@web.de>
 * @copyright Copyright (c) 2008, Christian Kerl
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 * @version   SVN: $Id: typeMappingTest.php 13803 2008-12-06 23:53:03Z chrisk $
 */

$app = 'frontend';
$debug = true;

include_once(dirname(__FILE__).'/../bootstrap/functional.php');

$c = new ckTestSoapClient();

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