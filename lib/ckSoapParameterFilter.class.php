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
 * ckSoapParameterFilter provides mapping of soap parameters to request parameters.
 *
 * @package    ckWebServicePlugin
 * @subpackage filter
 * @author     Christian Kerl <christian-kerl@web.de>
 */
class ckSoapParameterFilter extends sfFilter
{
  const PARAMETER_KEY = 'ck_web_service_plugin.param';

  /**
   * Executes this filter.
   *
   * @param sfFilterChain $filterChain A sfFilterChain instance
   */
  public function execute($filterChain)
  {
    if($this->isFirstCall())
    {
      $context = $this->getContext();
      $request = $context->getRequest();

      // get parameters from request
      $param = $request->getParameter(self::PARAMETER_KEY, null);

      // get parameter map from module config
      $map = sfConfig::get(sprintf('mod_%s_%s_parameter', strtolower($context->getModuleName()), $context->getActionName()));

      if(is_array($param) && is_array($map))
      {
        // map parameters to the names stored in the map
        for ($i = 0, $count = count($map); $i < $count; $i++)
        {
          if ($param[$i] === '')
          {
            $param[$i] = null;
          }

          $request->setParameter($map[$i], ckObjectWrapper::unwrap($param[$i]));
        }
      }

      if (sfConfig::get('sf_logging_enabled'))
      {
        $context->getEventDispatcher()->notify(new sfEvent($this, 'application.log', array('Mapped soap parameters to request parameters.')));
      }
    }

    $filterChain->execute();
  }
}