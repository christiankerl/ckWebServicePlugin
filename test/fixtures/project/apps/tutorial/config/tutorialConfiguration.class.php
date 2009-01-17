<?php

class tutorialConfiguration extends sfApplicationConfiguration
{
  public function configure()
  {
    $this->dispatcher->connect('webservice.handle_header', array('AuthHeaderListener', 'handleAuthHeader'));
  }
}
