<?php
/**
 * This file is part of the ckWebServicePlugin
 *
 * @package   ckWebServicePlugin
 * @author    Christian Kerl <christian-kerl@web.de>
 * @copyright Copyright (c) 2010, Christian Kerl
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 * @version   SVN: $Id$
 */

$project = 'projectWithDoctrine';
$app     = 'main';
$env     = 'soap';
$debug   = true;

include(dirname(__FILE__).'/../bootstrap/functional.php');

Doctrine::createTablesFromModels(sfConfig::get('sf_lib_dir').'/model/');
Doctrine::loadData(sfConfig::get('sf_data_dir').'/fixtures/');

class myArticle
{
  public
    $id,
    $title,
    $content,
    $Comments,
    $Authors;
}

class myComment
{
  public
    $id,
    $author,
    $content,
    $Article;
}

class myAuthor
{
  public
    $id,
    $name,
    $Articles;
}

$_options = array(
  'classmap' => array(
    'Article'      => 'myArticle',
    'ArticleArray' => 'ckGenericArray',
    'Author'       => 'myAuthor',
    'AuthorArray'  => 'ckGenericArray',
    'Comment'      => 'myComment',
    'CommentArray' => 'ckGenericArray'
  )
);

$c = new ckTestSoapClient($_options);
$c->getFixtureModel()
  ->isFaultEmpty()
  ->isCount('', 2)
  ->isType('', 'ckGenericArray')
  ->isType('0', 'myArticle')
  ->isType('0.Authors', 'ckGenericArray')
  ->isCount('0.Authors', 1)
  ->isType('0.Authors.0', 'myAuthor')
  ->isType('0.Comments', 'ckGenericArray')
  ->isCount('0.Comments', 2)
  ->isType('0.Comments.0', 'myComment')
  ;

$result = $c->getResult();

$t = $c->test();
$t->cmp_ok($result[0]->Authors[0], '===', $result[1]->Authors[0]);
$t->cmp_ok($result[0]->Comments[0]->Article, '===', $result[0]->Comments[1]->Article);
$t->cmp_ok($result[0], '===', $result[0]->Comments[0]->Article);
