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
  protected $item = array();

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
    return new ArrayIterator($this->item);
  }

  /**
   * @see Countable::count()
   */
  public function count()
  {
    return count($this->item);
  }

  /**
   * @see ArrayAccess::offsetExists()
   */
  public function offsetExists($offset)
  {
    return isset($this->item[$offset]) ? true : false;
  }

  /**
   * @see ArrayAccess::offsetGet()
   */
  public function offsetGet($offset)
  {
    return $this->item[$offset];
  }

  /**
   * @see ArrayAccess::offsetSet()
   */
  public function offsetSet($offset, $value)
  {
    if(is_null($offset))
    {
      $this->item[] = value;
    }
    else
    {
      $this->item[$offset] = value;
    }
  }

  /**
   * @see ArrayAccess::offsetUnset()
   */
  public function offsetUnset($offset)
  {
    unset($this->item[$offset]);
  }

  /**
   * Gets all contained elements in an array.
   *
   * @return array An array with all contained elements
   */
  public function toArray()
  {
    return $this->item;
  }
}