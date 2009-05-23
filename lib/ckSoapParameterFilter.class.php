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
		/** first execute of this chain **/
		if($this->isFirstCall())
		{
		  $log_params = array();

			/** get request class from context **/
			$request = $this->getContext()->getRequest();

			/** get params from request **/
			$param = $request->getParameter('ck_web_service_plugin.param', null);

			/** get mapped params from mod config **/
			$map = sfConfig::get(sprintf('mod_%s_%s_parameter', strtolower($this->getContext()->getModuleName()), $this->getContext()->getActionName()));

			/** params and map is array **/
			if(is_array($param) && is_array($map))
			{
				/** pass mapped params **/
				for ($i = 0; $i < count($map); $i++)
				{
					/** empty param **/
					if ($param[$i] === '')
					{
						/** set to null **/
						$param[$i] = null;
					}

					/** map soap param to request class **/
					$request->setParameter($map[$i], ckObjectWrapper::unwrap($param[$i]));

					/** logging enabled **/
					if (sfConfig::get('sf_logging_enabled'))
					{
						/** append params **/
						$log_params[$map[$i]] = $param[$i];
					}
				}
			}

			/** logging enabled **/
			if (sfConfig::get('sf_logging_enabled'))
			{
				/** write logmessage **/
				//$this->getContext()->getEventDispatcher()->notify(new sfEvent($this, 'application.log', array(sprintf('Mapped soap parameters due request %s', str_replace("\n", '', var_export(null, true))))));
			}
		}

		/** execute next filter **/
		$filterChain->execute();
	}
}