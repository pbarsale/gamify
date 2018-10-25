<?php
namespace App\Controllers;

use \Core\View;
use \App\Models\User;

class Scavenger extends \Core\Controller
{
    /**
    * Show the Scavenger page
    *
    * @return void
    */
    public function newAction()
    {
        View::renderTemplate('User/scavenger.html');
    }
}