<?php


abstract class BasePropelComment extends BaseObject  implements Persistent {


	
	protected static $peer;


	
	protected $id;


	
	protected $article_id;


	
	protected $author;


	
	protected $content;

	
	protected $aPropelArticle;

	
	protected $alreadyInSave = false;

	
	protected $alreadyInValidation = false;

	
	public function getId()
	{

		return $this->id;
	}

	
	public function getArticleId()
	{

		return $this->article_id;
	}

	
	public function getAuthor()
	{

		return $this->author;
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
			$this->modifiedColumns[] = PropelCommentPeer::ID;
		}

	} 
	
	public function setArticleId($v)
	{

						if ($v !== null && !is_int($v) && is_numeric($v)) {
			$v = (int) $v;
		}

		if ($this->article_id !== $v) {
			$this->article_id = $v;
			$this->modifiedColumns[] = PropelCommentPeer::ARTICLE_ID;
		}

		if ($this->aPropelArticle !== null && $this->aPropelArticle->getId() !== $v) {
			$this->aPropelArticle = null;
		}

	} 
	
	public function setAuthor($v)
	{

						if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->author !== $v) {
			$this->author = $v;
			$this->modifiedColumns[] = PropelCommentPeer::AUTHOR;
		}

	} 
	
	public function setContent($v)
	{

						if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->content !== $v) {
			$this->content = $v;
			$this->modifiedColumns[] = PropelCommentPeer::CONTENT;
		}

	} 
	
	public function hydrate(ResultSet $rs, $startcol = 1)
	{
		try {

			$this->id = $rs->getInt($startcol + 0);

			$this->article_id = $rs->getInt($startcol + 1);

			$this->author = $rs->getString($startcol + 2);

			$this->content = $rs->getString($startcol + 3);

			$this->resetModified();

			$this->setNew(false);

						return $startcol + 4; 
		} catch (Exception $e) {
			throw new PropelException("Error populating PropelComment object", $e);
		}
	}

	
	public function delete($con = null)
	{
		if ($this->isDeleted()) {
			throw new PropelException("This object has already been deleted.");
		}

		if ($con === null) {
			$con = Propel::getConnection(PropelCommentPeer::DATABASE_NAME);
		}

		try {
			$con->begin();
			PropelCommentPeer::doDelete($this, $con);
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
			$con = Propel::getConnection(PropelCommentPeer::DATABASE_NAME);
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


						if ($this->isModified()) {
				if ($this->isNew()) {
					$pk = PropelCommentPeer::doInsert($this, $con);
					$affectedRows += 1; 										 										 
					$this->setId($pk);  
					$this->setNew(false);
				} else {
					$affectedRows += PropelCommentPeer::doUpdate($this, $con);
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


			if (($retval = PropelCommentPeer::doValidate($this, $columns)) !== true) {
				$failureMap = array_merge($failureMap, $retval);
			}



			$this->alreadyInValidation = false;
		}

		return (!empty($failureMap) ? $failureMap : true);
	}

	
	public function getByName($name, $type = BasePeer::TYPE_PHPNAME)
	{
		$pos = PropelCommentPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
		return $this->getByPosition($pos);
	}

	
	public function getByPosition($pos)
	{
		switch($pos) {
			case 0:
				return $this->getId();
				break;
			case 1:
				return $this->getArticleId();
				break;
			case 2:
				return $this->getAuthor();
				break;
			case 3:
				return $this->getContent();
				break;
			default:
				return null;
				break;
		} 	}

	
	public function toArray($keyType = BasePeer::TYPE_PHPNAME)
	{
		$keys = PropelCommentPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getArticleId(),
			$keys[2] => $this->getAuthor(),
			$keys[3] => $this->getContent(),
		);
		return $result;
	}

	
	public function setByName($name, $value, $type = BasePeer::TYPE_PHPNAME)
	{
		$pos = PropelCommentPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
		return $this->setByPosition($pos, $value);
	}

	
	public function setByPosition($pos, $value)
	{
		switch($pos) {
			case 0:
				$this->setId($value);
				break;
			case 1:
				$this->setArticleId($value);
				break;
			case 2:
				$this->setAuthor($value);
				break;
			case 3:
				$this->setContent($value);
				break;
		} 	}

	
	public function fromArray($arr, $keyType = BasePeer::TYPE_PHPNAME)
	{
		$keys = PropelCommentPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setArticleId($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setAuthor($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setContent($arr[$keys[3]]);
	}

	
	public function buildCriteria()
	{
		$criteria = new Criteria(PropelCommentPeer::DATABASE_NAME);

		if ($this->isColumnModified(PropelCommentPeer::ID)) $criteria->add(PropelCommentPeer::ID, $this->id);
		if ($this->isColumnModified(PropelCommentPeer::ARTICLE_ID)) $criteria->add(PropelCommentPeer::ARTICLE_ID, $this->article_id);
		if ($this->isColumnModified(PropelCommentPeer::AUTHOR)) $criteria->add(PropelCommentPeer::AUTHOR, $this->author);
		if ($this->isColumnModified(PropelCommentPeer::CONTENT)) $criteria->add(PropelCommentPeer::CONTENT, $this->content);

		return $criteria;
	}

	
	public function buildPkeyCriteria()
	{
		$criteria = new Criteria(PropelCommentPeer::DATABASE_NAME);

		$criteria->add(PropelCommentPeer::ID, $this->id);

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

		$copyObj->setArticleId($this->article_id);

		$copyObj->setAuthor($this->author);

		$copyObj->setContent($this->content);


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
			self::$peer = new PropelCommentPeer();
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

} 