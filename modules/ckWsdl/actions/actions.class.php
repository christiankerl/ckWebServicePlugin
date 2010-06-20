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

class ckWsdlActions extends sfActions
{
  public function executeBind(sfWebRequest $request)
  {
    $service = $request->getParameter('service', false);

    $this->forward404Unless($service);

    $controller = $this->getControllerFile($service);
    $wsdl       = $this->getWsdlFile($service);

    $this->forward404Unless($controller && $wsdl);

    $controller = $this->getControllerUrl($request, $service);

    $this->binder = new ckWsdlEndpointLocationBinder($wsdl);
    $this->binder->bind($controller);

    $this->getResponse()->setContentType('application/wsdl+xml');
    $this->setLayout(false);

    return sfView::SUCCESS;
  }

  private function getControllerFile($service)
  {
    $file = sprintf('%s/%s.php', sfConfig::get('sf_web_dir'), $service);

    return file_exists($file) ? $file : false;
  }

  private function getControllerUrl(sfWebRequest $request, $service)
  {
    return sprintf('%s%s/%s.php', $request->getUriPrefix(), $request->getRelativeUrlRoot(), $service);
  }

  private function getWsdlFile($service)
  {
    $file = sprintf('%s/wsdl/%s.wsdl', sfConfig::get('sf_data_dir'), $service);

    return file_exists($file) ? $file : false;
  }
}