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
 * ckGenericArray generic array implementation to map WS-I compliant xml array definitions to PHP accessable arrays.
 *
 * @package    ckWebServicePlugin
 * @subpackage util
 * @author     Christian Kerl <christian-kerl@web.de>
 */
class ckGenericArray implements IteratorAggregate, ArrayAccess, Countable
{
  /**
   * The elements of the array.
   *
   * @var array
   */
  protected $_item = array();

  /**
   * True if the 'item' property was set, false otherwise.
   *
   * @var bool
   */
  protected $initialized = false;

  /**
   * Default constructor initializing the generic array with the values from a given array.
   *
   * @param array $items An array
   */
  public function __construct($items = array())
  {
    $this->item = $items;
  }

  /**
   * @see IteratorAggregate::getIterator()
   */
  public function getIterator()
  {
    return new ArrayIterator($this->_item);
  }

  /**
   * @see Countable::count()
   */
  public function count()
  {
    return count($this->_item);
  }

  /**
   * @see ArrayAccess::offsetExists()
   */
  public function offsetExists($offset)
  {
    return isset($this->_item[$offset]) ? true : false;
  }

  /**
   * @see ArrayAccess::offsetGet()
   */
  public function offsetGet($offset)
  {
    return $this->_item[$offset];
  }

  /**
   * @see ArrayAccess::offsetSet()
   */
  public function offsetSet($offset, $value)
  {
    if(is_null($offset))
    {
      $this->_item[] = $value;
    }
    else
    {
      $this->_item[$offset] = $value;
    }
  }

  /**
   * @see ArrayAccess::offsetUnset()
   */
  public function offsetUnset($offset)
  {
    unset($this->_item[$offset]);
  }

  /**
   * Gets all contained elements in an array.
   *
   * @return array An array with all contained elements
   */
  public function toArray()
  {
    return array_map(array(__CLASS__, 'toArrayDeep'), $this->_item);
  }

  /**
   * Sets the 'item' property once, so a bug with arrays containing only one value is fixed.
   *
   * @param string $property A property name
   * @param mixed  $value    A property value
   */
  public function __set($property, $value)
  {
    if($property == 'item' && !$this->initialized)
    {
      if(!is_array($value))
      {
        $value = array($value);
      }

      $this->_item = $value;
      $this->initialized = true;
    }
  }

  /**
   * If a given value is a ckGenericArray object its toArray() method is called, otherwise the given value is returned.
   *
   * @param mixed $input A value
   *
   * @return mixed The given value, or the result of toArray() if it is a ckGenericArray
   */
  protected static function toArrayDeep($input)
  {
    return $input instanceof ckGenericArray ? $input->toArray() : $input;
  }
}