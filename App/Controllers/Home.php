<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;

/**
 * Home controller
 *
 * PHP version 7.0
 */
class Home extends \Core\Controller
{

    /**
     * Show the index page
     *
     * @return void
     */
    public function indexAction()
    {
        $_SESSION['admin'] = false;
    	// Every view should have the logged in user data
        //\App\Mail::send('pbarsale@buffalo.edu','Hello','Hi Pratibha','<h1>Hi Pratibha<h1>');
        View::renderTemplate('Home/index.html');
    }
}
