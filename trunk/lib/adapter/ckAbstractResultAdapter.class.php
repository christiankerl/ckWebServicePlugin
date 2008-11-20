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
 * ckAbstractResultAdapter provides common methods, shared by all result adapters.
 * Result adapters are used to get the result of an action called through a webservice api.
 *
 * @package    ckWebServicePlugin
 * @subpackage adapter
 * @author     Christian Kerl <christian-kerl@web.de>
 */
abstract class ckAbstractResultAdapter
{
  /**
   * Gets the render mode required by the result adapter.
   *
   * @return int A render mode
   */
  public function getRenderMode()
  {
    return sfView::RENDER_NONE;
  }

  /**
   * Constructor initializing the result adapter with a given array of adapter specific parameters.
   *
   * @param array $parameters An array of adapter specific parameters
   */
  public function __construct($parameters = array())
  {

  }

  /**
   * Gets the result from a given action instance.
   *
   * @param sfAction $action An action instance
   */
  public abstract function getResult(sfAction $action);
}