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

class ckMemberResultAdapter extends ckAbstractResultAdapter
{
  const DEFAULT_RESULT_MEMBER = 'result';

  protected $resultMember;

  public function getResultMember()
  {
    return $this->resultMember;
  }

  public function __construct($parameters = array())
  {
    $this->resultMember = isset($parameter['member']) ? $parameter['member'] : DEFAULT_RESULT_MEMBER;
  }

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