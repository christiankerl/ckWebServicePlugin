<?php

/**
 * SoapHeaderListener class.
 *
 * @package    project
 * @subpackage lib
 * @author     Christian Kerl <christian-kerl@web.de>
 * @version    SVN: $Id$
 */
class SoapHeaderListener
{
  public static function listenToHandleHeader(sfEvent $event)
  {
    switch($event['header'])
    {
      case 'AuthHeader':
        $event->setReturnValue(self::handleAuthHeader($event['data']));
        break;
      case 'ExtraHeader':
        $event->setReturnValue(self::handleExtraHeader($event['data']));
        break;
      default:
        return false;
    }

    return true;
  }

  public static function handleAuthHeader(AuthData $data)
  {
    if($data->username == 'user' && $data->password == 'secret')
    {
      sfContext::getInstance()->getUser()->setAuthenticated(true);
    }

    $data->username = 'reset';
    $data->password = 'reset';

    return $data;
  }

  public static function handleExtraHeader(ExtraHeaderData $data)
  {
    $data->content = sprintf('HandledInput(%s)', $data->content);

    return $data;
  }
}