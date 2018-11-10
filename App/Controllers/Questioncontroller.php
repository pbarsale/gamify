<?php
/**
 * Created by PhpStorm.
 * User: priyankanaik
 * Date: 16/10/2018
 * Time: 23:44
 */

namespace App\Controllers;

use App\Models\Badge;
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
        View::renderTemplate('Admin/question.html', array('badges' => $badges));
    }

    public function addAction()
    {
        if (isset($_POST['add']) || isset($_POST['done'])) {
            Question::addQuestion($_POST['question'], $_POST['option'], $_POST['points'], $_POST['options'], $_POST['description'], $_POST['select-badge']);
        }
//        if(isset($_POST['add'])) {
//            $this->redirect('/museum/gamify/questioncontroller/new');
//        } else if(isset($_POST['done'])) {
//            $this->redirect('/museum/gamify/gamecontroller/new');
//        }
    }

    public function editAction()
    {
        $questions = Question::getAllQuestionsByGameId($_SESSION['game_id']);
        View::renderTemplate('Admin/questions.html', array('questions' => $questions));
    }

    public function modifyAction()
    {
        if (isset($_GET['question'])) {
            $question = Question::getQuestionById(intval($_GET['question']));
            if ($question) {
                View::renderTemplate('Admin/editquestion.html', array('question' => $question));
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