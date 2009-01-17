<?php

/**
 * math actions.
 *
 * @package    project
 * @subpackage math
 * @author     Christian Kerl <christian-kerl@web.de>
 * @version    SVN: $Id$
 */
class mathActions extends sfActions
{
  /**
   * An action multiplying two numbers.
   *
   * @WSMethod(webservice='MathApi')
   *
   * @param float $a Factor A
   * @param float $b Factor B
   *
   * @return float The result
   */
  public function executeMultiply($request)
  {
    $factorA = $request->getParameter('a');
    $factorB = $request->getParameter('b');

    if(is_numeric($factorA) && is_numeric($factorB))
    {
      $this->result = $factorA * $factorB;

      return sfView::SUCCESS;
    }
    else
    {
      return sfView::ERROR;
    }
  }

  /**
   * An action multiplying two numbers.
   *
   * @WSMethod(name='SimpleMultiply', webservice='MathApi')
   *
   * @param float $a Factor A
   * @param float $b Factor B
   *
   * @return float The result
   */
  public function executeSimpleMultiply($request)
  {
    $this->forward('math', 'multiply');
  }

  /**
   * An action multiplying two numbers.
   *
   * @WSMethod(name='SimpleMultiplyWithHeader', webservice='MathApi')
   * @WSHeader(name='AuthHeader', type='AuthData')
   *
   * @param float $a Factor A
   * @param float $b Factor B
   *
   * @return float The result
   */
  public function executeSimpleMultiplyWithHeader($request)
  {
    $factorA = $request->getParameter('a');
    $factorB = $request->getParameter('b');

    if($this->getUser()->isAuthenticated() && is_numeric($factorA) && is_numeric($factorB))
    {
      $this->result = $factorA * $factorB;

      return sfView::SUCCESS;
    }
    else
    {
      return sfView::ERROR;
    }
  }

  /**
   * An action multiplying any number of factors.
   *
   * @WSMethod(name='ArrayMultiply', webservice='MathApi')
   *
   * @param float[] $factors An array of factors
   *
   * @return float The result
   */
  public function executeArrayMultiply($request)
  {
    $this->result = 1;

    foreach($request->getParameter('factors') as $factor)
    {
      $this->result *= $factor;
    }
  }

  /**
   * An action multiplying any number of complex factors.
   *
   * @WSMethod(name='ComplexMultiply', webservice='MathApi')
   *
   * @param ComplexNumber[] $input
   *
   * @return ComplexNumber
   */
  public function executeComplexMultiply($request)
  {
    $this->result = new ComplexNumber(1, 0);

    foreach($request->getParameter('input') as $c)
    {
      $this->result = $this->result->multiply($c);
    }
  }
}
