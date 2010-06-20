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

if (!isset($_SERVER['SYMFONY']))
{
  throw new RuntimeException('Could not find symfony core libraries.');
}
require_once($_SERVER['SYMFONY'].'/vendor/lime/lime.php');
require_once($_SERVER['SYMFONY'].'/util/sfFinder.class.php');

$h = new lime_harness(array(
  'force_colors' => true
));
$h->base_dir = realpath(dirname(__FILE__).'/..');

// functional tests
$h->register_glob($h->base_dir.'/functional/*Test.php');

$c = new lime_coverage($h);
$c->extension = '.class.php';
$c->verbose = false;
$c->base_dir = realpath(dirname(__FILE__).'/../../lib');

$finder = sfFinder::type('file')->name('*.php')->prune('vendor')->prune('test')->prune('data')->prune('skeleton');
$c->register($finder->in($c->base_dir));
$c->run();