<?php

$app = 'frontend';

include_once(dirname(__FILE__).'/../bootstrap/functional.php');

class BeanObject
{

}

$test = new lime_test(null, new lime_output_color());

$beanAdapter = new ckGenericObjectAdapter_BeanObject();

$test->ok(file_exists(sfConfig::get('sf_cache_dir').'/ckWebServicePlugin/ckGenericObjectAdapter/BeanObject.php'));
$test->isa_ok($beanAdapter, 'ckGenericObjectAdapter_BeanObject');
$test->ok($beanAdapter instanceof ckGenericObjectAdapter);
$test->isa_ok($beanAdapter->getObject(), 'BeanObject');