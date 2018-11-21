<?php
/**
 * Created by PhpStorm.
 * User: priyankanaik
 * Date: 16/10/2018
 * Time: 23:44
 */
namespace App\Controllers;

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
        $points = LeaderBoard::getPointsOfUserForAdmin($user);
        $user->points = $points ? $points : 0;
        $badges = LeaderBoard::getBadgesOfUserForAdmin($user);
        $user->badges = $badges ? $badges : null;
        $notifications = Notification::getAllPendingScavengerHunt();
        View::renderTemplate('Admin/profile.html', array('user' => $user, 'notifications' => $notifications));
    }

    public function updateAction() {

    }

}