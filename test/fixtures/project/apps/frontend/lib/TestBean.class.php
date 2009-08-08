<?php


/**
 * @PropertyStrategy('ckBeanPropertyStrategy')
 */
class TestBean
{
  private $_data;

  public function __construct($data)
  {
    $this->setData($data);
  }

  /**
   *
   * @return string
   */
  public function getData()
  {
    return $this->_data;
  }

  public function setData($value)
  {
    $this->_data = $value;
  }
}