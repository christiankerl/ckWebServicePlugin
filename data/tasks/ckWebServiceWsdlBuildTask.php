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

pake_desc('build wsdl file from marked classes');
pake_task('wsdl-build', 'app_exists');

function run_wsdl_build($task, $args)
{
  if(count($args) < 3)
  {
    throw new Exception('you must provide the webservice name');
  }

  if(count($args) < 4)
  {
    throw new Exception('you must provide the webservice url');
  }

  $last_index = count($args)-1;

  $sf_app = $args[0];
  $sf_env = $args[1];
  $ws_name = $args[$last_index-1];
  unset($args[$last_index-1]);
  $ws_url = $args[$last_index];
  unset($args[$last_index]);

  $sf_controller = isset($args[2]) ? $args[2] : $sf_app.'_'.$sf_env;

  run_init_controller($task, $args);

  sfConfig::set('sf_app_dir', sfConfig::get('sf_root_dir').'/apps/'.$sf_app);
  sfConfig::set('sf_app_lib_dir', sfConfig::get('sf_app_dir').'/lib');
  _register_lib_dirs();

  $root_modules_dir = sfConfig::get('sf_root_dir').'/apps/'.$sf_app.'/modules';
  $finder = pakeFinder::type('directory')->name('*')->relative()->maxdepth(0);

  $gen = new ckWsdlGenerator($ws_name, $ws_url, $ws_url.$sf_controller.'.php');
  $gen->setCheckEnablement(true);

  foreach($finder->in($root_modules_dir) as $module_dir)
  {
    //proposed by Nicolas Martin to avoid problems with 'inited' modules
    if(!preg_match('/class(.*)Actions(.*)extends(.*)auto/', file_get_contents($root_modules_dir.'/'.$module_dir.'/actions/actions.class.php')) && file_exists($root_modules_dir.'/'.$module_dir.'/actions/actions.class.php'))
    {
      require_once($root_modules_dir.'/'.$module_dir.'/actions/actions.class.php');

      $class = new ReflectionClass($module_dir.'Actions');

      $module_config = $root_modules_dir.'/'.$module_dir.'/config/module.yml';

      if(file_exists($module_config))
      {
        $yml = sfYaml::load($module_config);
      }
      else
      {
        $yml = array();
      }

      if(!isset($yml[$sf_env]) || !is_array($yml[$sf_env]))
      {
        $yml[$sf_env] = array();
      }

      foreach($class->getMethods() as $method)
      {
        $name = $method->getName();

        if(ckString::startsWith($name, 'execute') && strlen($name)>7)
        {
          $action = ckString::lcfirst(substr($name, 7));
          $name = $module_dir.'_'.$action;

          if(!$gen->addMethod($name, $method))
          {
            $yml[$sf_env][$action] = array('enable' => false);

            continue;
          }

          pake_echo_action('method+', $name);

          $yml[$sf_env][$action] = array('enable'=>true, 'parameter'=>array(), 'result'=>null, 'render'=>false);

          foreach(ckDocBlockParser::parseParameters($method->getDocComment()) as $param)
          {
            $yml[$sf_env][$action]['parameter'][] = $param['name'];
          }
        }
      }

      //only save if we added something to the configuration
      if(!empty($yml[$sf_env]))
      {
        pake_echo_action('file+', $module_config);
        file_put_contents($module_config, sfYaml::dump($yml));
      }
    }
  }

  $file = sprintf('%s/web/%s.wsdl', sfConfig::get('sf_root_dir'), $ws_name);
  $def->save($file);
  pake_echo_action('file+', $file);
}

function _register_lib_dirs()
{
  //hack to init simpleAutoLoader
  __autoload('sfConfig');
  simpleAutoLoader::register(sfConfig::get('sf_lib_dir'), '.class.php');
  simpleAutoLoader::register(sfConfig::get('sf_app_lib_dir'), '.class.php');
  simpleAutoLoader::register(sfConfig::get('sf_root_dir').'/plugins/modules/*/actions', '.class.php');
  simpleAutoLoader::register(sfConfig::get('sf_app_dir').'/modules/*/actions', '.class.php');
  simpleAutoLoader::register(sfConfig::get('sf_app_dir').'/modules/*/lib', '.class.php');

  $finder = pakeFinder::type('directory')->name('*');

  foreach($finder->in(sfConfig::get('sf_lib_dir')) as $dir)
  {
    simpleAutoLoader::register($dir, '.php');
  }

  foreach($finder->in(sfConfig::get('sf_app_lib_dir')) as $dir)
  {
    simpleAutoLoader::register($dir, '.php');
  }
}