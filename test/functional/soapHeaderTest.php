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

$_options = array(
  'classmap' => array(
    'ExtraHeader' => 'ExtraHeaderData',
    'AuthHeader'  => 'AuthData',
  ),
);

$c = new ckTestSoapClient($_options);

// test executeHeaderSingle
$authData = new AuthData();
$authData->username = 'user';
$authData->password = 'secret';

$c->addRequestHeader('AuthHeaderElement', $authData)
  ->test_headerSingle()
  ->isFaultEmpty()
  ->isHeaderType('AuthHeaderElement', 'AuthData')
  ->isHeader('AuthHeaderElement.username', 'reset')
  ->isHeader('AuthHeaderElement.password', 'reset')
  ;

// test executeHeaderMulti
$authData = new AuthData();
$authData->username = 'user';
$authData->password = 'secret';

$testData = new ExtraHeaderData();
$testData->content = 'input';

$c->addRequestHeader('AuthHeaderElement', $authData)
  ->addRequestHeader('ExtraHeaderElement', $testData)
  ->test_headerMulti()
  ->isFaultEmpty()
  ->isHeaderType('AuthHeaderElement', 'AuthData')
  ->isHeader('AuthHeaderElement.username', 'reset')
  ->isHeader('AuthHeaderElement.password', 'reset')
  ->isHeaderType('ExtraHeaderElement', 'ExtraHeaderData')
  ->isHeader('ExtraHeaderElement.content', 'HandledInput(input)')
  ;