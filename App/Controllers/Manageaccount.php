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
        $users = User::getAllUsers();
        $notifications = Notification::getAllPendingScavengerHunt();
        View::renderTemplate('Admin/accountsearch.html', array('users' => $users, 'notifications' => $notifications));
    }

    public function userAction()
    {
        $user = User::getUserByName($_GET['user']);
        $notifications = Notification::getAllPendingScavengerHunt();
        View::renderTemplate('Admin/profile.html', array('user' => $user, 'notifications' => $notifications));
    }

    public function blockAction() {
        User::blockUser($_GET['user_id'], $_GET['block']);
        $this->redirect('/museum/gamify/manageaccount/new');
    }

    public function editAction() {
        $user = User::findById($_GET['user']);
        $points = LeaderBoard::getPointsOfUser($user);
        $user->points = $points ? $points : 0;
        $badges = LeaderBoard::getBadgesOfUser($user);
        $user->badges = $badges ? $badges : null;
        $notifications = Notification::getAllPendingScavengerHunt();
        $allBadges = Badge::getAllBadges();
        View::renderTemplate('Admin/profile.html', array('user' => $user, 'notifications' => $notifications, 'badges' => $allBadges));
    }

    public function updateAction() {
        if($_POST['existing-points'] + $_POST['points'] <= 0) {
            Flash::addMessage('Points are low to be updated!', 'warning');
            $this->redirect('/museum/gamify/manageaccount/edit?user=' . $_POST['user_id']);
        }
        User::updatePoints($_POST['points'], $_POST['user_id']);
        if($_POST['select-badge'] !== 0) {
            User::addBadges($_POST['select-badge'], $_POST['user_id']);
        }
        Flash::addMessage('Points and Badges Updated Successfully!');
        $this->redirect('/museum/gamify/manageaccount/edit?user=' . $_POST['user_id']);
    }

}