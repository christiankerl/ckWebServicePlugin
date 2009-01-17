<?php

class frontendConfiguration extends sfApplicationConfiguration
{
  public function configure()
  {
    $this->getEventDispatcher()->connect('webservice.handle_header', array('SoapHeaderListener', 'listenToHandleHeader'));
  }
}
