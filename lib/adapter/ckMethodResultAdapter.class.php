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
 * ckMethodResultAdapter gets the result of an action from a method.
 *
 * @package    ckWebServicePlugin
 * @subpackage adapter
 * @author     Christian Kerl <christian-kerl@web.de>
 */
class ckMethodResultAdapter extends ckAbstractMemberResultAdapter
{
  /**
   * The name of the method, which's return value is used as result.
   *
   * @var string
   */
  protected $resultMethod;

  /**
   * Gets name of the method, which's return value is used as result.
   *
   * @return string The name of the method
   */
  public function getResultMethod()
  {
    return $this->resultMethod;
  }

  /**
   * Constructor initializing the result adapter with a given array of adapter specific parameters.
   * Possible parameters are:
   *   'method': configures the result method name
   *
   * @param array $parameters An array of adapter specific parameters
   */
  public function __construct($parameters = array())
  {
    if(!isset($parameters['method']))
    {
      throw new sfConfigurationException('The \'method\' parameter has to be specified.');
    }

    $this->resultMethod = $parameters['method'];

    parent::__construct($parameters);
  }

  /**
   * @see ckAbstractResultAdapter::doGetResult()
   */
  protected function doGetResult(sfAction $action)
  {
    return call_user_func_array(array($action, $this->getResultMethod()), array());
  }
}