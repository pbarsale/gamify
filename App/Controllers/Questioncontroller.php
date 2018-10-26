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
        Question::addQuestion($_POST['question'], $_POST['option'], $_POST['points'], $_POST['options'], $_POST['description'], $_POST['select-badge']);
    }

}