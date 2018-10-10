<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;

/**
 * Authenticated controller
 *
 * PHP version 7.0
 */
abstract class Authenticated extends \Core\Controller
{
	/**
     * Require the user to be authenticated before giving access to all methods in the container
     *
     * @return void
     */
    protected function before(){
    	$this->requireLogin();
    }
}