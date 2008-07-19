<?php

/**
 * This file is part of the ckWebServicePlugin
 * 
 * @package   ckWebServicePlugin
 * @author    Sven Lauritzen <the-pulse@gmx.net>
 * @copyright Copyright (c) 2008, Sven Laurtizen
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 * @version   SVN: $Id$
 */

/**
 * Mixin class for sfComponent, which adds the method isSoapRequest().
 *
 * @package    ckWebServicePlugin
 * @subpackage action
 * @author     Sven Lauritzen <the-pulse@gmx.net>
 */

class ckWebServiceComponent
{

	/**
   * Tell whether the current request is a SOAP request.
   * @return boolean
   */
  public function isSoapRequest() {
    $controller = sfContext::getInstance()->getController();
    return $controller instanceof ckWebServiceController;
  }

}
