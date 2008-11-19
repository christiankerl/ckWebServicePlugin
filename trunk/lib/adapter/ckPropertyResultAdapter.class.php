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
 * ckPropertyResultAdapter gets the result of an action from a property.
 *
 * @package    ckWebServicePlugin
 * @subpackage adapter
 * @author     Christian Kerl <christian-kerl@web.de>
 */
class ckPropertyResultAdapter extends ckAbstractResultAdapter
{
  /**
   * The name of the property used as result by default.
   */
  const DEFAULT_RESULT_PROPERTY = 'result';

  /**
   * The name of the property used as result.
   *
   * @var string
   */
  protected $resultProperty;

  /**
   * Gets the name of the property used as result.
   *
   * @return string The name of the property
   */
  public function getResultProperty()
  {
    return $this->resultMember;
  }

  /**
   * Constructor initializing the result adapter with a given array of adapter specific parameters.
   * Possible parameters are:
   *   'property': configures the result property name
   *
   * @param array $parameters An array of adapter specific parameters
   */
  public function __construct($parameters = array())
  {
    $this->resultProperty = isset($parameter['property']) ? $parameter['property'] : self::DEFAULT_RESULT_PROPERTY;
  }

  /**
   * @see ckAbstractResultAdapter::getResult()
   */
  public function getResult(sfAction $action)
  {
    $result = null;
    $vars   = $action->getVarHolder()->getAll();

    if(isset($vars[$this->getResultProperty()]))
    {
      $result = $vars[$this->getResultProperty()];
    }
    else if(count($vars) == 1)
    {
      reset($vars);
      $result = current($vars);
    }

    return $result;
  }
}