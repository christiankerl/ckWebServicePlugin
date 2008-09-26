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

/**
 * ckWebServiceGenerateWsdlTask generates a wsdl file and webservice endpoint.
 *
 * @package    ckWebServicePlugin
 * @subpackage task
 * @author     Christian Kerl <christian-kerl@web.de>
 */
class ckWebServiceGenerateWsdlTask extends sfGeneratorBaseTask
{
  const CONTROLLER_TEMPLATE_PATH = '/task/generator/skeleton/app/web/index.php';

  const HANDLER_TEMPLATE_PATH    = '/lib/task/skeleton/SoapHandler.class.php';

  protected $handler_method_template = '';

  /**
   * @see sfTask
   */
  protected function configure()
  {
    $this->namespace        = 'webservice';
    $this->name             = 'generate-wsdl';
    $this->briefDescription = 'Generate a wsdl file from your marked module actions to expose them as a webservice api';
    $this->detailedDescription = <<<EOF
The [webservice:generate-wsdl|INFO] task generates a wsdl file from your marked module actions to expose them as a webservice api.
Call it with:

  [./symfony webservice:generate-wsdl|INFO]

This task also creates a front controller script in the [web/|COMMENT] directory:

  [web/%application%_%environment%.php|INFO]

You can set the environment by using the [environment|COMMENT] option:

  [./symfony webservice:generate-wsdl frontend --environment=soap|INFO]

  or

  [./symfony webservice:generate-wsdl frontend -e=soap|INFO]

You can enable debugging for the controller by using the [debug|COMMENT] option:

  [./symfony webservice:generate-wsdl frontend --debug=on|INFO]

  or

  [./symfony webservice:generate-wsdl frontend -d=on|INFO]

EOF;

    $this->handler_method_template = <<<EOF

  public function ##NAME##(##PARAMS##)
  {
  	return sfContext::getInstance()->getController()->invokeSoapEnabledAction('##MODULE##', '##ACTION##', array(##PARAMS##));
  }

EOF;

    $this->addArgument('application', sfCommandArgument::REQUIRED, 'The application name');
    $this->addArgument('name', sfCommandArgument::REQUIRED, 'The webservice name');
    $this->addArgument('url', sfCommandArgument::REQUIRED, 'The webservice url base');

    $this->addOption('environment', 'e', sfCommandOption::PARAMETER_REQUIRED, 'The environment to use for webservice mode', 'soap');
    $this->addOption('debug', 'd', sfCommandOption::PARAMETER_NONE, 'Enables debugging in generated controller');
  }

  /**
   * @see sfTask
   */
  protected function doRun(sfCommandManager $commandManager, $options)
  {
    $this->process($commandManager, $options);

    $this->checkProjectExists();

    $app = $commandManager->getArgumentValue('application');
    $this->checkAppExists($app);
    sfConfig::set('sf_app_module_dir', sprintf('%s/../../apps/%s/modules', $this->getPluginDir(), $app));
    sfConfig::set('sf_app_lib_dir', sprintf('%s/../../apps/%s/lib', $this->getPluginDir(), $app));

    $this->registerLibDirs();

    return $this->execute($commandManager->getArgumentValues(), $commandManager->getOptionValues());
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    $app  = $arguments['application'];
    $env  = $options['environment'];
    $dbg  = $options['debug'];
    $file = $arguments['name'];
    $url  = $arguments['url'];
    $url  = ckString::endsWith($url, '/') ? $url : $url.'/';

    $controller_name = $file.'.php';
    $controller_path = sprintf('%s/%s', sfConfig::get('sf_web_dir'), $controller_name);

    $this->getFilesystem()->remove($controller_path);
    $this->getFilesystem()->copy(sfConfig::get('sf_symfony_lib_dir').self::CONTROLLER_TEMPLATE_PATH, $controller_path);

    $this->getFilesystem()->replaceTokens($controller_path, '##', '##', array(
      'APP_NAME'    => $app,
      'ENVIRONMENT' => $env,
      'IS_DEBUG'    => $dbg ? 'true' : 'false',
    ));

    $finder = sfFinder::type('directory')->name('*')->relative()->maxdepth(0);

    $gen = new ckWsdlGenerator($file, $url, $url.$controller_name);
    $gen->setCheckEnablement(true);

    $use_handler  = true;
    $handler_file = sfConfig::get('sf_app_lib_dir').'/'.$file.'Handler.class.php';
    $handler_map  = array();

    foreach($finder->in(sfConfig::get('sf_app_module_dir')) as $module_dir)
    {
      // proposed by Nicolas Martin to avoid problems with 'inited' modules
      if(!preg_match('/class(.*)Actions(.*)extends(.*)auto/', file_get_contents(sfConfig::get('sf_app_module_dir').'/'.$module_dir.'/actions/actions.class.php')) && file_exists(sfConfig::get('sf_app_module_dir').'/'.$module_dir.'/actions/actions.class.php'))
      {
        require_once(sfConfig::get('sf_app_module_dir').'/'.$module_dir.'/actions/actions.class.php');

        $class = new ReflectionClass($module_dir.'Actions');

        $module_config = sfConfig::get('sf_app_module_dir').'/'.$module_dir.'/config/module.yml';

        $this->getFilesystem()->mkdirs(dirname($module_config));

        if(!file_exists($module_config))
        {
          $this->getFilesystem()->touch($module_config);
        }

        $yml = sfYaml::load($module_config);

        if(!isset($yml[$env]) || !is_array($yml[$env]))
        {
          $yml[$env] = array();
        }

        foreach($class->getMethods() as $method)
        {
          $name = $method->getName();

          if(ckString::startsWith($name, 'execute') && strlen($name)>7)
          {
            $action = ckString::lcfirst(substr($name, 7));
            $name = $module_dir.'_'.$action;

            $wsname = ckDocBlockParser::parseMethod($method->getDocComment());

            if(!empty($wsname) && $use_handler)
            {
              $name = $wsname['name'];
            }

            if(!$gen->addMethod($name, $method))
            {
              $yml[$env][$action] = array('enable'=>false);

              continue;
            }

            $yml[$env][$action] = array('enable'=>true, 'parameter'=>array(), 'result'=>null, 'render'=>false);
            $handler_map[$name] = array('module' => $module_dir, 'action' => $action, 'parameter' => array());

            foreach(ckDocBlockParser::parseParameters($method->getDocComment()) as $param)
            {
              $yml[$env][$action]['parameter'][] = $param['name'];
              $handler_map[$name]['parameter'][] = '$'.$param['name'];
            }


          }
        }

        // only save if we added something to the configuration
        if(!empty($yml[$env]))
        {
          file_put_contents($module_config, sfYaml::dump($yml));
        }
      }
    }

    $this->getFilesystem()->remove($handler_file);
    $this->getFilesystem()->copy($this->getPluginDir().self::HANDLER_TEMPLATE_PATH, $handler_file);

    $this->getFilesystem()->replaceTokens($handler_file, '##', '##', array(
      'HND_NAME'   => $file,
      'HND_METHOD' => $this->buildHandlerMethods($handler_map)
    ));

    $file = sprintf('%s/%s.wsdl', sfConfig::get('sf_web_dir'), $file);
    $gen->save($file);
    $this->logSection('file+', $file);
  }

  /**
   * Returns the plugin root path.
   *
   * @return string The plugin root path
   */
  protected function getPluginDir()
  {
    return dirname(__FILE__).'/../..';
  }

  /**
   * Registers required class files for autoloading.
   */
  protected function registerLibDirs()
  {
    $autoload = sfSimpleAutoload::getInstance();
    $autoload->addDirectory(sfConfig::get('sf_lib_dir'));
    $autoload->addDirectory(sfConfig::get('sf_app_lib_dir'));
    $autoload->addDirectory($this->getPluginDir().'/lib/vendor/ckWsdlGenerator');
    $autoload->addDirectory($this->getPluginDir().'/lib/util');
  }

  protected function buildHandlerMethods($methods)
  {
    $result = '';

    foreach($methods as $name => $params)
    {
      $result .= $this->replaceTokens($this->handler_method_template, '##', '##', array(
      	'NAME'   => $name,
      	'MODULE' => $params['module'],
      	'ACTION' => $params['action'],
      	'PARAMS' => implode(', ', $params['parameter'])
      ));
    }

    return $result;
  }

  protected function replaceTokens($str, $start_delimiter, $end_delimiter, $tokens)
  {
    $result = $str;

    foreach($tokens as $token => $value)
    {
      $result = str_replace($start_delimiter.$token.$end_delimiter, $value, $result);
    }

    return $result;
  }
}