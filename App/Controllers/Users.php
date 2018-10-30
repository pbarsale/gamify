<?php
namespace App\Controllers;

use \Core\View;
use \App\Models\Quiz;


class Users extends \Core\Controller
    {
    /**
    * Show the quiz page
    *
    * @return void
    */
    public function quizAction()
    {
        $games = Quiz::getAllGames();
        View::renderTemplate('User/quiz.html',array('games' => $games));
    }

    /**
     * Show the Scavenger Hunt page
     *
     * @return void
     */
    public function scavengerAction()
    {
        View::renderTemplate('User/quiz.html');
    }
}