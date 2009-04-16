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
 * ckAbstractMemberResultAdapter is an abstract base class for result adapters, which
 * get the result of an action from a class member (property or method). If the result is
 * an ORM object it is adapted to be serializable to its xml representation.
 *
 * @package    ckWebServicePlugin
 * @subpackage adapter
 * @author     Christian Kerl <christian-kerl@web.de>
 */
abstract class ckAbstractMemberResultAdapter extends ckAbstractResultAdapter
{
  /**
   * Constructor initializing the result adapter with a given array of adapter specific parameters.
   *
   * @param array $parameters An array of adapter specific parameters
   */
  public function __construct($parameters = array())
  {

  }

  /**
   * (non-PHPdoc)
   * @see lib/adapter/ckAbstractResultAdapter#getResult()
   */
  public function getResult(sfAction $action)
  {
    $result = null;
    $_result = $this->doGetResult($action);

    if($_result instanceof Doctrine_Record)
    {
      $_result = array($_result);
    }

    if(is_array($_result) || $_result instanceof Traversable)
    {
      $result = array();

      foreach($_result as $object)
      {
        $result[] = $object instanceof Doctrine_Record ? ckDoctrineCollectionAdapter::adaptCollectionsToArray($object) : $object;
      }
    }

    return $result;
  }

  /**
   * Gets the result from an action class member.
   *
   * @param $action An action instance
   */
  protected abstract function doGetResult(sfAction $action);
}
