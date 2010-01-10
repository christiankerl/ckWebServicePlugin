<?php

/**
 * main actions.
 *
 * @package    projectWithPropel
 * @subpackage main
 * @author     Christian Kerl
 * @version    SVN: $Id$
 */
class mainActions extends sfActions
{
 /**
  * @WSMethod(name='getFixtureModel')
  *
  * @return Article[] All articles loaded from the database.
  */
  public function executeGetFixtureModel(sfWebRequest $request)
  {
    $this->result = ArticlePeer::doSelect(new Criteria());

    return sfView::SUCCESS;
  }
}
