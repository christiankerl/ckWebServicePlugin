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
$debug = true;

include_once(dirname(__FILE__).'/../bootstrap/functional.php');

class MyComplexNumber
{
  public $realPart;

  public $imaginaryPart;

  public function __construct($realPart, $imaginaryPart)
  {
    $this->realPart      = $realPart;
    $this->imaginaryPart = $imaginaryPart;
  }
}

class MyAuthData
{
  public $username;
  public $password;
}

$_options = array(
  'classmap' => array(
    'ComplexNumber' => 'MyComplexNumber',
    'AuthHeader'    => 'MyAuthData',
  ),
);

$c = new ckTestSoapClient($_options);

// test executeMultiply
$c->math_multiply(2, 5)
  ->isFaultEmpty()
  ->isType('', 'double')
  ->is('', 10)
  ;

// test executeSimpleMulitply
$c->SimpleMultiply(2, 5)
  ->isFaultEmpty()
  ->isType('', 'double')
  ->is('', 10)
  ;

// test executeArrayMultiply
$c->ArrayMultiply(array(1, 2, 3, 4))
  ->isFaultEmpty()
  ->isType('', 'double')
  ->is('', 24)
  ;

// test executeComplexMultiply
$c->ComplexMultiply(array(new MyComplexNumber(1, 0), new MyComplexNumber(1, 0)))
  ->isFaultEmpty()
  ->isType('', 'MyComplexNumber')
  ->is('realPart', 1)
  ->is('imaginaryPart', 0)
  ;

// test executeSimpleMultiplyWithHeaderWithException
$c->SimpleMultiplyWithHeaderWithException(5, 2)
  ->hasFault('Unauthenticated user!')
  ;

// test executeSimpleMultiplyWithHeaderWithFault
$c->SimpleMultiplyWithHeaderWithFault(5, 2)
  ->hasFault('Unauthenticated user!')
  ;

// test executeSimpleMultiplyWithHeader
$authData = new MyAuthData();
$authData->username = 'test';
$authData->password = 'secret';

$c->addRequestHeader('AuthHeaderElement', $authData)
  ->SimpleMultiplyWithHeader(5, 2)
  ->isFaultEmpty()
  ->isHeaderType('AuthHeaderElement', 'MyAuthData')
  ->isHeader('AuthHeaderElement.username', 'test')
  ->isHeader('AuthHeaderElement.password', 'secret')
  ->isType('', 'double')
  ->is('', 10)
  ;