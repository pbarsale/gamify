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
        $questions = Question::getAllQuestions();
        View::renderTemplate('Admin/question.html', array('badges' => $badges, 'questions' => $questions));
    }

    public function addAction()
    {
        if (isset($_POST['add'])) {
            Question::addQuestion($_POST['question'], $_POST['option'], $_POST['points'], $_POST['options'], $_POST['description'], $_POST['select-badge']);
        }
    }

    public function modifyAction()
    {
        if (isset($_POST['question'])) {
            $question = Question::getQuestionById(intval($_POST['question']));
            if ($question) {
                View::renderTemplate('Admin/editquestion.html', array('question' => $question));
            }
        }
    }

    public function updateAction()
    {
        if (isset($_POST['update'])) {
            Question::updateQuestion(intval($_POST['id']), $_POST['question'], $_POST['option'], $_POST['description']);
        }
    }



}