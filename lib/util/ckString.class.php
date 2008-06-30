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
 * ckString provides methods for string manipulation.
 *
 * @package    ckWebServicePlugin
 * @subpackage util
 * @author     Christian Kerl <christian-kerl@web.de>
 */
class ckString
{
  /**
   * Makes a string's first character lowercase.
   *
   * @param  string $str A string
   *
   * @return string      The string with first character lowercased
   */
  public static function lcfirst($str)
  {
    if(is_string($str) && strlen($str) > 0)
    {
      $str[0] = strtolower($str[0]);
    }

    return $str;
  }
  
  /**
   * Makes a string's first character uppercase.
   *
   * @param  string $str A string
   *
   * @return string      The string with first character uppercased
   */
  public static function ucfirst($str)
  {
    return ucfirst($str);
  }
  
  /**
   * Checks if a string starts with a given string.
   *
   * @param  string $str    A string
   * @param  string $substr A string to check against
   *
   * @return bool           True if str starts with substr
   */
  public static function startsWith($str, $substr)
  {
    if(is_string($str) && is_string($substr))
    {
      return $substr == substr($str, 0, strlen($substr));
    }
  }
  
  /**
   * Checks if a string ends with a given string.
   *
   * @param  string $str    A string
   * @param  string $substr A string to check against
   *
   * @return bool           True if str ends with substr
   */
  public static function endsWith($str, $substr)
  {
    if(is_string($str) && is_string($substr))
    {
      return $substr == substr($str, strlen($str) - strlen($substr));
    }
  }
}