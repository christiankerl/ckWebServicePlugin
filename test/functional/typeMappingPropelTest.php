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
function checkFixtureModel(ckTestSoapClient $c, $withAuthor)
{
  $c->isFaultEmpty()
    ->isCount('', 2)
    ->isType('', 'ckGenericArray')
    ->isType('0', 'myArticle')
    ->is('0.id', 1)
    ->is('0.title', 'Howto Build Webservices in Symfony')
    ->is('0.content', 'Some Content.')
    ->isType('0.ArticleAuthors', 'ckGenericArray')
    ->isType('0.ArticleComments', 'ckGenericArray')
    ->isCount('0.ArticleComments', 2)
    ->isType('0.ArticleComments.0', 'myArticleComment')
    ->is('0.ArticleComments.0.content', 'Physics is great!')
    ->is('0.ArticleComments.0.author', 'Isaac Newton')
    ->isType('0.ArticleComments.1', 'myArticleComment')
    ->is('0.ArticleComments.1.content', 'I like cars!')
    ->is('0.ArticleComments.1.author', 'Henry Ford')
    ->isType('1', 'myArticle')
    ->is('1.id', 2)
    ->is('1.title', 'Howto Write Tests In Symfony')
    ->is('1.content', 'Some Content.')
    ->isType('1.ArticleAuthors', 'ckGenericArray')
    ->isType('1.ArticleComments', 'ckGenericArray')
    ->isCount('1.ArticleComments', 1)
    ->isType('1.ArticleComments.0', 'myArticleComment')
    ->is('1.ArticleComments.0.content', 'Alles ist relativ!')
    ->is('1.ArticleComments.0.author', 'Albert Einstein')
  ;

  $result = $c->getResult();

  $t = $c->test();
  $t->cmp_ok($result[0]->ArticleComments[0]->Article, '===', $result[0]->ArticleComments[1]->Article);
  $t->cmp_ok($result[0], '===', $result[0]->ArticleComments[0]->Article);

  if($withAuthor)
  {
    $c
    ->isCount('0.ArticleAuthors', 1)
    ->isType('0.ArticleAuthors.0', 'myArticleAuthor')
    ->isType('0.ArticleAuthors.0.Author', 'myAuthor')
    ->is('0.ArticleAuthors.0.Author.id', 1)
    ->is('0.ArticleAuthors.0.Author.name', 'Christian Kerl')
    ->isCount('0.ArticleAuthors.0.Author.ArticleAuthors', 2)
    ->isCount('1.ArticleAuthors', 1)
    ->isType('1.ArticleAuthors.0', 'myArticleAuthor')
    ->isType('1.ArticleAuthors.0.Author', 'myAuthor')
    ->is('1.ArticleAuthors.0.Author.id', 1)
    ->is('1.ArticleAuthors.0.Author.name', 'Christian Kerl')
    ->isCount('1.ArticleAuthors.0.Author.ArticleAuthors', 2);

    $t->ok($result[0]->ArticleAuthors[0]->Author === $result[1]->ArticleAuthors[0]->Author);
    $t->ok($result[0] === $result[0]->ArticleAuthors[0]->Article);
    $t->ok($result[1] === $result[1]->ArticleAuthors[0]->Article);
  }
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

checkFixtureModel($c->getFixtureModel(), true);

$lastResult = $c->getResult();
// unset ArticleAuthors with cyclic reference causing problems during deserialization at server
$lastResult[0]->ArticleAuthors = array();
$lastResult[1]->ArticleAuthors = array();

checkFixtureModel($c->passFixtureModel($lastResult), false);