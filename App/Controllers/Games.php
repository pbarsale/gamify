<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;

/**
 * Games controller
 *
 * PHP version 7.0
 */
class Games extends Authenticated
{
	/**
     * Show the index page
     *
     * @return void
     */
    public function indexAction()
    {
    	
        View::renderTemplate('Games/index.html');
    }
}


