<?php

/**
 * orm actions.
 *
 * @package    project
 * @subpackage orm
 * @author     Christian Kerl <christian-kerl@web.de>
 * @version    SVN: $Id$
 */
class ormActions extends sfActions
{
  /**
   * @WSMethod(webservice='ORMTestApi')
   *
   * @return DoctrineArticle
   */
  public function executeGetObjectDoctrine($request)
  {
    $comment = new DoctrineComment();
    $comment->fromArray(array(
      'id' => 2,
      'article_id' => 1,
      'author' => 'John Doe',
      'content' => 'Some Test Content.'
    ));

    $author = new DoctrineAuthor();
    $author->fromArray(array(
      'id' => 3,
      'name' => 'Lisa'
    ));

    $article = new DoctrineArticle();
    $article->fromArray(array(
      'id' => 1,
      'title' => 'A Test Article!',
      'content' => 'Some Test Content.',
    ));

    $article->Comments[] = $comment;
    $article->Authors[] = $author;

    $this->result = $article;
  }

  /**
   * @WSMethod(webservice='ORMTestApi')
   *
   * @param DoctrineArticle $article
   */
  public function executeSetObjectDoctrine($request)
  {
    $article = $request->getParameter('article');

    $success = $article instanceof DoctrineArticle;

    if($success)
    {
      foreach($article->Comments as $comment)
      {
        $success = $comment instanceof DoctrineComment && $article == $comment->Article;

        if(!$success)
        {
          break;
        }
      }
    }

    if(!$success)
    {
      throw new sfException('TypeMappingException');
    }
  }

  /**
   * @WSMethod(webservice='ORMTestApi')
   *
   * @return PropelArticle
   */
  public function executeGetObjectPropel($request)
  {
    $author = new PropelAuthor();
    $author->setId(1);
    $author->setName('Joe');

    $comment = new PropelComment();
    $comment->setId(1);
    $comment->setContent('MyComment');

    $article = new PropelArticle();
    $article->setId(1);
    $article->setTitle('MyTitle');
    $article->setContent('MyContent');
    $article->addPropelComment($comment);

    $assoc = new PropelArticleAuthor();

    $article->addPropelArticleAuthor($assoc);

    $assoc->setPropelAuthor($author);

    $this->result = $article;
  }

  /**
   * @WSMethod(webservice='ORMTestApi')
   *
   * @param PropelArticle $article
   */
  public function executeSetObjectPropel($request)
  {
    $article = $request->getParameter('article');

    $success = $article instanceof PropelArticle;

    if($success)
    {
      foreach($article->getPropelComments() as $comment)
      {
        $success = $comment instanceof PropelComment && $comment->getPropelArticle() == $article;

        if(!$success)
        {
          break;
        }
      }
    }

    if(!$success)
    {
      throw new sfException('TypeMappingException');
    }
  }
}
