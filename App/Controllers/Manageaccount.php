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
use App\Models\LeaderBoard;
use App\Models\Notification;
use App\Models\User;
use \Core\View;
/**
 * GameType controller
 *
 * PHP version 7.0
 */
class Manageaccount extends \Core\Controller
{
    public function newAction()
    {
        $this->throwToLoginPage();
        $users = User::getAllUsers();
        $notifications = Notification::getAllPendingScavengerHunt();
        $pendingGames = Game::getAllPendingGames();
        View::renderTemplate('Admin/accountsearch.html', array('users' => $users, 'notifications' => $notifications, 'pendingGames' => $pendingGames));
    }

    public function blockAction() {
        $this->throwToLoginPage();
        if(isset($_GET['user_id']) and isset($_GET['block'])) {
            User::blockUser($_GET['user_id'], $_GET['block']);
        }
        $this->redirect('/museum/gamify/manageaccount/new');
    }

    public function editAction() {
        $this->throwToLoginPage();
        if(isset($_GET['user'])) {
            $user = User::findById($_GET['user']);
            if ($user) {
                $points = LeaderBoard::getPointsOfUser($user);
                $user->points = $points ? $points : 0;
                $badges = LeaderBoard::getBadgesOfUser($user);
                $user->badges = $badges ? $badges : null;
            } else {
                Flash::addMessage('User Not Found!', 'warning');
            }
        }
        $notifications = Notification::getAllPendingScavengerHunt();
        $allBadges = Badge::getAllBadges();
        $pendingGames = Game::getAllPendingGames();
        View::renderTemplate('Admin/profile.html', array('user' => $user, 'notifications' => $notifications, 'badges' => $allBadges, 'pendingGames' => $pendingGames));
    }

    public function updateAction() {
        $this->throwToLoginPage();
        if(isset($_POST['update'])) {
            if (!empty($_POST['points']) && $_POST['existing-points'] + $_POST['points'] < 0) {
                Flash::addMessage('Points are low to be updated!', 'warning');
                $this->redirect('/museum/gamify/manageaccount/edit?user=' . $_POST['user_id']);
            }
            if(!empty($_POST['points'])) {
                User::updatePoints($_POST['points'], $_POST['user_id']);
            }
            if ($_POST['select-badge'] != 0) {
                User::addBadges($_POST['select-badge'], $_POST['user_id']);
            }
            Flash::addMessage('Points and/or Badges Updated Successfully!');
        }
        $this->redirect('/museum/gamify/manageaccount/edit?user=' . $_POST['user_id']);
    }

    private function throwToLoginPage()
    {
        if (!isset($_SESSION['admin'])) {
            $this->redirect('/museum/gamify/admin/new');
        }
    }

}