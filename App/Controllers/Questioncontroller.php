<?php
/**
 * Created by PhpStorm.
 * User: priyankanaik
 * Date: 16/10/2018
 * Time: 23:44
 */

namespace App\Controllers;

use App\Flash;
use App\Models\Badge;
use App\Models\Game;
use App\Models\Notification;
use \Core\View;
use \App\Models\Question;

/**
 * GameType controller
 *
 * PHP version 7.0
 */
class Questioncontroller extends \Core\Controller
{
    public function newAction()
    {
        $this->throwToLoginPage();
        $badges = Badge::getAllBadges();
        $notifications = Notification::getAllPendingScavengerHunt();
        $game_type_id = $_SESSION['game_type_id'];
        if(isset($_POST['game_type_id'])) {
            $game_type_id = $_SESSION['game_type_id'] = $_POST['game_type_id'];
        }
        $pendingGames = Game::getAllPendingGames();
        $game_id = $_SESSION['game_id'];
        if(isset($_POST['game_id'])) {
            $game_id = $_SESSION['game_id'] = $_POST['game_id'];
        }
        if ($game_type_id == 4) {
            $count = count(Question::getAllQuestionsByGameId($game_id));
            $nextquestion = Question::getNextQuestion($game_id, isset($_SESSION['question_id']) ? $_SESSION['question_id'] : null);
            $prevquestion = Question::getPreviousQuestion($game_id, $nextquestion['id']);
            View::renderTemplate('Admin/quiz.html', array('badges' => $badges, 'game_type_id' => $game_type_id, 'notifications' => $notifications, 'prevquestion' => $nextquestion, 'isPrevQ' => $prevquestion, 'count' => $count, 'pendingGames' => $pendingGames));
        } else {
            $question = Question::getAllQuestionsByGameId($game_id);
            View::renderTemplate('Admin/scavenger.html', array('badges' => $badges, 'game_type_id' => $game_type_id, 'notifications' => $notifications, 'pendingGames' => $pendingGames, 'question' => $question));
        }
    }

    public function prevAction()
    {
        $this->throwToLoginPage();
        $badges = Badge::getAllBadges();
        $notifications = Notification::getAllPendingScavengerHunt();
        $game_type_id = $_SESSION['game_type_id'];
        $pendingGames = Game::getAllPendingGames();
        if ($game_type_id == 4) {
            $count = count(Question::getAllQuestionsByGameId($_SESSION['game_id']));
            $prevquestion = Question::getPreviousQuestion($_SESSION['game_id'], $_SESSION['question_id']);
            $prePrevQuestion = Question::getPreviousQuestion($_SESSION['game_id'], $prevquestion['id']);
            View::renderTemplate('Admin/quiz.html', array('badges' => $badges, 'game_type_id' => $game_type_id, 'notifications' => $notifications, 'prevquestion' => $prevquestion, 'isPrevQ' => $prePrevQuestion, 'count' => $count, 'pendingGames' => $pendingGames));
        } else {
            View::renderTemplate('Admin/scavenger.html', array('badges' => $badges, 'game_type_id' => $game_type_id, 'notifications' => $notifications, 'pendingGames' => $pendingGames));
        }
    }

