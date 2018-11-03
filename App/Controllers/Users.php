<?php
namespace App\Controllers;

use App\Models\User;
use \Core\View;
use \App\Models\Quiz;
use \App\Models\Question;
use \App\Flash;
use \App\Auth;

class Users extends \Core\Controller
    {
    /**
    * Show the quiz page
    *
    * @return void
    */
    public function quizAction()
    {
        $gameid = $this->route_params['token'];
        $questions = Question::getAllQuestionsByGameId($gameid);    
        //var_dump($questions);    
        View::renderTemplate('User/quiz.html',array('questions' => $questions));
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

    /**
     * Show the Quiz Answer page
     *
     * @return void
     */
    public function quizAnswerAction()
    {
        // View::renderTemplate('User/quiz.html');
        Quiz::calculatePoints(intval($_POST['questionid']),intval($_POST['points']),intval($_POST['badge_id']),$_POST['option']);
        Flash::addMessage('Points Updated Successful');
        $this->redirect(Auth::getReturnToPage());
    }

    public function userAction()
    {
        $user = User::getUserByName($_GET['user']);
        View::renderTemplate('User/profile.html', array('user' => $user));
    }
}