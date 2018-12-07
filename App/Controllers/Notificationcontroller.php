<?php
/**
 * Created by PhpStorm.
 * User: priyankanaik
 * Date: 16/10/2018
 * Time: 23:44
 */
namespace App\Controllers;

use App\Flash;
use App\Models\Game;
use App\Models\Notification;
use \Core\View;
/**
 * GameType controller
 *
 * PHP version 7.0
 */
class Notificationcontroller extends \Core\Controller
{

    public function newAction()
    {
        $this->throwToLoginPage();
        $notifications = Notification::getAllPendingScavengerHunt();
        $pendingGames = Game::getAllPendingGames();
        View::renderTemplate('Admin/notification.html', array('notifications' => $notifications, 'pendingGames' => $pendingGames));
    }

    public function approveAction()
    {
        $this->throwToLoginPage();
        if(isset($_GET['question']) and isset($_GET['option']) and isset($_GET['user'])) {
            if (Notification::approvePendingRequest($_GET['question'], $_GET['option'], $_GET['user'])) {
                Flash::addMessage('User Request Approved!');
            } else {
                Flash::addMessage('User Request could not be Approved!', 'warning');
            }
        }
        $this->redirect('/museum/gamify/notificationcontroller/new');
    }

    public function denyAction()
    {
        $this->throwToLoginPage();
        if(isset($_GET['question']) and isset($_GET['option']) and isset($_GET['user'])) {
            if (Notification::denyPendingRequest($_GET['question'], $_GET['option'], $_GET['user'])) {
                Flash::addMessage('User Request Denied!');
            } else {
                Flash::addMessage('User Request could not be Denied!', 'warning');
            }
        }
        $this->redirect('/museum/gamify/notificationcontroller/new');
    }

    private function throwToLoginPage()
    {
        if (!isset($_SESSION['admin'])) {
            $this->redirect('/museum/gamify/admin/new');
        }
    }

}