    public function addAction()
    {
        $this->throwToLoginPage();
        if (isset($_POST['add']) or isset($_POST['done'])) {
            if(isset($_POST['question_id'])) {
                if(Question::updatePrevQuestion($_POST['question_id'], $_POST['prev-question'], $_POST['prev-option'], $_POST['prev-option_id'], isset($_POST['prev-points']) ? $_POST['prev-points'] : null, isset($_POST['prev-options']) ? $_POST['prev-options'] : null, isset($_POST['prev-description']) ? $_POST['prev-description'] : null, isset($_POST['prev-select-badge']) ? $_POST['prev-select-badge'] : null, isset($_POST['prev-select-badges']) ? $_POST['prev-select-badges'] : null, isset($_POST['prev-point']) ? $_POST['prev-point'] : null)) {
                    Flash::addMessage('Question Updated Successfully!');
                } else {
                    Flash::addMessage('Question Update Failed!', 'warning');
                }
            } else {
                if (Question::addQuestion($_POST['question'], $_POST['option'], isset($_POST['points']) ? $_POST['points'] : null, isset($_POST['options']) ? $_POST['options'] : null, isset($_POST['description']) ? $_POST['description'] : null, isset($_POST['select-badge']) ? $_POST['select-badge'] : null, isset($_POST['select-badges']) ? $_POST['select-badges'] : null, isset($_POST['point']) ? $_POST['point'] : null)) {
                    Flash::addMessage('Question Added Successfully!');
                } else {
                    Flash::addMessage('Question Addition Failed!', 'warning');
                }
            }
        }
        $_SESSION['question_id'] = isset($_POST['question_id']) ? intval($_POST['question_id']) : null;
        if (isset($_POST['add'])) {
            $this->redirect('/museum/gamify/questioncontroller/new');
        } else if (isset($_POST['prev'])) {
            if(isset($_POST['question_id'])) {
                if(Question::updatePrevQuestion($_POST['question_id'], $_POST['prev-question'], $_POST['prev-option'], $_POST['prev-option_id'], isset($_POST['prev-points']) ? $_POST['prev-points'] : null, isset($_POST['prev-options']) ? $_POST['prev-options'] : null, isset($_POST['prev-description']) ? $_POST['prev-description'] : null, isset($_POST['prev-select-badge']) ? $_POST['prev-select-badge'] : null, isset($_POST['prev-select-badges']) ? $_POST['prev-select-badges'] : null, isset($_POST['prev-point']) ? $_POST['prev-point'] : null)) {
                    Flash::addMessage('Question Updated Successfully!');
                } else {
                    Flash::addMessage('Question Update Failed!', 'warning');
                }
            }
            $this->redirect('/museum/gamify/questioncontroller/prev');
        } elseif (isset($_POST['done'])) {
            $this->redirect('/museum/gamify/gamecontroller/new');
        }
    }

    public function editAction()
    {
        $this->throwToLoginPage();
        $questions = Question::getAllQuestionsByGameId($_SESSION['game_id']);
        $notifications = Notification::getAllPendingScavengerHunt();
        $_SESSION['game_type_id'] = Game::getGameType($_SESSION['game_id']);
        View::renderTemplate('Admin/questions.html', array('questions' => $questions, 'notifications' => $notifications));
    }

    public function modifyAction()
    {
        $this->throwToLoginPage();
        $notifications = Notification::getAllPendingScavengerHunt();
        if (isset($_GET['question'])) {
            $question = Question::getQuestionById(intval($_GET['question']));
            if ($question) {
                View::renderTemplate('Admin/editquestion.html', array('question' => $question, 'game_type_id' => $_SESSION['game_type_id'], 'notifications' => $notifications));
            } else {
                Flash::addMessage('Question Not Found!', 'warning');
                $this->redirect('/museum/gamify/questioncontroller/edit');
            }
        }
    }

    public function updateAction()
    {
        $this->throwToLoginPage();
        if (isset($_POST['update'])) {
            if(Question::updateQuestion(intval($_POST['id']), $_POST['question'], $_POST['option'], isset($_POST['description']) ? $_POST['description'] : null)) {
                Flash::addMessage('Question Updated Successfully!');
            } else {
                Flash::addMessage('Question Update Failed!', 'warning');
                $this->redirect('/museum/gamify/questioncontroller/update');
            }
        }
        $this->redirect('/museum/gamify/questioncontroller/edit');
    }

    private function throwToLoginPage()
    {
        if (!isset($_SESSION['admin'])) {
            $this->redirect('/museum/gamify/admin/new');
        }
    }

}