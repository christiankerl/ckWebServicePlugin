<?php
/**
 * This file is part of the ckWebServicePlugin
 *
 * @package    ckWebServicePlugin
 * @subpackage test
 * @author     Nicolas Martin <email.de.nicolas.martin@gmail.com>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT License
 * @version    SVN: $Id$
 */

/**
 * testSoapClient enables local SOAP request without HTTP layer
 *
 * @package    ckWebServicePlugin
 * @subpackage test
 * @author     Nicolas Martin <email.de.nicolas.martin@gmail.com>
 */
class testSoapClient extends SoapClient
{
  public function __construct($wsdl, $options)
  {
    parent::__construct($wsdl, $options);
  }

  /**
   * Warnings have to be disabled here. The reason is if some output had been
   * previously printed to stdout, SoapServer::handle throws a PHP warning.
   * Since most of the time some text is already printed at this point (via the
   * $t->diag() for example, the sfTestBrowser intercept this warning and
   * throw\" an Except\"ion, which cause conflict with SoapFault exceptions) 
   *
   * @see sfTestBrowser.class.php (custom error handler) 
   */
  public function __doRequest($request, $location, $action, $version) 
  {
    ob_start();
    @$this->server->handle($request);
    $this->response = ob_get_contents();
    ob_end_clean();

    return $this->response;
  }
  public function setSoapServer(SoapServer $server)
  {
    $this->server = $server;
  }
}
