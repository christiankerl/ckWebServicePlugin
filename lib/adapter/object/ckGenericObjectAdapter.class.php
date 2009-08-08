<?php
/**
 * This file is part of the ckWebServicePlugin
 *
 * @package   ckWebServicePlugin
 * @author    Christian Kerl <christian-kerl@web.de>
 * @copyright Copyright (c) 2009, Christian Kerl
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 * @version   SVN: $Id$
 */

/**
 * ckGenericObjectAdapter adapts an object so all its properties determined by the associated
 * ckAbstratcPropertyStrategy are accessible like public properties.
 *
 * @package    ckWebServicePlugin
 * @subpackage adapter
 * @author     Christian Kerl <christian-kerl@web.de>
 */
class ckGenericObjectAdapter
{
  protected $object;
  protected $propertyStrategy;

  /**
   * Constructor initializing the ckGenericObjectAdapter with a given object.
   *
   * @param $object The object to adapt
   */
  public function __construct($object)
  {
    $this->object = $object;
  }

  /**
   * Gets the ReflectionAnnotatedClass of the managed object.
   *
   * @return ReflectionAnnotatedClass The ReflectionAnnotatedClass of the managed object
   */
  protected function getType()
  {
    return new ReflectionAnnotatedClass($this->getObject());
  }

  /**
   * Gets the ckAbstractPropertyStrategy associated with the managed object.
   *
   * @return ckAbstractPropertyStrategy The ckAbstractPropertyStrategy associated with the managed object
   */
  protected function getPropertyStrategy()
  {
    if(is_null($this->propertyStrategy))
    {
      $this->propertyStrategy = ckAbstractPropertyStrategy::getPropertyStrategy($this->getType());
    }

    return $this->propertyStrategy;
  }

  /**
   * Initializes the managed object. After this method was called the managed object has to accept
   * property updates.
   *
   * @return object The initialized object.
   */
  protected function initializeObject()
  {
    return $this->object;
  }

  /**
   * Gets the managed object.
   *
   * @return object
   */
  public function getObject()
  {
    if(is_null($this->object))
    {
      $this->object = $this->initializeObject();
    }

    return $this->object;
  }

  /**
   * Gets the given property of the managed object through its property strategy.
   * The property value is wrapped in a SOAP-processable type using ckObjectWrapper::wrap().
   *
   * @param string $property The property to get
   *
   * @return object
   */
  public function __get($property)
  {
    return ckObjectWrapper::wrap($this->getPropertyStrategy()->getPropertyValue($this->getObject(), $property));
  }

  /**
   * Sets the given property of the managed object to the given value through its property strategy.
   * The value is unwrapped from a SOAP-processable type using ckObjectWrapper::unwrap().
   *
   * @param string $property The property to set
   * @param mixed $value The new value
   */
  public function __set($property, $value)
  {
    $this->getPropertyStrategy()->setPropertyValue($this->getObject(), $property, ckObjectWrapper::unwrap($value));
  }
}