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
	/**
   * Executes this filter.
   *
   * @param sfFilterChain $filterChain A sfFilterChain instance
   */
	public function execute($filterChain)
	{
		if($this->isFirstCall())
		{
			$request = $this->getContext()->getRequest();
			$param   = $request->getParameter('ck_web_service_plugin.param', null);
			$map     = sfConfig::get(sprintf('mod_%s_%s_parameter', strtolower($this->getContext()->getModuleName()), $this->getContext()->getActionName()));

			if(is_array($param) && is_array($map))
			{
				for ($i = 0; $i < count($map); $i++)
				{
					$request->setParameter($map[$i], !empty($param[$i]) ? $param[$i] : null);

					if (sfConfig::get('sf_logging_enabled'))
					{
						$params[$map[$i]] = $param[$i];
					}
				}
			}

			if (sfConfig::get('sf_logging_enabled'))
			{
				$this->getContext()->getEventDispatcher()->notify(new sfEvent($this, 'application.log', array(sprintf('Mapped soap parameters due request %s', str_replace("\n", '', var_export($params, true))))));
			}
		}

		$filterChain->execute();
	}
}