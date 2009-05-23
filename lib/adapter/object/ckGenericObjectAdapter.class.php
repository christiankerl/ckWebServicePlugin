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
 * ckGenericObjectAdapter
 *
 * @package    ckWebServicePlugin
 * @subpackage adapter
 * @author     Christian Kerl <christian-kerl@web.de>
 */
class ckGenericObjectAdapter
{
  protected $object;
  protected $propertyStrategy;

  public function __construct($object)
  {
    $this->object = $object;
  }

  protected function getType()
  {
    return new ReflectionAnnotatedClass($this->getObject());
  }

  protected function getPropertyStrategy()
  {
    if(is_null($this->propertyStrategy))
    {
      $this->propertyStrategy = ckAbstractPropertyStrategy::getPropertyStrategy($this->getType());
    }

    return $this->propertyStrategy;
  }

  protected function initializeObject()
  {
    return $this->object;
  }

  public function getObject()
  {
    if(is_null($this->object))
    {
      $this->object = $this->initializeObject();
    }

    return $this->object;
  }

  public function __get($property)
  {
    return ckObjectWrapper::wrap($this->getPropertyStrategy()->getPropertyValue($this->getObject(), $property));
  }

  public function __set($property, $value)
  {
    $this->getPropertyStrategy()->setPropertyValue($this->getObject(), $property, ckObjectWrapper::unwrap($value));
  }
}