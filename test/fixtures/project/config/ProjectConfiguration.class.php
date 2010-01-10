<?php
if (!isset($_SERVER['SYMFONY']))
{
  throw new RuntimeException('Could not find symfony core libraries.');
}

require_once $_SERVER['SYMFONY'].'/autoload/sfCoreAutoload.class.php';
sfCoreAutoload::register();

class ProjectConfiguration extends sfProjectConfiguration
{
  const PLUGIN_NAME = 'ckWebServicePlugin';

  public function setup()
  {
    $this->enablePlugins(self::PLUGIN_NAME);

    $this->setPluginPath(self::PLUGIN_NAME, dirname(__FILE__).'/../../../..');
  }
}
