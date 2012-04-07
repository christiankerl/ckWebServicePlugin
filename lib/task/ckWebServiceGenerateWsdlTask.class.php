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
class ckWebServiceGenerateWsdlTask extends sfBaseTask
{
  /**
   * The default environment.
   *
   * @var string
   */
  const DEFAULT_ENVIRONMENT = 'soap';

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

The wsdl file will be created in the [data/wsdl/|COMMENT] directory:

  [data/wsdl/%name%.wsdl|INFO]

This task also creates a front controller script in the [web/|COMMENT] directory:

  [web/%name%.php|INFO]

a custom soap handler class and corresponding base soap handler class, in the application's lib directory:

  [apps/%application%/lib/%name%Handler.class.php]

  [apps/%application%/lib/Base%name%Handler.class.php]

You can set the environment by using the [environment|COMMENT] option:

  [./symfony webservice:generate-wsdl frontend --environment=soap|INFO]

  or

  [./symfony webservice:generate-wsdl frontend -e=soap|INFO]

You can enable debugging for the controller by using the [enabledebug|COMMENT] option:

  [./symfony webservice:generate-wsdl frontend --enabledebug=on|INFO]

  or

  [./symfony webservice:generate-wsdl frontend -d=on|INFO]

EOF;

    $this->addArgument('application', sfCommandArgument::REQUIRED, 'The application name');
    $this->addArgument('name', sfCommandArgument::REQUIRED, 'The webservice name');
    $this->addArgument('url', sfCommandArgument::REQUIRED, 'The target namespace for the webservice types');

    $this->addOption('environment', 'e', sfCommandOption::PARAMETER_REQUIRED, 'The environment to use for webservice mode', self::DEFAULT_ENVIRONMENT);
    $this->addOption('enabledebug', 'd', sfCommandOption::PARAMETER_NONE, 'Enables debugging in generated controller');
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    $databaseManager = new sfDatabaseManager($this->configuration);

    if($this->isPropelPluginActive())
    {
      $this->loadPropelPeerClasses();
    }

    $app  = $arguments['application'];
    $env  = $options['environment'];
    $dbg  = $options['enabledebug'];
    $file = $arguments['name'];
    $url  = $arguments['url'];

    $this->buildControllerFile($file, $app, $env, $dbg);

    $gen = new ckWsdlGenerator(new ckWsdlGeneratorContext($file, $url, null, $env == self::DEFAULT_ENVIRONMENT));

    WSMethod::setCreateMethodNameCallback(array($this, 'generateWSMethodName'));

    $this->addClassMapping($app, $env);

    $handler_methods = array();

    foreach($this->getModules() as $module)
    {
      if($this->loadModuleClassFile($module))
      {
        $yml = $this->loadModuleConfigFile($module);

        if(!isset($yml[$env]) || !is_array($yml[$env]))
        {
          $yml[$env] = array();
        }

        $class = new ReflectionAnnotatedClass($module.'Actions');

        foreach($class->getMethods(ReflectionMethod::IS_PUBLIC) as $method)
        {
          try
          {
            extract($this->getModuleAndAction($method));
          }
          catch(InvalidArgumentException $e)
          {
            continue;
          }

          if(!$gen->addMethod($method))
          {
            continue;
          }

          $name = $method->getAnnotation('WSMethod')->getName();

          $result = isset($yml[$env][$action]['result']) ? $yml[$env][$action]['result'] : array('class' => 'ckPropertyResultAdapter', 'param' => array('property' => 'result'));

          $yml[$env][$action] = array('parameter'=>array(), 'result' => $result);
          $handler_methods[$name] = array('module' => $module, 'action' => $action, 'parameter' => array());

          foreach(ckDocBlockParser::parseParameters($method->getDocComment()) as $param)
          {
            $yml[$env][$action]['parameter'][] = $handler_methods[$name]['parameter'][] = $param['name'];
          }
        }

        if(!empty($yml[$env]))
        {
          $this->saveModuleConfigFile($module, $yml);
        }
      }
    }

