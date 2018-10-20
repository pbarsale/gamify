<?php
/**
 * Created by PhpStorm.
 * User: priyankanaik
 * Date: 16/10/2018
 * Time: 23:44
 */
namespace App\Controllers;

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
        View::renderTemplate('Admin/question.html');
    }

    public function addAction()
    {
        $options = $_POST['optionA1'] . "#$#" . $_POST['optionA2'] . "#$#" . $_POST['optionA3'] . "#$#" . $_POST['optionA4'];
        $answer = '';
        if(!empty($_POST['options'])) {
            foreach($_POST['options'] as $selected) {
                $answer .= $_POST[$selected] . "#$#";
            }
        }
        Question::addQuestion($_POST['question'], $options, $_POST['points'], $answer, $_POST['description']);
    }

}