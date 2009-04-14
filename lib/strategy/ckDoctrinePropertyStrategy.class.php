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
 * ckDoctrinePropertyStrategy is an implementation of ckAbstractPropertyStrategy,
 * which allows to access the properties of Doctrine_Record objects.
 *
 * @package    ckWebServicePlugin
 * @subpackage strategy
 * @author     Christian Kerl <christian-kerl@web.de>
 */
class ckDoctrinePropertyStrategy extends ckBeanPropertyStrategy
{
  protected static $TYPES = array(
    'string'  => array('string', 'blob', 'clob', 'timestamp', 'time', 'date', 'enum', 'gzip'),
    'boolean' => array('boolean'),
    'int'     => array('integer'),
    'double'  => array('float', 'decimal'),
  );

  /**
   * (non-PHPdoc)
   * @see strategy/ckAbstractPropertyStrategy#__construct()
   */
  public function __construct(ReflectionClass $class)
  {
    if(!$class->isSubclassOf('sfDoctrineRecord'))
    {
      throw new InvalidArgumentException(sprintf('The class \'%s\' has to be a subclass of sfDoctrineRecord.', $class->getName()));
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

    $table = Doctrine::getTable($this->getClass()->getName());

    $relations = array();

    foreach($table->getRelations() as $value)
    {
      $type = $value->isOneToOne() ? $value->getClass() : $value->getClass().'[]';

      $relations[$value->getLocalFieldName()] = array('name' => $value->getAlias(), 'type' => $type);
    }

    foreach($table->getColumns() as $name => $definition)
    {
      $fieldname = $table->getFieldName($name);

      if(!isset($relations[$fieldname]) || $definition['primary'])
      {
        $properties[] = array('name' => $this->getPropertyName($fieldname), 'type' => $this->getPropertyType($definition['type']));
      }
    }

    return array_merge($properties, array_values($relations));
  }

  protected function getPropertyName($fieldname)
  {
    return ckString::lcfirst(Doctrine_Inflector::classify($fieldname));
  }

  protected function getPropertyType($type)
  {
    foreach(self::$TYPES as $name => $types)
    {
      if(in_array($type, $types))
      {
        return $name;
      }
    }

    return null;
  }
}