    $this->buildHandlerFile($file);
    $this->buildBaseHandlerFile($file, $handler_methods);
    $this->buildWsdlFile($gen, $file);
  }

  protected function getModuleAndAction(ReflectionMethod $method)
  {
    $class  = $method->getDeclaringClass()->getName();
    $method = $method->getName();

    if(!ckString::endsWith($class, 'Actions') || !ckString::startsWith($method, 'execute') || strlen($method) <= 7)
    {
      throw new InvalidArgumentException();
    }

    return array('module' => substr($class, 0, -7), 'action' => ckString::lcfirst(substr($method, 7)));
  }

  public function addClassMapping($application, $env)
  {
    sfConfig::set('sf_environment', $env);
    $h = new sfDefineEnvironmentConfigHandler(array('prefix' => 'app_'));
    $config = $h->getConfiguration(array(sfConfig::get('sf_app_dir') . '/config/app.yml'));

    $ckConfig = isset($config['ck_web_service_plugin']) ? $config['ck_web_service_plugin'] : array();
    $options = isset($ckConfig['soap_options']) ? $ckConfig['soap_options']: array();
    $soap_headers = isset($ckConfig['soap_headers']) ? $ckConfig['soap_headers'] : array();

    if(!isset($options['classmap']) || !is_array($options['classmap']))
    {
      $options['classmap'] = array();
    }

    foreach($soap_headers as $header_name => $header_options)
    {
      if(isset($header_options['class']))
      {
        $options['classmap'][$header_name] = $header_options['class'];
      }
    }

    foreach ($options['classmap'] as $alias => $mapping)
    {
      ckXsdComplexType::create($mapping, $alias);
    }
  }

  public function generateWSMethodName(ReflectionMethod $method)
  {
    return implode('_', $this->getModuleAndAction($method));
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

  protected function getModules()
  {
    return sfFinder::type('directory')->name('*')->relative()->maxdepth(0)->in(sfConfig::get('sf_app_module_dir'));
  }

  protected function getModuleConfigFilePath($module)
  {
    return sfConfig::get('sf_app_module_dir').'/'.$module.'/config/module.yml';
  }

  protected function loadModuleClassFile($module)
  {
    $module_classfile = sfConfig::get('sf_app_module_dir').'/'.$module.'/actions/actions.class.php';

    return file_exists($module_classfile) && !preg_match('/class(.*)Actions(.*)extends(.*)auto/', file_get_contents($module_classfile)) ? require_once($module_classfile) : false;
  }

  protected function loadModuleConfigFile($module)
  {
    $module_configfile = $this->getModuleConfigFilePath($module);

    return file_exists($module_configfile) ? sfYaml::load($module_configfile) : array();
  }

  protected function saveModuleConfigFile($module, $config)
  {
    $module_configfile = $this->getModuleConfigFilePath($module);

    if(!file_exists($module_configfile))
    {
      $this->getFilesystem()->mkdirs(dirname($module_configfile));
      $this->getFilesystem()->touch($module_configfile);
    }

    file_put_contents($module_configfile, sfYaml::dump($config));
  }

  protected function buildControllerFile($controller, $application, $environment, $debug)
  {
    $template = sfConfig::get('sf_symfony_lib_dir').'/task/generator/skeleton/app/web/index.php';
    $pathname = sprintf('%s/%s.php', sfConfig::get('sf_web_dir'), $controller);

    $this->getFilesystem()->copy($template, $pathname);
    $this->getFilesystem()->replaceTokens($pathname, '##', '##', array(
      'IP_CHECK'    => '',
      'APP_NAME'    => $application,
      'ENVIRONMENT' => $environment,
      'IS_DEBUG'    => $debug ? 'true' : 'false',
    ));
  }

  protected function buildWsdlFile(ckWsdlGenerator $generator, $service_name)
  {
    $wsdl_dir = sprintf('%s/wsdl', sfConfig::get('sf_data_dir'));
    $wsdl_file = sprintf('%s/%s.wsdl', $wsdl_dir, $service_name);

    $this->getFilesystem()->mkdirs($wsdl_dir);
    $generator->save($wsdl_file);
    $this->logSection('file+', $wsdl_file);
  }

  protected function buildHandlerFile($handler_name)
  {
    $template = $this->getPluginDir().'/lib/task/skeleton/SoapHandler.class.php';
    $pathname = sprintf('%s/%sHandler.class.php', sfConfig::get('sf_app_lib_dir'), $handler_name);

    if(!file_exists($pathname))
    {
      $this->getFilesystem()->copy($template, $pathname);
      $this->getFilesystem()->replaceTokens($pathname, '##', '##', array(
        'HND_NAME'   => $handler_name
      ));
    }
  }

  protected function buildBaseHandlerFile($handler_name, $methods)
  {
    $template = $this->getPluginDir().'/lib/task/skeleton/BaseSoapHandler.class.php';
    $pathname = sprintf('%s/Base%sHandler.class.php', sfConfig::get('sf_app_lib_dir'), $handler_name);

    if(file_exists($pathname))
    {
      $this->getFilesystem()->remove($pathname);
    }

    $this->getFilesystem()->copy($template, $pathname);
    $this->getFilesystem()->replaceTokens($pathname, '##', '##', array(
      'HND_NAME'   => $handler_name,
      'HND_METHOD' => $this->buildHandlerMethods($methods)
    ));
  }

  /**
   * Generates soap handler methods.
   *
   * @param array $methods An array with the methods as keys and the corresponding options as values
   *
   * @return string The generated methods.
   */
  protected function buildHandlerMethods($methods)
  {
    $result = array();
    $prepend_dollar = create_function('&$in', '$in = "$".$in;');

    foreach($methods as $name => $params)
    {
      array_walk($params['parameter'], $prepend_dollar);

      $result[] = $this->replaceTokensInString($this->getHandlerMethodTemplate(), array(
      	'NAME'   => $name,
      	'MODULE' => $params['module'],
      	'ACTION' => $params['action'],
      	'PARAMS' => implode(', ', $params['parameter'])
      ), '##', '##');
    }

    return implode("\n\n", $result);
  }

  /**
   * Gets the template string for a soap handler method.
   *
   * @return string The template string
   */
  protected function getHandlerMethodTemplate()
  {
    return <<<EOF
  public function ##NAME##(##PARAMS##)
  {
    return sfContext::getInstance()->getController()->invokeSoapEnabledAction('##MODULE##', '##ACTION##', array(##PARAMS##));
  }
EOF;
  }

  /**
   * Replaces all tokens in a given string with the given values.
   *
   * @param string $str             The string in which the tokens should be replaced
   * @param array  $tokens          An array with the tokens as keys and the corresponding replacment values as values
   * @param string $start_delimiter The string, which marks the start of a token
   * @param string $end_delimiter   The string, which marks the end of a token
   *
   * @return string The string with the tokens replaced
   */
  protected function replaceTokensInString($str, $tokens, $start_delimiter, $end_delimiter)
  {
    $result = $str;

    foreach($tokens as $token => $value)
    {
      $result = str_replace($start_delimiter.$token.$end_delimiter, $value, $result);
    }

    return $result;
  }

  protected function isPropelPluginActive()
  {
    return $this->isPluginActive('sfPropelPlugin');
  }

  protected function loadPropelPeerClasses()
  {
    foreach($this->configuration->getModelDirs() as $model_dir)
    {
      foreach(glob($model_dir.DIRECTORY_SEPARATOR.'*Peer.php') as $peer_class_file)
      {
        require_once($peer_class_file);
      }
    }
  }

  protected function isDoctrinePluginActive()
  {
    return $this->isPluginActive('sfDoctrinePlugin');
  }

  protected function isPluginActive($plugin)
  {
    foreach($this->configuration->getPluginPaths() as $plugin_path)
    {
      if(ckString::endsWith($plugin_path, $plugin))
      {
        return true;
      }
    }

    return false;
  }
}
