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
        View::renderTemplate('Admin/accountsearch.html', array('users' => $users, 'notifications' => $notifications));
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
        View::renderTemplate('Admin/profile.html', array('user' => $user, 'notifications' => $notifications, 'badges' => $allBadges));
    }

    public function updateAction() {
        $this->throwToLoginPage();
        if(isset($_POST['update'])) {
            if (!empty($_POST['points']) && $_POST['existing-points'] + $_POST['points'] <= 0) {
                Flash::addMessage('Points are low to be updated!', 'warning');
                $this->redirect('/museum/gamify/manageaccount/edit?user=' . $_POST['user_id']);
            }
            $flag = false;
            if(!empty($_POST['points'])) {
                $flag = User::updatePoints($_POST['points'], $_POST['user_id']);
            }
            if ($_POST['select-badge'] != 0) {
                $flag = User::addBadges($_POST['select-badge'], $_POST['user_id']);
            }
            if($flag) {
                Flash::addMessage('Points and/or Badges Updated Successfully!');
            }
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