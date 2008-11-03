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

class ckRenderResultAdapter extends ckAbstractResultAdapter
{
  public function getRenderMode()
  {
    return sfView::RENDER_VAR;
  }

  public function __construct($parameters = array())
  {

  }

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