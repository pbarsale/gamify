<?php
/**
 * Created by PhpStorm.
 * User: priyankanaik
 * Date: 16/10/2018
 * Time: 23:44
 */

namespace App\Controllers;

use App\Models\GameType;
use App\Models\LeaderBoard;
use App\Models\User;
use \Core\View;
/**
 * GameType controller
 *
 * PHP version 7.0
 */
class Leaderboardcontroller extends \Core\Controller
{
    public function newAction()
    {
        $users = User::getAllUsersByUserAge($_SESSION['user_id']);
        $leaderBoardUsers = LeaderBoard::getLeaderBoard($users);
        View::renderTemplate('User/leaderboard.html', array('users' => $leaderBoardUsers));
    }

}