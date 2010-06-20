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

function checkFixtureModel(ckTestSoapClient $c, $withAuthor)
{
  $c->isFaultEmpty()
    ->isCount('', 2)
    ->isType('', 'ckGenericArray')
    ->isType('0', 'myArticle')
    ->is('0.id', 1)
    ->is('0.title', 'Howto Build Webservices in Symfony')
    ->is('0.content', 'Some Content.')
    ->isType('0.Authors', 'ckGenericArray')
    ->isType('0.Comments', 'ckGenericArray')
    ->isCount('0.Comments', 2)
    ->isType('0.Comments.0', 'myComment')
    ->is('0.Comments.0.content', 'Physics is great!')
    ->is('0.Comments.0.author', 'Isaac Newton')
    ->isType('0.Comments.1', 'myComment')
    ->is('0.Comments.1.content', 'I like cars!')
    ->is('0.Comments.1.author', 'Henry Ford')
    ->isType('1', 'myArticle')
    ->is('1.id', 2)
    ->is('1.title', 'Howto Write Tests In Symfony')
    ->is('1.content', 'Some Content.')
    ->isType('1.Authors', 'ckGenericArray')
    ->isType('1.Comments', 'ckGenericArray')
    ->isCount('1.Comments', 1)
    ->isType('1.Comments.0', 'myComment')
    ->is('1.Comments.0.content', 'Alles ist relativ!')
    ->is('1.Comments.0.author', 'Albert Einstein')
  ;

  $result = $c->getResult();

  $t = $c->test();
  $t->cmp_ok($result[0]->Comments[0]->Article, '===', $result[0]->Comments[1]->Article);
  $t->cmp_ok($result[0], '===', $result[0]->Comments[0]->Article);

  if($withAuthor)
  {
    $c
    ->isCount('0.Authors', 1)
    ->isType('0.Authors.0', 'myAuthor')
    ->is('0.Authors.0.id', 1)
    ->is('0.Authors.0.name', 'Christian Kerl')
    ->isCount('1.Authors', 1)
    ->isType('1.Authors.0', 'myAuthor')
    ->is('1.Authors.0.id', 1)
    ->is('1.Authors.0.name', 'Christian Kerl');

    $t->cmp_ok($result[0]->Authors[0], '===', $result[1]->Authors[0]);
    $t->cmp_ok($result[0], '===', $result[0]->Authors[0]->Articles[0]);
    $t->cmp_ok($result[1], '===', $result[0]->Authors[0]->Articles[1]);
    $t->cmp_ok($result[0], '===', $result[1]->Authors[0]->Articles[0]);
    $t->cmp_ok($result[1], '===', $result[1]->Authors[0]->Articles[1]);
  }
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

$c = new ckTestSoapClient($_options, $test);

checkFixtureModel($c->getFixtureModel(), true);

$lastResult = $c->getResult();
// unset Authors with cyclic reference causing problems during deserialization at server
$lastResult[0]->Authors = array();
$lastResult[1]->Authors = array();

checkFixtureModel($c->passFixtureModel($lastResult), false);
