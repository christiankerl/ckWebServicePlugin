<?php


abstract class BasePropelArticleAuthorPeer {

	
	const DATABASE_NAME = 'propel';

	
	const TABLE_NAME = 'propel_article_author';

	
	const CLASS_DEFAULT = 'lib.model.PropelArticleAuthor';

	
	const NUM_COLUMNS = 2;

	
	const NUM_LAZY_LOAD_COLUMNS = 0;


	
	const ARTICLE_ID = 'propel_article_author.ARTICLE_ID';

	
	const AUTHOR_ID = 'propel_article_author.AUTHOR_ID';

	
	private static $phpNameMap = null;


	
	private static $fieldNames = array (
		BasePeer::TYPE_PHPNAME => array ('ArticleId', 'AuthorId', ),
		BasePeer::TYPE_COLNAME => array (PropelArticleAuthorPeer::ARTICLE_ID, PropelArticleAuthorPeer::AUTHOR_ID, ),
		BasePeer::TYPE_FIELDNAME => array ('article_id', 'author_id', ),
		BasePeer::TYPE_NUM => array (0, 1, )
	);

	
	private static $fieldKeys = array (
		BasePeer::TYPE_PHPNAME => array ('ArticleId' => 0, 'AuthorId' => 1, ),
		BasePeer::TYPE_COLNAME => array (PropelArticleAuthorPeer::ARTICLE_ID => 0, PropelArticleAuthorPeer::AUTHOR_ID => 1, ),
		BasePeer::TYPE_FIELDNAME => array ('article_id' => 0, 'author_id' => 1, ),
		BasePeer::TYPE_NUM => array (0, 1, )
	);

	
	public static function getMapBuilder()
	{
		return BasePeer::getMapBuilder('lib.model.map.PropelArticleAuthorMapBuilder');
	}
	
	public static function getPhpNameMap()
	{
		if (self::$phpNameMap === null) {
			$map = PropelArticleAuthorPeer::getTableMap();
			$columns = $map->getColumns();
			$nameMap = array();
			foreach ($columns as $column) {
				$nameMap[$column->getPhpName()] = $column->getColumnName();
			}
			self::$phpNameMap = $nameMap;
		}
		return self::$phpNameMap;
	}
	
	static public function translateFieldName($name, $fromType, $toType)
	{
		$toNames = self::getFieldNames($toType);
		$key = isset(self::$fieldKeys[$fromType][$name]) ? self::$fieldKeys[$fromType][$name] : null;
		if ($key === null) {
			throw new PropelException("'$name' could not be found in the field names of type '$fromType'. These are: " . print_r(self::$fieldKeys[$fromType], true));
		}
		return $toNames[$key];
	}

	

	static public function getFieldNames($type = BasePeer::TYPE_PHPNAME)
	{
		if (!array_key_exists($type, self::$fieldNames)) {
			throw new PropelException('Method getFieldNames() expects the parameter $type to be one of the class constants TYPE_PHPNAME, TYPE_COLNAME, TYPE_FIELDNAME, TYPE_NUM. ' . $type . ' was given.');
		}
		return self::$fieldNames[$type];
	}

	
	public static function alias($alias, $column)
	{
		return str_replace(PropelArticleAuthorPeer::TABLE_NAME.'.', $alias.'.', $column);
	}

	
	public static function addSelectColumns(Criteria $criteria)
	{

		$criteria->addSelectColumn(PropelArticleAuthorPeer::ARTICLE_ID);

		$criteria->addSelectColumn(PropelArticleAuthorPeer::AUTHOR_ID);

	}

