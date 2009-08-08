<?php


abstract class BasePropelArticle extends BaseObject  implements Persistent {


	
	protected static $peer;


	
	protected $id;


	
	protected $title;


	
	protected $content;

	
	protected $collPropelComments;

	
	protected $lastPropelCommentCriteria = null;

	
	protected $collPropelArticleAuthors;

	
	protected $lastPropelArticleAuthorCriteria = null;

	
	protected $alreadyInSave = false;

	
	protected $alreadyInValidation = false;

	
	public function getId()
	{

		return $this->id;
	}

	
	public function getTitle()
	{

		return $this->title;
	}

	
	public function getContent()
	{

		return $this->content;
	}

	
	public function setId($v)
	{

						if ($v !== null && !is_int($v) && is_numeric($v)) {
			$v = (int) $v;
		}

		if ($this->id !== $v) {
			$this->id = $v;
			$this->modifiedColumns[] = PropelArticlePeer::ID;
		}

	} 
	
	public function setTitle($v)
	{

						if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->title !== $v) {
			$this->title = $v;
			$this->modifiedColumns[] = PropelArticlePeer::TITLE;
		}

	} 
	
	public function setContent($v)
	{

						if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->content !== $v) {
			$this->content = $v;
			$this->modifiedColumns[] = PropelArticlePeer::CONTENT;
		}

	} 
	
	public function hydrate(ResultSet $rs, $startcol = 1)
	{
		try {

			$this->id = $rs->getInt($startcol + 0);

			$this->title = $rs->getString($startcol + 1);

			$this->content = $rs->getString($startcol + 2);

			$this->resetModified();

			$this->setNew(false);

						return $startcol + 3; 
		} catch (Exception $e) {
			throw new PropelException("Error populating PropelArticle object", $e);
		}
	}

	
	public function delete($con = null)
	{
		if ($this->isDeleted()) {
			throw new PropelException("This object has already been deleted.");
		}

		if ($con === null) {
			$con = Propel::getConnection(PropelArticlePeer::DATABASE_NAME);
		}

		try {
			$con->begin();
			PropelArticlePeer::doDelete($this, $con);
			$this->setDeleted(true);
			$con->commit();
		} catch (PropelException $e) {
			$con->rollback();
			throw $e;
		}
	}

	
	public function save($con = null)
	{
		if ($this->isDeleted()) {
			throw new PropelException("You cannot save an object that has been deleted.");
		}

		if ($con === null) {
			$con = Propel::getConnection(PropelArticlePeer::DATABASE_NAME);
		}

		try {
			$con->begin();
			$affectedRows = $this->doSave($con);
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollback();
			throw $e;
		}
	}

	
	protected function doSave($con)
	{
		$affectedRows = 0; 		if (!$this->alreadyInSave) {
			$this->alreadyInSave = true;


						if ($this->isModified()) {
				if ($this->isNew()) {
					$pk = PropelArticlePeer::doInsert($this, $con);
					$affectedRows += 1; 										 										 
					$this->setId($pk);  
					$this->setNew(false);
				} else {
					$affectedRows += PropelArticlePeer::doUpdate($this, $con);
				}
				$this->resetModified(); 			}

			if ($this->collPropelComments !== null) {
				foreach($this->collPropelComments as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collPropelArticleAuthors !== null) {
				foreach($this->collPropelArticleAuthors as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			$this->alreadyInSave = false;
		}
		return $affectedRows;
	} 
	
	protected $validationFailures = array();

	
	public function getValidationFailures()
	{
		return $this->validationFailures;
	}

	
	public function validate($columns = null)
	{
		$res = $this->doValidate($columns);
		if ($res === true) {
			$this->validationFailures = array();
			return true;
		} else {
			$this->validationFailures = $res;
			return false;
		}
	}

	
	protected function doValidate($columns = null)
	{
		if (!$this->alreadyInValidation) {
			$this->alreadyInValidation = true;
			$retval = null;

			$failureMap = array();


			if (($retval = PropelArticlePeer::doValidate($this, $columns)) !== true) {
				$failureMap = array_merge($failureMap, $retval);
			}


				if ($this->collPropelComments !== null) {
					foreach($this->collPropelComments as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collPropelArticleAuthors !== null) {
					foreach($this->collPropelArticleAuthors as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}


			$this->alreadyInValidation = false;
		}

		return (!empty($failureMap) ? $failureMap : true);
	}

	
	public function getByName($name, $type = BasePeer::TYPE_PHPNAME)
	{
		$pos = PropelArticlePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
		return $this->getByPosition($pos);
	}

	
	public function getByPosition($pos)
	{
		switch($pos) {
			case 0:
				return $this->getId();
				break;
			case 1:
				return $this->getTitle();
				break;
			case 2:
				return $this->getContent();
				break;
			default:
				return null;
				break;
		} 	}

	
	public function toArray($keyType = BasePeer::TYPE_PHPNAME)
	{
		$keys = PropelArticlePeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getTitle(),
			$keys[2] => $this->getContent(),
		);
		return $result;
	}

	
	public function setByName($name, $value, $type = BasePeer::TYPE_PHPNAME)
	{
		$pos = PropelArticlePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
		return $this->setByPosition($pos, $value);
	}

	
	public function setByPosition($pos, $value)
	{
		switch($pos) {
			case 0:
				$this->setId($value);
				break;
			case 1:
				$this->setTitle($value);
				break;
			case 2:
				$this->setContent($value);
				break;
		} 	}

	
	public function fromArray($arr, $keyType = BasePeer::TYPE_PHPNAME)
	{
		$keys = PropelArticlePeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setTitle($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setContent($arr[$keys[2]]);
	}

	
	public function buildCriteria()
	{
		$criteria = new Criteria(PropelArticlePeer::DATABASE_NAME);

		if ($this->isColumnModified(PropelArticlePeer::ID)) $criteria->add(PropelArticlePeer::ID, $this->id);
		if ($this->isColumnModified(PropelArticlePeer::TITLE)) $criteria->add(PropelArticlePeer::TITLE, $this->title);
		if ($this->isColumnModified(PropelArticlePeer::CONTENT)) $criteria->add(PropelArticlePeer::CONTENT, $this->content);

		return $criteria;
	}

	
	public function buildPkeyCriteria()
	{
		$criteria = new Criteria(PropelArticlePeer::DATABASE_NAME);

		$criteria->add(PropelArticlePeer::ID, $this->id);

		return $criteria;
	}

	
	public function getPrimaryKey()
	{
		return $this->getId();
	}

	
	public function setPrimaryKey($key)
	{
		$this->setId($key);
	}

	
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setTitle($this->title);

		$copyObj->setContent($this->content);


		if ($deepCopy) {
									$copyObj->setNew(false);

			foreach($this->getPropelComments() as $relObj) {
				$copyObj->addPropelComment($relObj->copy($deepCopy));
			}

			foreach($this->getPropelArticleAuthors() as $relObj) {
				$copyObj->addPropelArticleAuthor($relObj->copy($deepCopy));
			}

		} 

		$copyObj->setNew(true);

		$copyObj->setId(NULL); 
	}

	
	public function copy($deepCopy = false)
	{
				$clazz = get_class($this);
		$copyObj = new $clazz();
		$this->copyInto($copyObj, $deepCopy);
		return $copyObj;
	}

	
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new PropelArticlePeer();
		}
		return self::$peer;
	}

	
	public function initPropelComments()
	{
		if ($this->collPropelComments === null) {
			$this->collPropelComments = array();
		}
	}

	
	public function getPropelComments($criteria = null, $con = null)
	{
				if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collPropelComments === null) {
			if ($this->isNew()) {
			   $this->collPropelComments = array();
			} else {

				$criteria->add(PropelCommentPeer::ARTICLE_ID, $this->getId());

				PropelCommentPeer::addSelectColumns($criteria);
				$this->collPropelComments = PropelCommentPeer::doSelect($criteria, $con);
			}
		} else {
						if (!$this->isNew()) {
												

				$criteria->add(PropelCommentPeer::ARTICLE_ID, $this->getId());

				PropelCommentPeer::addSelectColumns($criteria);
				if (!isset($this->lastPropelCommentCriteria) || !$this->lastPropelCommentCriteria->equals($criteria)) {
					$this->collPropelComments = PropelCommentPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastPropelCommentCriteria = $criteria;
		return $this->collPropelComments;
	}

	
	public function countPropelComments($criteria = null, $distinct = false, $con = null)
	{
				if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(PropelCommentPeer::ARTICLE_ID, $this->getId());

		return PropelCommentPeer::doCount($criteria, $distinct, $con);
	}

	
	public function addPropelComment(PropelComment $l)
	{
		$this->collPropelComments[] = $l;
		$l->setPropelArticle($this);
	}

	
	public function initPropelArticleAuthors()
	{
		if ($this->collPropelArticleAuthors === null) {
			$this->collPropelArticleAuthors = array();
		}
	}

	
	public function getPropelArticleAuthors($criteria = null, $con = null)
	{
				if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collPropelArticleAuthors === null) {
			if ($this->isNew()) {
			   $this->collPropelArticleAuthors = array();
			} else {

				$criteria->add(PropelArticleAuthorPeer::ARTICLE_ID, $this->getId());

				PropelArticleAuthorPeer::addSelectColumns($criteria);
				$this->collPropelArticleAuthors = PropelArticleAuthorPeer::doSelect($criteria, $con);
			}
		} else {
						if (!$this->isNew()) {
												

				$criteria->add(PropelArticleAuthorPeer::ARTICLE_ID, $this->getId());

				PropelArticleAuthorPeer::addSelectColumns($criteria);
				if (!isset($this->lastPropelArticleAuthorCriteria) || !$this->lastPropelArticleAuthorCriteria->equals($criteria)) {
					$this->collPropelArticleAuthors = PropelArticleAuthorPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastPropelArticleAuthorCriteria = $criteria;
		return $this->collPropelArticleAuthors;
	}

	
	public function countPropelArticleAuthors($criteria = null, $distinct = false, $con = null)
	{
				if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(PropelArticleAuthorPeer::ARTICLE_ID, $this->getId());

		return PropelArticleAuthorPeer::doCount($criteria, $distinct, $con);
	}

	
	public function addPropelArticleAuthor(PropelArticleAuthor $l)
	{
		$this->collPropelArticleAuthors[] = $l;
		$l->setPropelArticle($this);
	}


	
	public function getPropelArticleAuthorsJoinPropelAuthor($criteria = null, $con = null)
	{
				if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collPropelArticleAuthors === null) {
			if ($this->isNew()) {
				$this->collPropelArticleAuthors = array();
			} else {

				$criteria->add(PropelArticleAuthorPeer::ARTICLE_ID, $this->getId());

				$this->collPropelArticleAuthors = PropelArticleAuthorPeer::doSelectJoinPropelAuthor($criteria, $con);
			}
		} else {
									
			$criteria->add(PropelArticleAuthorPeer::ARTICLE_ID, $this->getId());

			if (!isset($this->lastPropelArticleAuthorCriteria) || !$this->lastPropelArticleAuthorCriteria->equals($criteria)) {
				$this->collPropelArticleAuthors = PropelArticleAuthorPeer::doSelectJoinPropelAuthor($criteria, $con);
			}
		}
		$this->lastPropelArticleAuthorCriteria = $criteria;

		return $this->collPropelArticleAuthors;
	}

} 