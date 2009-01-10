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

$testDir = dirname(__FILE__).'/..';

$h = new lime_harness(new lime_output_color());
$h->base_dir = $testDir;

$h->register($testDir.'/unit');
$h->register($testDir.'/functional');

$h->run();
