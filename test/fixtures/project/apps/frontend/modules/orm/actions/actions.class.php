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
  public function executeGetObjectDoctrine(sfWebRequest $request)
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
  public function executeSetObjectDoctrine(sfWebRequest $request)
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
}