	const COUNT = 'COUNT(propel_article_author.ARTICLE_ID)';
	const COUNT_DISTINCT = 'COUNT(DISTINCT propel_article_author.ARTICLE_ID)';

	
	public static function doCount(Criteria $criteria, $distinct = false, $con = null)
	{
				$criteria = clone $criteria;

				$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(PropelArticleAuthorPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(PropelArticleAuthorPeer::COUNT);
		}

				foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$rs = PropelArticleAuthorPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
						return 0;
		}
	}
	
	public static function doSelectOne(Criteria $criteria, $con = null)
	{
		$critcopy = clone $criteria;
		$critcopy->setLimit(1);
		$objects = PropelArticleAuthorPeer::doSelect($critcopy, $con);
		if ($objects) {
			return $objects[0];
		}
		return null;
	}
	
	public static function doSelect(Criteria $criteria, $con = null)
	{
		return PropelArticleAuthorPeer::populateObjects(PropelArticleAuthorPeer::doSelectRS($criteria, $con));
	}
	
	public static function doSelectRS(Criteria $criteria, $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(self::DATABASE_NAME);
		}

		if (!$criteria->getSelectColumns()) {
			$criteria = clone $criteria;
			PropelArticleAuthorPeer::addSelectColumns($criteria);
		}

				$criteria->setDbName(self::DATABASE_NAME);

						return BasePeer::doSelect($criteria, $con);
	}
	
	public static function populateObjects(ResultSet $rs)
	{
		$results = array();
	
				$cls = PropelArticleAuthorPeer::getOMClass();
		$cls = sfPropel::import($cls);
				while($rs->next()) {
		
			$obj = new $cls();
			$obj->hydrate($rs);
			$results[] = $obj;
			
		}
		return $results;
	}

	
	public static function doCountJoinPropelArticle(Criteria $criteria, $distinct = false, $con = null)
	{
				$criteria = clone $criteria;

				$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(PropelArticleAuthorPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(PropelArticleAuthorPeer::COUNT);
		}

				foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(PropelArticleAuthorPeer::ARTICLE_ID, PropelArticlePeer::ID);

		$rs = PropelArticleAuthorPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
						return 0;
		}
	}


	
	public static function doCountJoinPropelAuthor(Criteria $criteria, $distinct = false, $con = null)
	{
				$criteria = clone $criteria;

				$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(PropelArticleAuthorPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(PropelArticleAuthorPeer::COUNT);
		}

				foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(PropelArticleAuthorPeer::AUTHOR_ID, PropelAuthorPeer::ID);

		$rs = PropelArticleAuthorPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
						return 0;
		}
	}


	
	public static function doSelectJoinPropelArticle(Criteria $c, $con = null)
	{
		$c = clone $c;

				if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		PropelArticleAuthorPeer::addSelectColumns($c);
		$startcol = (PropelArticleAuthorPeer::NUM_COLUMNS - PropelArticleAuthorPeer::NUM_LAZY_LOAD_COLUMNS) + 1;
		PropelArticlePeer::addSelectColumns($c);

		$c->addJoin(PropelArticleAuthorPeer::ARTICLE_ID, PropelArticlePeer::ID);
		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = PropelArticleAuthorPeer::getOMClass();

			$cls = sfPropel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = PropelArticlePeer::getOMClass();

			$cls = sfPropel::import($omClass);
			$obj2 = new $cls();
			$obj2->hydrate($rs, $startcol);

			$newObject = true;
			foreach($results as $temp_obj1) {
				$temp_obj2 = $temp_obj1->getPropelArticle(); 				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
										$temp_obj2->addPropelArticleAuthor($obj1); 					break;
				}
			}
			if ($newObject) {
				$obj2->initPropelArticleAuthors();
				$obj2->addPropelArticleAuthor($obj1); 			}
			$results[] = $obj1;
		}
		return $results;
	}


	
	public static function doSelectJoinPropelAuthor(Criteria $c, $con = null)
	{
		$c = clone $c;

				if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		PropelArticleAuthorPeer::addSelectColumns($c);
		$startcol = (PropelArticleAuthorPeer::NUM_COLUMNS - PropelArticleAuthorPeer::NUM_LAZY_LOAD_COLUMNS) + 1;
		PropelAuthorPeer::addSelectColumns($c);

		$c->addJoin(PropelArticleAuthorPeer::AUTHOR_ID, PropelAuthorPeer::ID);
		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = PropelArticleAuthorPeer::getOMClass();

			$cls = sfPropel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = PropelAuthorPeer::getOMClass();

			$cls = sfPropel::import($omClass);
			$obj2 = new $cls();
			$obj2->hydrate($rs, $startcol);

			$newObject = true;
			foreach($results as $temp_obj1) {
				$temp_obj2 = $temp_obj1->getPropelAuthor(); 				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
										$temp_obj2->addPropelArticleAuthor($obj1); 					break;
				}
			}
			if ($newObject) {
				$obj2->initPropelArticleAuthors();
				$obj2->addPropelArticleAuthor($obj1); 			}
			$results[] = $obj1;
		}
		return $results;
	}


	
	public static function doCountJoinAll(Criteria $criteria, $distinct = false, $con = null)
	{
		$criteria = clone $criteria;

				$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(PropelArticleAuthorPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(PropelArticleAuthorPeer::COUNT);
		}

				foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(PropelArticleAuthorPeer::ARTICLE_ID, PropelArticlePeer::ID);

		$criteria->addJoin(PropelArticleAuthorPeer::AUTHOR_ID, PropelAuthorPeer::ID);

		$rs = PropelArticleAuthorPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
						return 0;
		}
	}


	
	public static function doSelectJoinAll(Criteria $c, $con = null)
	{
		$c = clone $c;

				if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		PropelArticleAuthorPeer::addSelectColumns($c);
		$startcol2 = (PropelArticleAuthorPeer::NUM_COLUMNS - PropelArticleAuthorPeer::NUM_LAZY_LOAD_COLUMNS) + 1;

		PropelArticlePeer::addSelectColumns($c);
		$startcol3 = $startcol2 + PropelArticlePeer::NUM_COLUMNS;

		PropelAuthorPeer::addSelectColumns($c);
		$startcol4 = $startcol3 + PropelAuthorPeer::NUM_COLUMNS;

		$c->addJoin(PropelArticleAuthorPeer::ARTICLE_ID, PropelArticlePeer::ID);

		$c->addJoin(PropelArticleAuthorPeer::AUTHOR_ID, PropelAuthorPeer::ID);

		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = PropelArticleAuthorPeer::getOMClass();


			$cls = sfPropel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);


					
			$omClass = PropelArticlePeer::getOMClass();


			$cls = sfPropel::import($omClass);
			$obj2 = new $cls();
			$obj2->hydrate($rs, $startcol2);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj2 = $temp_obj1->getPropelArticle(); 				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					$temp_obj2->addPropelArticleAuthor($obj1); 					break;
				}
			}

			if ($newObject) {
				$obj2->initPropelArticleAuthors();
				$obj2->addPropelArticleAuthor($obj1);
			}


					
			$omClass = PropelAuthorPeer::getOMClass();


			$cls = sfPropel::import($omClass);
			$obj3 = new $cls();
			$obj3->hydrate($rs, $startcol3);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj3 = $temp_obj1->getPropelAuthor(); 				if ($temp_obj3->getPrimaryKey() === $obj3->getPrimaryKey()) {
					$newObject = false;
					$temp_obj3->addPropelArticleAuthor($obj1); 					break;
				}
			}

			if ($newObject) {
				$obj3->initPropelArticleAuthors();
				$obj3->addPropelArticleAuthor($obj1);
			}

			$results[] = $obj1;
		}
		return $results;
	}


	
	public static function doCountJoinAllExceptPropelArticle(Criteria $criteria, $distinct = false, $con = null)
	{
				$criteria = clone $criteria;

				$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(PropelArticleAuthorPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(PropelArticleAuthorPeer::COUNT);
		}

				foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(PropelArticleAuthorPeer::AUTHOR_ID, PropelAuthorPeer::ID);

		$rs = PropelArticleAuthorPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
						return 0;
		}
	}


	
	public static function doCountJoinAllExceptPropelAuthor(Criteria $criteria, $distinct = false, $con = null)
	{
				$criteria = clone $criteria;

				$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn(PropelArticleAuthorPeer::COUNT_DISTINCT);
		} else {
			$criteria->addSelectColumn(PropelArticleAuthorPeer::COUNT);
		}

				foreach($criteria->getGroupByColumns() as $column)
		{
			$criteria->addSelectColumn($column);
		}

		$criteria->addJoin(PropelArticleAuthorPeer::ARTICLE_ID, PropelArticlePeer::ID);

		$rs = PropelArticleAuthorPeer::doSelectRS($criteria, $con);
		if ($rs->next()) {
			return $rs->getInt(1);
		} else {
						return 0;
		}
	}


	
	public static function doSelectJoinAllExceptPropelArticle(Criteria $c, $con = null)
	{
		$c = clone $c;

								if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		PropelArticleAuthorPeer::addSelectColumns($c);
		$startcol2 = (PropelArticleAuthorPeer::NUM_COLUMNS - PropelArticleAuthorPeer::NUM_LAZY_LOAD_COLUMNS) + 1;

		PropelAuthorPeer::addSelectColumns($c);
		$startcol3 = $startcol2 + PropelAuthorPeer::NUM_COLUMNS;

		$c->addJoin(PropelArticleAuthorPeer::AUTHOR_ID, PropelAuthorPeer::ID);


		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = PropelArticleAuthorPeer::getOMClass();

			$cls = sfPropel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = PropelAuthorPeer::getOMClass();


			$cls = sfPropel::import($omClass);
			$obj2  = new $cls();
			$obj2->hydrate($rs, $startcol2);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj2 = $temp_obj1->getPropelAuthor(); 				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					$temp_obj2->addPropelArticleAuthor($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj2->initPropelArticleAuthors();
				$obj2->addPropelArticleAuthor($obj1);
			}

			$results[] = $obj1;
		}
		return $results;
	}


	
	public static function doSelectJoinAllExceptPropelAuthor(Criteria $c, $con = null)
	{
		$c = clone $c;

								if ($c->getDbName() == Propel::getDefaultDB()) {
			$c->setDbName(self::DATABASE_NAME);
		}

		PropelArticleAuthorPeer::addSelectColumns($c);
		$startcol2 = (PropelArticleAuthorPeer::NUM_COLUMNS - PropelArticleAuthorPeer::NUM_LAZY_LOAD_COLUMNS) + 1;

		PropelArticlePeer::addSelectColumns($c);
		$startcol3 = $startcol2 + PropelArticlePeer::NUM_COLUMNS;

		$c->addJoin(PropelArticleAuthorPeer::ARTICLE_ID, PropelArticlePeer::ID);


		$rs = BasePeer::doSelect($c, $con);
		$results = array();

		while($rs->next()) {

			$omClass = PropelArticleAuthorPeer::getOMClass();

			$cls = sfPropel::import($omClass);
			$obj1 = new $cls();
			$obj1->hydrate($rs);

			$omClass = PropelArticlePeer::getOMClass();


			$cls = sfPropel::import($omClass);
			$obj2  = new $cls();
			$obj2->hydrate($rs, $startcol2);

			$newObject = true;
			for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
				$temp_obj1 = $results[$j];
				$temp_obj2 = $temp_obj1->getPropelArticle(); 				if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
					$newObject = false;
					$temp_obj2->addPropelArticleAuthor($obj1);
					break;
				}
			}

			if ($newObject) {
				$obj2->initPropelArticleAuthors();
				$obj2->addPropelArticleAuthor($obj1);
			}

			$results[] = $obj1;
		}
		return $results;
	}


  static public function getUniqueColumnNames()
  {
    return array();
  }
	
	public static function getTableMap()
	{
		return Propel::getDatabaseMap(self::DATABASE_NAME)->getTable(self::TABLE_NAME);
	}

	
	public static function getOMClass()
	{
		return PropelArticleAuthorPeer::CLASS_DEFAULT;
	}

	
	public static function doInsert($values, $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(self::DATABASE_NAME);
		}

		if ($values instanceof Criteria) {
			$criteria = clone $values; 		} else {
			$criteria = $values->buildCriteria(); 		}


				$criteria->setDbName(self::DATABASE_NAME);

		try {
									$con->begin();
			$pk = BasePeer::doInsert($criteria, $con);
			$con->commit();
		} catch(PropelException $e) {
			$con->rollback();
			throw $e;
		}

		return $pk;
	}

	
	public static function doUpdate($values, $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(self::DATABASE_NAME);
		}

		$selectCriteria = new Criteria(self::DATABASE_NAME);

		if ($values instanceof Criteria) {
			$criteria = clone $values; 
			$comparison = $criteria->getComparison(PropelArticleAuthorPeer::ARTICLE_ID);
			$selectCriteria->add(PropelArticleAuthorPeer::ARTICLE_ID, $criteria->remove(PropelArticleAuthorPeer::ARTICLE_ID), $comparison);

			$comparison = $criteria->getComparison(PropelArticleAuthorPeer::AUTHOR_ID);
			$selectCriteria->add(PropelArticleAuthorPeer::AUTHOR_ID, $criteria->remove(PropelArticleAuthorPeer::AUTHOR_ID), $comparison);

		} else { 			$criteria = $values->buildCriteria(); 			$selectCriteria = $values->buildPkeyCriteria(); 		}

				$criteria->setDbName(self::DATABASE_NAME);

		return BasePeer::doUpdate($selectCriteria, $criteria, $con);
	}

	
	public static function doDeleteAll($con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(self::DATABASE_NAME);
		}
		$affectedRows = 0; 		try {
									$con->begin();
			$affectedRows += BasePeer::doDeleteAll(PropelArticleAuthorPeer::TABLE_NAME, $con);
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollback();
			throw $e;
		}
	}

	
	 public static function doDelete($values, $con = null)
	 {
		if ($con === null) {
			$con = Propel::getConnection(PropelArticleAuthorPeer::DATABASE_NAME);
		}

		if ($values instanceof Criteria) {
			$criteria = clone $values; 		} elseif ($values instanceof PropelArticleAuthor) {

			$criteria = $values->buildPkeyCriteria();
		} else {
						$criteria = new Criteria(self::DATABASE_NAME);
												if(count($values) == count($values, COUNT_RECURSIVE))
			{
								$values = array($values);
			}
			$vals = array();
			foreach($values as $value)
			{

				$vals[0][] = $value[0];
				$vals[1][] = $value[1];
			}

			$criteria->add(PropelArticleAuthorPeer::ARTICLE_ID, $vals[0], Criteria::IN);
			$criteria->add(PropelArticleAuthorPeer::AUTHOR_ID, $vals[1], Criteria::IN);
		}

				$criteria->setDbName(self::DATABASE_NAME);

		$affectedRows = 0; 
		try {
									$con->begin();
			
			$affectedRows += BasePeer::doDelete($criteria, $con);
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollback();
			throw $e;
		}
	}

	
	public static function doValidate(PropelArticleAuthor $obj, $cols = null)
	{
		$columns = array();

		if ($cols) {
			$dbMap = Propel::getDatabaseMap(PropelArticleAuthorPeer::DATABASE_NAME);
			$tableMap = $dbMap->getTable(PropelArticleAuthorPeer::TABLE_NAME);

			if (! is_array($cols)) {
				$cols = array($cols);
			}

			foreach($cols as $colName) {
				if ($tableMap->containsColumn($colName)) {
					$get = 'get' . $tableMap->getColumn($colName)->getPhpName();
					$columns[$colName] = $obj->$get();
				}
			}
		} else {

		}

		$res =  BasePeer::doValidate(PropelArticleAuthorPeer::DATABASE_NAME, PropelArticleAuthorPeer::TABLE_NAME, $columns);
    if ($res !== true) {
        $request = sfContext::getInstance()->getRequest();
        foreach ($res as $failed) {
            $col = PropelArticleAuthorPeer::translateFieldname($failed->getColumn(), BasePeer::TYPE_COLNAME, BasePeer::TYPE_PHPNAME);
            $request->setError($col, $failed->getMessage());
        }
    }

    return $res;
	}

	
	public static function retrieveByPK( $article_id, $author_id, $con = null) {
		if ($con === null) {
			$con = Propel::getConnection(self::DATABASE_NAME);
		}
		$criteria = new Criteria();
		$criteria->add(PropelArticleAuthorPeer::ARTICLE_ID, $article_id);
		$criteria->add(PropelArticleAuthorPeer::AUTHOR_ID, $author_id);
		$v = PropelArticleAuthorPeer::doSelect($criteria, $con);

		return !empty($v) ? $v[0] : null;
	}
} 
if (Propel::isInit()) {
			try {
		BasePropelArticleAuthorPeer::getMapBuilder();
	} catch (Exception $e) {
		Propel::log('Could not initialize Peer: ' . $e->getMessage(), Propel::LOG_ERR);
	}
} else {
			Propel::registerMapBuilder('lib.model.map.PropelArticleAuthorMapBuilder');
}
