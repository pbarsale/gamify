<?php
namespace App\Controllers;

use App\Models\Scavenger;
use App\Models\User;
use \Core\View;
use \App\Models\Quiz;
use \App\Models\Question;
use \App\Models\Game;
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
        if(!(isset($_SESSION['user_id']) && isset($this->route_params['token']))){
            $url = '/museum/gamify/';
            $this->redirect($url);
        }

        $gameid = $this->route_params['token'];
        $game = Game::getGameById($gameid);

        if($game && $game->game_type_id==$this->QUIZ_CONST){
            $div_id = 0;
            $game_name = "Quiz";
            if(ISSET($_SESSION['div_id']))
                $div_id = $_SESSION['div_id']-1;

            $questions = Question::getAllQuestionsByGameId($gameid);
            $resource = Game::getResourceForId($gameid);
            if($resource)
                $game_name =$resource[0]['text'];

            for ($counter=0; $counter<sizeof($questions);$counter++) {
                $score = Question::getUserScoreQuiz($questions[$counter]['id']);
                $questions[$counter]['answered'] = $score['answered'];
                $questions[$counter]['userpoints'] = $score['userpoints'];
                $questions[$counter]['userbadge'] = $score['userbadge'];
            }
            View::renderTemplate('User/quiz.html',array('questions' => $questions,'gameid' => $gameid,
                'div_id' => $div_id,'game_name' => $game_name));
        }
        else if($game && $game->game_type_id==$this->SCAVENGER_HUNT_CONST){
            $this->scavengerAction();
        }
    }

    /**
     * Show the Scavenger Hunt page
     *
     * @return void
     */
    public function scavengerAction(){

        if(!(isset($_SESSION['user_id']) && isset($this->route_params['token']))){
            $url = '/museum/gamify/';
            $this->redirect($url);
        }

        $gameid = $this->route_params['token'];
        $game_name = "Scavenger Hunt";
        $questions = Question::getAllQuestionsByGameId($gameid);
        $resource = Game::getResourceForId($gameid);
        if($resource)
            $game_name =$resource[0]['text'];

        for ($counter=0; $counter<sizeof($questions);$counter++) {

            $scoreForQuestion = Question::getUserScoreForScavengerHuntQuestion($questions[$counter]['id']);
            //var_dump($scoreForQuestion);
            $questions[$counter]['answered'] = $scoreForQuestion['answered'];
            $questions[$counter]['userpoints'] = $scoreForQuestion['userpoints'];

            for ($opt=0; $opt<sizeof($questions[$counter]['options']);$opt++){
                $optionScore = Question::getUserScoreForScavengerHuntOption($questions[$counter]['id']
                                        ,$questions[$counter]['options'][$opt]['id']);

                $questions[$counter]['options'][$opt]['answered'] = $optionScore['answered'];
                $questions[$counter]['options'][$opt]['userpoints'] = $optionScore['userpoints'];
                $questions[$counter]['options'][$opt]['userbadge'] = $optionScore['userbadge'];
                $questions[$counter]['options'][$opt]['status'] = $optionScore['status'];
                $questions[$counter]['options'][$opt]['image'] = $optionScore['image'];
                $questions[$counter]['options'][$opt]['badge_img'] = $optionScore['badge_img'];
            }
        }
        View::renderTemplate('User/scavenger.html',array('questions' => $questions,'gameid' => $gameid,'game_name' => $game_name));
    }

    /**
     * Show the Quiz Answer page
     *
     * @return void
     */
    public function quizAnswerAction(){

        if(!isset($_POST['option'])){
            Flash::addMessage("Please select the answer","warning");
            $url = '/museum/gamify/users/quiz/'.$_POST['gameid'];
            $_SESSION['div_id'] = $_POST['div_id'];
            $this->redirect($url);
        }
        Quiz::calculatePoints(intval($_POST['questionid']),intval($_POST['points']),intval($_POST['badge_id']),$_POST['option']);
        //Flash::addMessage('Points Updated Successful');
        $url = '/museum/gamify/users/quiz/'.$_POST['gameid'];
        $_SESSION['div_id'] = $_POST['div_id'];
        $this->redirect($url);
    }


    public function scavengerAnswerAction(){

        if(!isset($_SESSION['user_id'])){
            $url = '/museum/gamify/';
            $this->redirect($url);
        }
        $message = Scavenger::calculateScore(intval($_POST['gameid']), intval($_POST['questionid']),intval($_POST['optionid']),
                                    intval($_POST['points']),intval($_POST['badge_id']),$_POST['iscorrect'],
                                    $_FILES['schunt']);

        if($message!="success")
            Flash::addMessage($message);

        $url = '/museum/gamify/users/quiz/'.$_POST['gameid'];
        $this->redirect($url);
    }

    public function profileAction(){
        View::renderTemplate('User/profile.html');
    }
}