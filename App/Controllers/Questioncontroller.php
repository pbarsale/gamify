<?php
/**
 * Created by PhpStorm.
 * User: priyankanaik
 * Date: 16/10/2018
 * Time: 23:44
 */

namespace App\Controllers;

use App\Models\Badge;
use App\Models\Game;
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
        $badges = Badge::getAllBadges();
        $game_type_id = $_SESSION['game_type_id'];
        if ($game_type_id == 4) {
            View::renderTemplate('Admin/quiz.html', array('badges' => $badges, 'game_type_id' => $game_type_id));
        } else {
            View::renderTemplate('Admin/scavenger.html', array('badges' => $badges, 'game_type_id' => $game_type_id));
        }
    }

    public function addAction()
    {
        if (isset($_POST['add']) or isset($_POST['done'])) {
            Question::addQuestion($_POST['question'], $_POST['option'], $_POST['points'], isset($_POST['options']) ? $_POST['options'] : null, isset($_POST['description']) ? $_POST['description'] : null, $_POST['select-badge'], isset($_POST['select-badges']) ? $_POST['select-badges'] : null, isset($_POST['point']) ? $_POST['point'] : null);
        }
        if (isset($_POST['add'])) {
            $this->redirect('/museum/gamify/questioncontroller/new');
        } elseif (isset($_POST['done'])) {
            $this->redirect('/museum/gamify/gamecontroller/new');
        }
    }

    public function editAction()
    {
        $questions = Question::getAllQuestionsByGameId($_SESSION['game_id']);
        $_SESSION['game_type_id'] = Game::getGameType($_SESSION['game_id']);
        View::renderTemplate('Admin/questions.html', array('questions' => $questions));
    }

    public function modifyAction()
    {
        if (isset($_GET['question'])) {
            $question = Question::getQuestionById(intval($_GET['question']));
            if ($question) {
                View::renderTemplate('Admin/editquestion.html', array('question' => $question, 'game_type_id' => $_SESSION['game_type_id']));
            }
        }
    }

    public function updateAction()
    {
        if (isset($_POST['update'])) {
            Question::updateQuestion(intval($_POST['id']), $_POST['question'], $_POST['option'], $_POST['description']);
            $this->redirect('/museum/gamify/questioncontroller/edit');
        }
    }

}