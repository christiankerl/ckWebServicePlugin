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
$debug = false;

include_once(dirname(__FILE__).'/../bootstrap/functional.php');

$c = new ckTestSoapClient(new sfTestBrowser());

$testInput = 'some string';


if($c->test_test($testInput)->getResult() == $testInput)
{
  echo 'Test succeded!';
}
else
{
  echo 'Test failed!';
}