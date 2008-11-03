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

abstract class ckAbstractResultAdapter
{
  public function getRenderMode()
  {
    return sfView::RENDER_NONE;
  }

  public function __construct($parameters = array())
  {

  }

  public abstract function getResult(sfAction $action);
}