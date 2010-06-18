<?php

/**
 * nonLC_name actions.
 *
 * @package    project
 * @subpackage nonLC_name
 * @author     Christian Kerl <christian-kerl@web.de>
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class nonLC_nameActions extends sfActions
{
  /**
   * @WSMethod(webservice='TestServiceApi')
   *
   * @return string
   */
  public function executeGetResult($request)
  {
    $this->anotherCustomProperty = 'Fail!';

    $this->myCustomResult = 'MyCustomResult';
  }
}
