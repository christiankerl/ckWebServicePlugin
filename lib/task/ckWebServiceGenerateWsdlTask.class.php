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
    $this->registerPluginLibs();
    
    $this->process($commandManager, $options);

    $this->checkProjectExists();
    
    $app = $commandManager->getArgumentValue('application');
    $this->checkAppExists($app);
    sfConfig::set('sf_app_module_dir', sprintf('%s/../../apps/%s/modules', $this->getPluginDir(), $app));
    
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
    $name = $arguments['name'];
    $url  = $arguments['url'];
    $url  = ckString::endsWith($url, '/') ? $url : $url.'/';

    $controller_name = $name.'.php';
    $controller_path = sprintf('%s/%s', sfConfig::get('sf_web_dir'), $controller_name);

    $this->getFilesystem()->remove($controller_path);
    $this->getFilesystem()->copy(sfConfig::get('sf_symfony_lib_dir').self::CONTROLLER_TEMPLATE_PATH, $controller_path);

    $this->getFilesystem()->replaceTokens($controller_path, '##', '##', array(
      'APP_NAME'    => $app,
      'ENVIRONMENT' => $env,
      'IS_DEBUG'    => $dbg ? 'true' : 'false',
    ));
    
    $finder = sfFinder::type('directory')->name('*')->relative()->maxdepth(0);

    $ws_def = new WsdlDefinition();
    $ws_def->setDefinitionName($name);
    $ws_def->setWsdlFileName(sprintf('%s/%s.wsdl', sfConfig::get('sf_web_dir'), $name));
    $ws_def->setNameSpace($url);
    $ws_def->setEndPoint($url.$controller_name);

    $ws_write = new WsdlWriter($ws_def);

    $methods = array();

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

            $param_return = $this->parseMethodCommentBlock($method->getDocComment());

            if($param_return == null)
            {
              $yml[$env][$action] = array('enable'=>false);
              
              continue;
            }

            $yml[$env][$action] = array('enable'=>true, 'parameter'=>array(), 'result'=>null, 'render'=>false);

            $ws_method = new WsdlMethod();
            $ws_method->setName($name);

            if(!is_null($param_return['return'] && !empty($param_return['return'])))
            {
              $ws_method->setReturn($param_return['return']['type'], $param_return['return']['desc']);
            }

            foreach($param_return['param'] as $param)
            {
              $yml[$env][$action]['parameter'][] = $param['name'];

              $ws_method->addParameter($param['type'], $param['name'], $param['desc']);
            }

            $methods[] = $ws_method;

            $ws_write->addMethod($ws_method);
          }
        }

        // only save if we added something to the configuration
        if(!empty($yml[$env]))
        {
          file_put_contents($module_config, sfYaml::dump($yml));
        }

      }
    }

    $complexTypes = WsdlType::getComplexTypes($methods);

    foreach ($complexTypes as &$complexType)
    {
      $ws_write->addComplexType($complexType);
    }

    $ws_write->doCreateWsdl();
    $ws_write->save();
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
   *
   */
  protected function registerPluginLibs()
  {
    $autoload = sfSimpleAutoload::getInstance();
    $autoload->addDirectory($this->getPluginDir().'/lib/vendor/wsdl');
    $autoload->addDirectory($this->getPluginDir().'/lib/util');    
  }
  
  /**
   * Parses parameter and return types, names and descriptions from a method comment block, if the tag @ws-enable is found.
   *
   * @param  string $text A method comment block
   * @return array        An array containing parameter and return types, names and descriptions
   */
  protected function parseMethodCommentBlock($text)
  {
    $result = array('param'=>array(), 'return'=>null);
    $lines = explode("\n", $text);

    $enable = false;

    foreach($lines as $line)
    {
      $line = trim($line);

      if(ckString::startsWith($line, '* ') && substr($line, 2, 1) == '@')
      {
        $parts = explode(' ', substr($line, 3), 4);

        if($parts[0] == 'ws-enable')
        {
          $enable = true;
        }
        else if($parts[0] == 'param' && count($parts)>=3)
        {
          $desc = isset($parts[3]) ? $parts[3] : '';
          $result[$parts[0]][] = array('name'=>substr($parts[2], 1), 'type'=>$parts[1], 'desc'=>$desc);
        }
        else if($parts[0] == 'return' && count($parts)>=2)
        {
          $desc = isset($parts[2]) ? $parts[2] : '';
          $desc .= isset($parts[3]) ? ' '.$parts[3] : '';
          $result[$parts[0]] = array('type'=>$parts[1], 'desc'=>$desc);
        }
      }
    }

    if($enable)
    {
      return $result;
    }
    else
    {
      return null;
    }
  }
}