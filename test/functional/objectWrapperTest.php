<?php

$app = 'frontend';

include_once(dirname(__FILE__).'/../bootstrap/functional.php');

class StdObject
{
  public function __construct($data)
  {
    $this->data = $data;
  }

  /**
   * @var string
   */
  public $data;
}

/**
 * @PropertyStrategy('ckBeanPropertyStrategy')
 */
class TreeBeanObject
{
  private $_parent;

  public function __construct($parent)
  {
    $this->_parent = $parent;
  }

  /**
   * @return TreeBeanObject
   */
  public function getParent()
  {
    return $this->_parent;
  }

  public function setParent($parent)
  {
    $this->_parent = $parent;
  }
}

$test = new lime_test(null, new lime_output_color());

$test->is(ckObjectWrapper::wrap(null), null);
$test->is(ckObjectWrapper::unwrap(null), null);

$test->is(ckObjectWrapper::wrap(10), 10);
$test->is(ckObjectWrapper::unwrap(10), 10);

$test->is(ckObjectWrapper::wrap('str'), 'str');
$test->is(ckObjectWrapper::unwrap('str'), 'str');

$std        = new StdObject('testData');
$wrappedStd = ckObjectWrapper::wrap($std);

$test->isa_ok($wrappedStd, 'ckGenericObjectAdapter');
$test->is($wrappedStd->getObject(), $std);
$test->is(ckObjectWrapper::unwrap($wrappedStd), $std);

$wrappedWrappedStd = ckObjectWrapper::wrap($wrappedStd);

$test->isa_ok($wrappedWrappedStd, 'ckGenericObjectAdapter');
$test->is($wrappedWrappedStd, $wrappedStd);
$test->is($wrappedWrappedStd->getObject(), $std);

$tbRoot   = new TreeBeanObject(null);
$tbRoot->setParent($tbRoot);
$tbChild1 = new TreeBeanObject($tbRoot);
$tbChild2 = new TreeBeanObject($tbChild1);

$wrappedTbChild2 = ckObjectWrapper::wrap($tbChild2);

$test->isa_ok($wrappedTbChild2, 'ckGenericObjectAdapter');
$test->isa_ok($wrappedTbChild2->parent, 'ckGenericObjectAdapter');
$test->isa_ok($wrappedTbChild2->parent->parent, 'ckGenericObjectAdapter');

$wrappedTbRoot = $wrappedTbChild2->parent->parent;

$test->is($wrappedTbRoot->parent->getObject(), $wrappedTbRoot->getObject());
$test->is($wrappedTbRoot->parent, $wrappedTbRoot);
