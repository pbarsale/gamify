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
        $count = 0;
        if(isset($_POST['count']))
            $count = $_POST['count'];

        for ($x = 1; $x < $count; $x++) {
            $qid = 'questionid'.''.$x;
            $pid = 'points'.''.$x;
            $bid = 'badge_id'.''.$x;
            $oid = 'option'.''.$x;

            if(isset($_POST[$qid]) && isset($_POST[$pid]) && isset($_POST[$bid]) && isset($_POST[$oid])) {
                Quiz::calculatePoints(intval($_POST[$qid]),intval($_POST[$pid]),intval($_POST[$bid]),$_POST[$oid]);
            }
        }
        Flash::addMessage('Points Updated Successful');
        $this->redirect(Auth::getReturnToPage());
    }

    public function userAction()
    {
        $user = User::getUserByName($_GET['user']);
        View::renderTemplate('User/profile.html', array('user' => $user));
    }
}