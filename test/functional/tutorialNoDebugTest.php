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

$app = 'tutorial';
$debug = false;

include_once(dirname(__FILE__).'/../bootstrap/functional.php');

$_options = array(
  'classmap' => array(
  ),
);

$c = new ckTestSoapClient($_options);

// test executeSimpleMultiplyWithHeaderWithException
$c->SimpleMultiplyWithHeaderWithException(5, 2)
  ->hasFault('Internal Server Error')
  ;

// test executeSimpleMultiplyWithHeaderWithFault
$c->SimpleMultiplyWithHeaderWithFault(5, 2)
  ->hasFault('Unauthenticated user!')
  ;