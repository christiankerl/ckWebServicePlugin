<?php


abstract class BasePropelArticleAuthor extends BaseObject  implements Persistent {


	
	protected static $peer;


	
	protected $article_id;


	
	protected $author_id;

	
	protected $aPropelArticle;

	
	protected $aPropelAuthor;

	
	protected $alreadyInSave = false;

	
	protected $alreadyInValidation = false;

	
	public function getArticleId()
	{

		return $this->article_id;
	}

	
	public function getAuthorId()
	{

		return $this->author_id;
	}

	
	public function setArticleId($v)
	{

						if ($v !== null && !is_int($v) && is_numeric($v)) {
			$v = (int) $v;
		}

		if ($this->article_id !== $v) {
			$this->article_id = $v;
			$this->modifiedColumns[] = PropelArticleAuthorPeer::ARTICLE_ID;
		}

		if ($this->aPropelArticle !== null && $this->aPropelArticle->getId() !== $v) {
			$this->aPropelArticle = null;
		}

	} 
	
	public function setAuthorId($v)
	{

						if ($v !== null && !is_int($v) && is_numeric($v)) {
			$v = (int) $v;
		}

		if ($this->author_id !== $v) {
			$this->author_id = $v;
			$this->modifiedColumns[] = PropelArticleAuthorPeer::AUTHOR_ID;
		}

		if ($this->aPropelAuthor !== null && $this->aPropelAuthor->getId() !== $v) {
			$this->aPropelAuthor = null;
		}

	} 
	
	public function hydrate(ResultSet $rs, $startcol = 1)
	{
		try {

			$this->article_id = $rs->getInt($startcol + 0);

			$this->author_id = $rs->getInt($startcol + 1);

			$this->resetModified();

			$this->setNew(false);

						return $startcol + 2; 
		} catch (Exception $e) {
			throw new PropelException("Error populating PropelArticleAuthor object", $e);
		}
	}

	
	public function delete($con = null)
	{
		if ($this->isDeleted()) {
			throw new PropelException("This object has already been deleted.");
		}

		if ($con === null) {
			$con = Propel::getConnection(PropelArticleAuthorPeer::DATABASE_NAME);
		}

		try {
			$con->begin();
			PropelArticleAuthorPeer::doDelete($this, $con);
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
			$con = Propel::getConnection(PropelArticleAuthorPeer::DATABASE_NAME);
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


												
			if ($this->aPropelArticle !== null) {
				if ($this->aPropelArticle->isModified()) {
					$affectedRows += $this->aPropelArticle->save($con);
				}
				$this->setPropelArticle($this->aPropelArticle);
			}

			if ($this->aPropelAuthor !== null) {
				if ($this->aPropelAuthor->isModified()) {
					$affectedRows += $this->aPropelAuthor->save($con);
				}
				$this->setPropelAuthor($this->aPropelAuthor);
			}


						if ($this->isModified()) {
				if ($this->isNew()) {
					$pk = PropelArticleAuthorPeer::doInsert($this, $con);
					$affectedRows += 1; 										 										 
					$this->setNew(false);
				} else {
					$affectedRows += PropelArticleAuthorPeer::doUpdate($this, $con);
				}
				$this->resetModified(); 			}

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


												
			if ($this->aPropelArticle !== null) {
				if (!$this->aPropelArticle->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aPropelArticle->getValidationFailures());
				}
			}

			if ($this->aPropelAuthor !== null) {
				if (!$this->aPropelAuthor->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aPropelAuthor->getValidationFailures());
				}
			}


			if (($retval = PropelArticleAuthorPeer::doValidate($this, $columns)) !== true) {
				$failureMap = array_merge($failureMap, $retval);
			}



			$this->alreadyInValidation = false;
		}

		return (!empty($failureMap) ? $failureMap : true);
	}

	
	public function getByName($name, $type = BasePeer::TYPE_PHPNAME)
	{
		$pos = PropelArticleAuthorPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
		return $this->getByPosition($pos);
	}

	
	public function getByPosition($pos)
	{
		switch($pos) {
			case 0:
				return $this->getArticleId();
				break;
			case 1:
				return $this->getAuthorId();
				break;
			default:
				return null;
				break;
		} 	}

	
	public function toArray($keyType = BasePeer::TYPE_PHPNAME)
	{
		$keys = PropelArticleAuthorPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getArticleId(),
			$keys[1] => $this->getAuthorId(),
		);
		return $result;
	}

	
	public function setByName($name, $value, $type = BasePeer::TYPE_PHPNAME)
	{
		$pos = PropelArticleAuthorPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
		return $this->setByPosition($pos, $value);
	}

	
	public function setByPosition($pos, $value)
	{
		switch($pos) {
			case 0:
				$this->setArticleId($value);
				break;
			case 1:
				$this->setAuthorId($value);
				break;
		} 	}

	
	public function fromArray($arr, $keyType = BasePeer::TYPE_PHPNAME)
	{
		$keys = PropelArticleAuthorPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setArticleId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setAuthorId($arr[$keys[1]]);
	}

	
	public function buildCriteria()
	{
		$criteria = new Criteria(PropelArticleAuthorPeer::DATABASE_NAME);

		if ($this->isColumnModified(PropelArticleAuthorPeer::ARTICLE_ID)) $criteria->add(PropelArticleAuthorPeer::ARTICLE_ID, $this->article_id);
		if ($this->isColumnModified(PropelArticleAuthorPeer::AUTHOR_ID)) $criteria->add(PropelArticleAuthorPeer::AUTHOR_ID, $this->author_id);

		return $criteria;
	}

	
	public function buildPkeyCriteria()
	{
		$criteria = new Criteria(PropelArticleAuthorPeer::DATABASE_NAME);

		$criteria->add(PropelArticleAuthorPeer::ARTICLE_ID, $this->article_id);
		$criteria->add(PropelArticleAuthorPeer::AUTHOR_ID, $this->author_id);

		return $criteria;
	}

	
	public function getPrimaryKey()
	{
		$pks = array();

		$pks[0] = $this->getArticleId();

		$pks[1] = $this->getAuthorId();

		return $pks;
	}

	
	public function setPrimaryKey($keys)
	{

		$this->setArticleId($keys[0]);

		$this->setAuthorId($keys[1]);

	}

	
	public function copyInto($copyObj, $deepCopy = false)
	{


		$copyObj->setNew(true);

		$copyObj->setArticleId(NULL); 
		$copyObj->setAuthorId(NULL); 
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
			self::$peer = new PropelArticleAuthorPeer();
		}
		return self::$peer;
	}

	
	public function setPropelArticle($v)
	{


		if ($v === null) {
			$this->setArticleId(NULL);
		} else {
			$this->setArticleId($v->getId());
		}


		$this->aPropelArticle = $v;
	}


	
	public function getPropelArticle($con = null)
	{
		if ($this->aPropelArticle === null && ($this->article_id !== null)) {
						$this->aPropelArticle = PropelArticlePeer::retrieveByPK($this->article_id, $con);

			
		}
		return $this->aPropelArticle;
	}

	
	public function setPropelAuthor($v)
	{


		if ($v === null) {
			$this->setAuthorId(NULL);
		} else {
			$this->setAuthorId($v->getId());
		}


		$this->aPropelAuthor = $v;
	}


	
	public function getPropelAuthor($con = null)
	{
		if ($this->aPropelAuthor === null && ($this->author_id !== null)) {
						$this->aPropelAuthor = PropelAuthorPeer::retrieveByPK($this->author_id, $con);

			
		}
		return $this->aPropelAuthor;
	}

} 