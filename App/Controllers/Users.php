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
        $div_id = 0;
        if(ISSET($_SESSION['div_id']))
            $div_id = $_SESSION['div_id']-1;

        $gameid = $this->route_params['token'];
        $questions = Question::getAllQuestionsByGameId($gameid);
        for ($counter=0; $counter<sizeof($questions);$counter++) {
            $score = Question::getUserScoreQuiz($questions[$counter]['id']);
            $questions[$counter]['answered'] = $score['answered'];
            $questions[$counter]['userpoints'] = $score['userpoints'];
            $questions[$counter]['userbadge'] = $score['userbadge'];
        }
        View::renderTemplate('User/quiz.html',array('questions' => $questions,'gameid' => $gameid,'div_id' => $div_id));
    }

    /**
     * Show the Scavenger Hunt page
     *
     * @return void
     */
    public function scavengerAction(){
        View::renderTemplate('User/quiz.html');
    }

    /**
     * Show the Quiz Answer page
     *
     * @return void
     */
    public function quizAnswerAction(){
        Quiz::calculatePoints(intval($_POST['questionid']),intval($_POST['points']),intval($_POST['badge_id']),$_POST['option']);
        Flash::addMessage('Points Updated Successful');
        $url = '/museum/gamify/users/quiz/'.$_POST['gameid'];
        $_SESSION['div_id'] = $_POST['div_id'];
        $this->redirect($url);
    }

    public function userAction(){
        $user = User::getUserByName($_GET['user']);
        View::renderTemplate('User/profile.html', array('user' => $user));
    }
}