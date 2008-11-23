<?php

/**
 * test actions.
 *
 * @package    project
 * @subpackage test
 * @author     Christian Kerl <christian-kerl@web.de>
 * @version    SVN: $Id: actions.class.php 9301 2008-05-27 01:08:46Z dwhittle $
 */
class testActions extends sfActions
{

  /**
   * Enter description here...
   *
   * @ws-enable
   *
   * @param string $input
   *
   * @return string
   */
  public function executeTest($request)
  {
    $this->result = $request->getParameter('input');

    return sfView::SUCCESS;
  }
}
