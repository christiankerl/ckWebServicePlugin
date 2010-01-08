<?php


/**
 * This class adds structure of 'propel_article_author' table to 'propel' DatabaseMap object.
 *
 *
 *
 * These statically-built map classes are used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 * @package    lib.model.map
 */
class PropelArticleAuthorMapBuilder implements MapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.model.map.PropelArticleAuthorMapBuilder';

	/**
	 * The database map.
	 */
	private $dbMap;

	/**
	 * Tells us if this DatabaseMapBuilder is built so that we
	 * don't have to re-build it every time.
	 *
	 * @return     boolean true if this DatabaseMapBuilder is built, false otherwise.
	 */
	public function isBuilt()
	{
		return ($this->dbMap !== null);
	}

	/**
	 * Gets the databasemap this map builder built.
	 *
	 * @return     the databasemap
	 */
	public function getDatabaseMap()
	{
		return $this->dbMap;
	}

	/**
	 * The doBuild() method builds the DatabaseMap
	 *
	 * @return     void
	 * @throws     PropelException
	 */
	public function doBuild()
	{
		$this->dbMap = Propel::getDatabaseMap(PropelArticleAuthorPeer::DATABASE_NAME);

		$tMap = $this->dbMap->addTable(PropelArticleAuthorPeer::TABLE_NAME);
		$tMap->setPhpName('PropelArticleAuthor');
		$tMap->setClassname('PropelArticleAuthor');

		$tMap->setUseIdGenerator(false);

		$tMap->addForeignPrimaryKey('ARTICLE_ID', 'ArticleId', 'INTEGER' , 'propel_article', 'ID', true, null);

		$tMap->addForeignPrimaryKey('AUTHOR_ID', 'AuthorId', 'INTEGER' , 'propel_author', 'ID', true, null);

	} // doBuild()

} // PropelArticleAuthorMapBuilder
