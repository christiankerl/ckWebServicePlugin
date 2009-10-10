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
 * ckPropelPropertyStrategy
 *
 * @package    ckWebServicePlugin
 * @subpackage strategy
 * @author     Christian Kerl <christian-kerl@web.de>
 */
class ckPropelPropertyStrategy extends ckBeanPropertyStrategy
{
  protected $tableMap = null;

  /**
   * Gets the table map from the peer class corresponding to the annotated propel object class.
   *
   * @return TableMap The table map
   */
  protected function getTableMap()
  {
    if(is_null($this->tableMap))
    {
      $this->tableMap = call_user_func(array($this->getClass()->getName().'Peer', 'getTableMap'));
    }

    return $this->tableMap;
  }

  /**
   * Gets the TableMap for the given table name.
   *
   * @param string $refTable A table name
   *
   * @return TableMap The TableMap for the given table name
   */
  protected function getReferenceTableMap($refTable)
  {
    return $this->getTableMap()->getDatabaseMap()->getTable($refTable);
  }

  /**
   * (non-PHPdoc)
   * @see strategy/ckAbstractPropertyStrategy#__construct()
   */
  public function __construct(ReflectionClass $class)
  {
    if(!$class->isSubclassOf('BaseObject'))
    {
      throw new InvalidArgumentException(sprintf('The class \'%s\' has to be a subclass of BaseObject.', $class->getName()));
    }

    parent::__construct($class);
  }

  /**
   * (non-PHPdoc)
   * @see strategy/ckAbstractPropertyStrategy#getProperties()
   */
  public function getProperties()
  {
    $properties = array();

    $properties = array_merge($properties, $this->getSimpleProperties());
    $properties = array_merge($properties, $this->getToOneProperties());
    $properties = array_merge($properties, $this->getToManyProperties());

    return $properties;
  }

  /* (non-PHPdoc)
   * @see lib/vendor/ckWsdlGenerator/strategy/ckBeanPropertyStrategy#setPropertyValue()
   */
  public function setPropertyValue($object, $property, $value)
  {
    if(is_array($value) && ckString::endsWith($property, 's'))
    {
      $method = 'add' . ckString::ucfirst(substr($property, 0, -1));

      if(!$this->getClass()->hasMethod($method))
      {
        throw new InvalidArgumentException();
      }

      foreach($value as $item)
      {
        call_user_func_array(array($object, $method), array($item));
      }
    }
    else
    {
      parent::setPropertyValue($object, $property, $value);
    }
  }

  /**
   * Gets an array with property definitions for all simple properties, this means they are stored in a column.
   *
   * @return array An array with property definitions
   */
  protected function getSimpleProperties()
  {
    $properties = array();

    foreach($this->getTableMap()->getColumns() as $column)
    {
      if(!$column->isForeignKey())
      {
        $properties[] = array('name' => ckString::lcfirst($column->getPhpName()), 'type' => $column->getType());
      }
    }

    return $properties;
  }

  /**
   * Gets an array with property definitions for all ends of *-to-one relations.
   *
   * @return array An array with property definitions
   */
  protected function getToOneProperties()
  {
    $properties = array();

    foreach($this->getTableMap()->getColumns() as $column)
    {
      if($column->isForeignKey())
      {
        $refClass = $this->getReferenceTableMap($column->getRelatedTableName())->getPhpName();

        $properties[] = array('name' => $refClass, 'type' => $refClass);
      }
    }

    return $properties;
  }

  /**
   * Gets an array with property definitions for all ends of *-to-many relations.
   *
   * @return array An array with property definitions
   */
  protected function getToManyProperties()
  {
    $properties = array();

    foreach($this->getClass()->getMethods() as $method)
    {
      if(ckString::startsWith($method->getName(), 'init'))
      {
        $refClass = substr($method->getName(), 4, -1);

        if($this->isToManyProperty($refClass))
        {
          $properties[] = array('name' => $refClass.'s', 'type' => $refClass.'[]');
        }
      }
    }

    return $properties;
  }

  /**
   * Checks if the given property name identifies an end of a *-to-many relation.
   *
   * @param string $property A property name
   *
   * @return boolean True, if the check is successful, false otherwise
   */
  protected function isToManyProperty($property)
  {
    $class = $this->getClass();

    return $class->hasMethod('init'.$property.'s') && $class->hasMethod('add'.$property) && $class->hasMethod('count'.$property.'s');
  }
}