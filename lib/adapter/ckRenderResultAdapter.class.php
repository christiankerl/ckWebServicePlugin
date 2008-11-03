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
 * ckRenderResultAdapter gets the result of an action by executing the standard rendering pipeline
 * and saving the result to a variable.
 *
 * @package    ckWebServicePlugin
 * @subpackage adapter
 * @author     Christian Kerl <christian-kerl@web.de>
 */
class ckRenderResultAdapter extends ckAbstractResultAdapter
{
  /**
   * @see ckAbstractResultAdapter::getRenderMode()
   */
  public function getRenderMode()
  {
    return sfView::RENDER_VAR;
  }

  /**
   * @see ckAbstractResultAdapter::__construct()
   */
  public function __construct($parameters = array())
  {

  }

  /**
   * @see ckAbstractResultAdapter::getResult()
   */
  public function getResult(sfAction $action)
  {
    $lastStackEntry = $action->getContext()->getActionStack()->getLastEntry();

    if($lastStackEntry->getActionInstance() !== $action)
    {
      throw new sfRenderException();
    }

    return $lastStackEntry->getPresentation();
  }
}