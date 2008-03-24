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
 * ckSoapHandler is the standard handler for webservice requests.
 *
 * @package    ckWebServicePlugin
 * @subpackage controller
 * @author     Christian Kerl <christian-kerl@web.de>
 */
class ckSoapHandler
{
  /**
   * Standard constructor.
   *
   */
  public function __construct()
  {

  }

  /**
   * Redirects webservice calls back to the ckWebServiceController.
   *
   * @param string $method    The method name
   * @param array  $arguments The method arguments
   *
   * @return mixed The result of the called method
   */
  public function __call($method, $arguments)
  {
    if (sfConfig::get('sf_logging_enabled'))
    {
      sfContext::getInstance()->getLogger()->info(sprintf("{%s} SoapCall of function '%s'. Parameters: %s", __CLASS__, $method, str_replace("\n", '', var_export($arguments, true))));
    }

    //pass the function call back to our controller
    return call_user_func_array(array(sfContext::getInstance()->getController(), $method), $arguments);
  }
}