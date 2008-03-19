<?php
/**
 * This file is part of the ckWebServicePlugin
 *
 * @package   ckWebServicePlugin
 * @author    Christian Kerl <christian-kerl@web.de>
 * @copyright Copyright (c) 2008, Christian Kerl
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
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
      $param   = $request->getParameter('param', null, 'ckWebServicePlugin');
      $map     = sfConfig::get('mod_'.$this->getContext()->getModuleName().'_soap_parameter_map_'.$this->getContext()->getActionName());

      if(is_array($param) && is_array($map))
      {
        $a = 0;
        foreach($param as $index => $value)
        {
          if(count($map) > $a)
          {
            $request->setParameter($map[$a], $value);
            $a++;
          }
        }
      }
    }

    $filterChain->execute();
  }
}