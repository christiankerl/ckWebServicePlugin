<?php
/**
 * This file is part of the ckWebServicePlugin
 *
 * @package   ckWebServicePlugin
 * @author    Christian Kerl <christian-kerl@web.de>
 * @copyright Copyright (c) 2010, Christian Kerl
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 * @version   SVN: $Id$
 */

include(dirname(__FILE__).'/../bootstrap/unit.php');

// implementation which doesn't initialize on ctor call
// mimics how php_soap extension instantiates objects
class NonInitializingMock extends ckGenericArray
{
  public function __construct() {}
}

$empty = new ckGenericArray();
$array = new ckGenericArray(array(1, 2, 3, 4));
$complex = new ckGenericArray(array(
  new ckGenericArray(),
  new ckGenericArray(array(1, 2, 3, 4)),
  1, 2, 3, 4
));
$phpSoapExtMock = new NonInitializingMock();

$t = new lime_test(28);

$t->comment('Countable::count()');
$t->is(count($empty), 0);
$t->is(count($array), 4);

$t->comment('ArrayAccess::offsetExists()');

$t->ok(!isset($empty[0]));
$t->ok(isset($array[0]));

$t->comment('ArrayAccess::offsetGet()');
$t->is($array[0], 1);
$t->is($array[1], 2);

$t->comment('ArrayAccess::offsetSet()');

$empty[0] = 1;
$empty['test'] = 1;

$t->is($empty[0], 1);
$t->is($empty['test'], 1);

$t->comment('ArrayAccess::offsetUnset()');

unset($empty[0]);
unset($empty['test']);

$t->ok(!isset($empty[0]));
$t->ok(!isset($empty['test']));

$t->comment('IteratorAggregate::getIterator()');

$runs = 0;

foreach($array as $key => $value)
{
  $t->is($key, $runs);
  $t->is($value, $runs + 1);

  $runs++;
}

$t->is($runs, 4);

$t->comment('ckGenericArray::toArray()');

$converted = $complex->toArray();

$t->isa_ok($converted, 'array');
$t->isa_ok($converted[0], 'array');
$t->isa_ok($converted[1], 'array');
$t->is_deeply($converted, array(array(), array(1, 2, 3, 4), 1, 2, 3, 4));

$t->comment('ckGenericArray::__get()');

$t->is_deeply($array->item, array(1, 2, 3, 4));
$t->is($array->unknownProperty, null);

$t->comment('ckGenericArray::__set()');

$phpSoapExtMock->item = 'test';

$t->is(count($phpSoapExtMock), 1);
$t->is($phpSoapExtMock[0], 'test');

$phpSoapExtMock->item = 'none';

$t->is($phpSoapExtMock[0], 'test');

