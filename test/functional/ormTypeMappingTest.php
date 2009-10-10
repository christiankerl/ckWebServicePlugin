<?php

$app = 'frontend';
$env = 'soapORMTestApi';
$debug = true;

include_once(dirname(__FILE__).'/../bootstrap/functional.php');

class CArticle
{
  public
    $id,
    $title,
    $content,
    $Comments,
    $Authors;
}

class CComment
{
  public
    $id,
    $author,
    $content,
    $Article;
}

class CAuthor
{
  public
    $id,
    $name;
}

class CPArticle
{
  public
    $id,
    $title,
    $content,
    $PropelComments,
    $PropelArticleAuthors;
}

class CPComment
{
  public
    $id,
    $author,
    $content,
    $PropelArticle;
}

class CPAuthor
{
  public
    $id,
    $name,
    $PropelArticleAuthors;
}

class CPArticleAuthor
{
  public
    $PropelAuthor,
    $PropelArticle;
}

$_options = array(
  'classmap' => array(
    'DoctrineArticle' => 'CArticle',
    'DoctrineArticleArray' => 'ckGenericArray',
    'DoctrineAuthor' => 'CAuthor',
    'DoctrineAuthorArray' => 'ckGenericArray',
    'DoctrineComment' => 'CComment',
    'DoctrineCommentArray' => 'ckGenericArray',

    'PropelArticle' => 'CPArticle',
    'PropelComment' => 'CPComment',
    'PropelAuthor' => 'CPAuthor',
    'PropelArticleAuthor' => 'CPArticleAuthor',
    'PropelCommentArray' => 'ckGenericArray',
    'PropelArticleAuthorArray' => 'ckGenericArray',
  ),
);

$c = new ckTestSoapClient($_options);

$c->orm_getObjectDoctrine()
  ->isFaultEmpty()
  ->isType('', 'CArticle')
  ->isType('Authors.0', 'CAuthor')
  ->is('Authors.0.name', 'Lisa')
  ->isType('Comments.0', 'CComment')
  ->isType('Comments.0.Article', 'CArticle')
  ->is('Comments.0.Article.id', $c->getResult()->id);

$comment1 = new CComment();
$comment2 = new CComment();

$article = new CArticle();
$article->id = 1;
$article->title = 'Some Title';
$article->Comments = array();
$article->Comments[] = $comment1;
$article->Comments[] = $comment2;

$c->orm_setObjectDoctrine($article)
  ->isFaultEmpty();

//$c->orm_setObjectDoctrine(null)
//  ->hasFault('TypeMappingException');
/*
$c->orm_getObjectPropel()
  ->isFaultEmpty()
  ->isType('', 'CPArticle')
  ->isType('PropelComments.0', 'CPComment')
  ->isType('PropelComments.0.PropelArticle', 'CPArticle')
  ->isType('PropelArticleAuthors.0', 'CPArticleAuthor')
  ->isType('PropelArticleAuthors.0.PropelAuthor', 'CPAuthor')
  ->is('PropelComments.0.PropelArticle.id', $c->getResult()->id);

$comment1 = new CPComment();
$comment2 = new CPComment();

$article = new CPArticle();
$article->id = 1;
$article->title = 'Some Title';
$article->PropelComments = array();
$article->PropelComments[] = $comment1;
$article->PropelComments[] = $comment2;

$c->orm_setObjectPropel($article)
  ->isFaultEmpty();
*/
//$c->orm_setObjectPropel(null)
//  ->hasFault('TypeMappingException');
