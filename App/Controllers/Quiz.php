<?php
namespace App\Controllers;

use \Core\View;
use \App\Models\User;

class Quiz extends \Core\Controller
    {
    /**
    * Show the quiz page
    *
    * @return void
    */
    public function newAction()
    {
        View::renderTemplate('User/quiz.html');
    }
}