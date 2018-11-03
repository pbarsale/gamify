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
        $game_types = GameType::getAllGameTypes();
        View::renderTemplate('User/leaderboard.html', array('game_types' => $game_types));
    }

    public function leaderboardAction() {
        $users = User::getAllUsersByUserAge($_SESSION['user_id']);
        $game_type = $_GET['game_type'];
        if($game_type === 'Quiz') {
            $leaderBoardUsers = LeaderBoard::getLeaderBoardForQuiz($users);
        } else {
            $leaderBoardUsers = LeaderBoard::getLeaderBoardForScavengerHunt($users);
        }
        View::renderTemplate('User/leaderboard1.html', array('users' => $leaderBoardUsers));
    }

}