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

if (!isset($_SERVER['SYMFONY']))
{
  throw new RuntimeException('Could not find symfony core libraries.');
}

include($_SERVER['SYMFONY'].'/../test/bootstrap/unit.php');

include($_SERVER['SYMFONY'].'/autoload/sfSimpleAutoload.class.php');

$autoload = sfSimpleAutoload::getInstance(sys_get_temp_dir().DIRECTORY_SEPARATOR.sprintf('ckWebServicePluginUnitTestAutoload%s.data', md5(__FILE__)));
$autoload->addDirectory(realpath(dirname(__FILE__).'/../../lib'));
$autoload->register();