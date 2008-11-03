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
 * ckMemberResultAdapter gets the result of an action from a member variable.
 *
 * @package    ckWebServicePlugin
 * @subpackage adapter
 * @author     Christian Kerl <christian-kerl@web.de>
 */
class ckMemberResultAdapter extends ckAbstractResultAdapter
{
  /**
   * The name of the member variable used as result by default.
   */
  const DEFAULT_RESULT_MEMBER = 'result';

  /**
   * The name of the member variable used as result.
   *
   * @var string
   */
  protected $resultMember;

  /**
   * Gets the name of the member variable used as result.
   *
   * @return string A name of the member variable
   */
  public function getResultMember()
  {
    return $this->resultMember;
  }

  /**
   * Constructor initializing the result adapter with a given array of adapter specific parameters.
   * Possible parameters are:
   *   'member': configures the result member name
   *
   * @param array $parameters An array of adapter specific parameters
   */
  public function __construct($parameters = array())
  {
    $this->resultMember = isset($parameter['member']) ? $parameter['member'] : DEFAULT_RESULT_MEMBER;
  }

  /**
   * @see ckAbstractResultAdapter::getResult()
   */
  public function getResult(sfAction $action)
  {
    $result = null;
    $vars   = $action->getVarHolder()->getAll();

    if(isset($vars[$this->getResultMember()]))
    {
      $result = $vars[$this->getResultMember()];
    }
    else if(count($vars) == 1)
    {
      reset($vars);
      $result = current($vars);
    }

    return $result;
  }
}