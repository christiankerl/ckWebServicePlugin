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

$project = 'projectWithPropel';
$app     = 'main';
$env     = 'soap';
$debug   = true;

include(dirname(__FILE__).'/../bootstrap/functional.php');

$configuration->initializePropel($app);
$configuration->loadFixtures('fixtures/model.yml');

class myArticle
{
  public
    $id,
    $title,
    $content,
    $ArticleComments,
    $ArticleAuthors;
}

class myArticleComment
{
  public
    $id,
    $author,
    $content,
    $Article;
}

class myArticleAuthor
{
  public
    $Article,
    $Author;
}

class myAuthor
{
  public
    $id,
    $name,
    $ArticleAuthors;
}

function checkFixtureModel(ckTestSoapClient $c)
{
  $c->getFixtureModel()
    ->isFaultEmpty()
    ->isCount('', 2)
    ->isType('', 'ckGenericArray')
    ->isType('0', 'myArticle')
    ->isType('0.ArticleAuthors', 'ckGenericArray')
    ->isCount('0.ArticleAuthors', 1)
    ->isType('0.ArticleAuthors.0', 'myArticleAuthor')
    ->isType('0.ArticleAuthors.0.Author', 'myAuthor')
    ->isType('0.ArticleComments', 'ckGenericArray')
    ->isCount('0.ArticleComments', 2)
    ->isType('0.ArticleComments.0', 'myArticleComment')
    ;

  $result = $c->getResult();

  $t = $c->test();
  $t->cmp_ok($result[0]->ArticleAuthors[0]->Author, '===', $result[1]->ArticleAuthors[0]->Author);
  $t->cmp_ok($result[0]->ArticleComments[0]->Article, '===', $result[0]->ArticleComments[1]->Article);
  $t->cmp_ok($result[0], '===', $result[0]->ArticleComments[0]->Article);
}

$_options = array(
  'classmap' => array(
    'Article'             => 'myArticle',
    'ArticleArray'        => 'ckGenericArray',
    'Author'              => 'myAuthor',
    'AuthorArray'         => 'ckGenericArray',
    'ArticleAuthor'       => 'myArticleAuthor',
    'ArticleAuthorArray'  => 'ckGenericArray',
    'ArticleComment'      => 'myArticleComment',
    'ArticleCommentArray' => 'ckGenericArray'
  )
);

$c = new ckTestSoapClient($_options);

checkFixtureModel($c->getFixtureModel());
checkFixtureModel($c->passFixtureModel($c->getResult()));