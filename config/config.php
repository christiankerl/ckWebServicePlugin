<?php

/**
 * This file is part of the ckWebServicePlugin
 * 
 * @package   ckWebServicePlugin
 * @author    Sven Lauritzen <the-pulse@gmx.net>
 * @copyright Copyright (c) 2008, Sven Laurtizen
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 * @version   SVN: $Id$
 */

sfMixer::register('sfComponent',
                  array('ckWebServiceComponent', 'isSoapRequest'));
