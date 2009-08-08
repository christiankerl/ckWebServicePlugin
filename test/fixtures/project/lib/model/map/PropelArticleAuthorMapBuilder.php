<?php



class PropelArticleAuthorMapBuilder {

	
	const CLASS_NAME = 'lib.model.map.PropelArticleAuthorMapBuilder';

	
	private $dbMap;

	
	public function isBuilt()
	{
		return ($this->dbMap !== null);
	}

	
	public function getDatabaseMap()
	{
		return $this->dbMap;
	}

	
	public function doBuild()
	{
		$this->dbMap = Propel::getDatabaseMap('propel');

		$tMap = $this->dbMap->addTable('propel_article_author');
		$tMap->setPhpName('PropelArticleAuthor');

		$tMap->setUseIdGenerator(false);

		$tMap->addForeignPrimaryKey('ARTICLE_ID', 'ArticleId', 'int' , CreoleTypes::INTEGER, 'propel_article', 'ID', true, null);

		$tMap->addForeignPrimaryKey('AUTHOR_ID', 'AuthorId', 'int' , CreoleTypes::INTEGER, 'propel_author', 'ID', true, null);

	} 
} 