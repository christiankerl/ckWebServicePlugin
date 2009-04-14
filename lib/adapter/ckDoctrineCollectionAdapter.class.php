<?php
/**
 * This file is part of the ckWebServicePlugin
 *
 * @package   ckWebServicePlugin
 * @author    Christian Kerl <christian-kerl@web.de>
 * @copyright Copyright (c) 2008, Christian Kerl
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 * @version   SVN: $Id$
 */

/**
 * ckDoctrineCollectionAdapter adapts Doctrine_Collection objects so they are serializable as soap arrays.
 *
 * @package    ckWebServicePlugin
 * @subpackage adapter
 * @author     Christian Kerl <christian-kerl@web.de>
 */
class ckDoctrineCollectionAdapter extends Doctrine_Collection
{
  /**
   * Adapts all property values of a given Doctrine_Record object, which are instances of Doctrine_Collection,
   * to ckDoctrineCollectionAdapters.
   *
   * @param $object A Doctrine_Record instance
   *
   * @return Doctrine_Record The given Doctrine_Record instance with adapted property values
   */
  public static function adaptCollectionsToArray(Doctrine_Record $object)
  {
    foreach($object->getTable()->getRelations() as $relation)
    {
      if(!$relation->isOneToOne())
      {
        $object->setRelated($relation->getAlias(), ckDoctrineCollectionAdapter::fromCollection($object->get($relation->getAlias())));
      }
    }

    return $object;
  }

  /**
   * Creates a new instance from a given Doctrine_Collection instance.
   *
   * @param $collection A Doctrine_Collection instance
   *
   * @return ckDoctrineCollectionAdapter The new instance containing the data from the Doctrine_Collection instance
   */
  public static function fromCollection(Doctrine_Collection $collection)
  {
    if($collection instanceof ckDoctrineCollectionAdapter)
    {
      return $collection;
    }

    $result = new ckDoctrineCollectionAdapter($collection->getTable(), $collection->getKeyColumn());
    $result->setData($collection->getData());

    foreach($result->getData() as $object)
    {
      ckDoctrineCollectionAdapter::adaptCollectionsToArray($object);
    }

    return $result;
  }

  /**
   * Returns the contained objects, if the 'item' property is accessed, all other calls are redirected
   * to the parent implementation.
   *
   * (non-PHPdoc)
   * @see test/fixtures/project/plugins/sfDoctrinePlugin/lib/doctrine/Doctrine/Doctrine_Access#__get()
   */
  public function __get($property)
  {
    if($property == 'item')
    {
      return $this->getData();
    }

    return parent::__get($property);
  }
}
