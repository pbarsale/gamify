<?php
/**
 * Created by PhpStorm.
 * User: priyankanaik
 * Date: 16/10/2018
 * Time: 23:44
 */

namespace App\Controllers;

use App\Models\AgeGroup;
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
        $this->throwToLoginPage();
        if(isset($_SESSION['user_id'])) {
            $user = User::findById($_SESSION['user_id']);
            if ($user) {
                $user->getAgeFromBirthDate();
                $userAgeGroup = AgeGroup::getAgeGroupIdByAge($user->age);
                $users = User::getAllUsersByUserAge($userAgeGroup);
                $leaderBoardUsers = $users ? LeaderBoard::getLeaderBoard($users) : array();
                View::renderTemplate('User/leaderboard.html', array('users' => $leaderBoardUsers, 'ageGroup' => $userAgeGroup));
            }
        }
    }

    private function throwToLoginPage()
    {
        if (!isset($_SESSION['admin'])) {
            $this->redirect('/museum/gamify/admin/new');
        }
    }
}