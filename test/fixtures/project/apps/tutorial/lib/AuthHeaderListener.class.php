<?php

/**
 * AuthHeaderListener class.
 *
 * @package    project
 * @subpackage lib
 * @author     Christian Kerl <christian-kerl@web.de>
 * @version    SVN: $Id$
 */
class AuthHeaderListener
{
  const HEADER = 'AuthHeader';

  public static function handleAuthHeader($event)
  {
    if($event['header'] == self::HEADER)
    {
      if($event['data']->username == 'test' && $event['data']->password == 'secret')
      {
        sfContext::getInstance()->getUser()->setAuthenticated(true);
      }

      return true;
    }
    else
    {
      return false;
    }
  }
}