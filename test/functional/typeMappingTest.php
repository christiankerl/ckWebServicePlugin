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
$debug = true;

include_once(dirname(__FILE__).'/../bootstrap/functional.php');

$_options = array(
  'classmap' => array(
    'TestData'         => 'TestData',
    'TestBean'         => 'MyTestBean',
    'StringArray'      => 'ckGenericArray',
    'TestDataArray'    => 'ckGenericArray',
    'StringArrayArray' => 'ckGenericArray',
  ),
);

class MyTestBean
{
  public $data;

  public function __construct($data)
  {
    $this->data = $data;
  }
}

$c = new ckTestSoapClient($_options);

// test executeSimple
$c->test_simple(true, 5, 'a string', 1.5)
  ->isFaultEmpty()
  ->isType('', 'boolean')
  ->is('', true);

// test executeComplex
$object = new TestData();
$object->content = 'a string';

$c->test_complex($object)
  ->isFaultEmpty()
  ->isType('', 'TestData')
  ->is('content', $object->content);

// test arraySimple
$array = array(1, 2, 3, 4);

$c->test_arraySimple($array)
  ->isFaultEmpty()
  ->isType('', 'ckGenericArray')
  ->isCount('', 2)
  ->is('0', 'a')
  ->is('1', 'b');

// test arrayComplex
$object = new TestData();
$object->content = 'a string';
$array = array($object);

$c->test_arrayComplex($array)
  ->isFaultEmpty()
  ->isType('', 'ckGenericArray')
  ->isCount('', 1)
  ->isType('0', 'TestData')
  ->is('0.content', $object->content);

// test arrayArray
$array = array(array('a'));

$c->test_arrayArray($array)
  ->isFaultEmpty()
  ->isType('', 'ckGenericArray')
  ->isCount('', 1)
  ->isType('0', 'ckGenericArray')
  ->isCount('0', 1)
  ->is('0.0', $array[0][0]);

// test beanObject
$array = array(new MyTestBean('data_0'), new MyTestBean('data_1'), new MyTestBean('data_2'));

$c->test_beanObject($array)
  ->isFaultEmpty()
  ->isType('', 'MyTestBean')
  ->is('data', 'ResultBeanData');
