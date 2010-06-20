<?php

/**
 * nonLC_name actions.
 *
 * @package    project
 * @subpackage nonLC_name
 * @author     Christian Kerl <christian-kerl@web.de>
 * @version    SVN: $Id$
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
