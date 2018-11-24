<?php
/**
 * Created by PhpStorm.
 * User: priyankanaik
 * Date: 16/10/2018
 * Time: 23:44
 */
namespace App\Controllers;

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
        View::renderTemplate('Admin/notification.html', array('notifications' => $notifications));
    }

    public function approveAction()
    {
        $this->throwToLoginPage();
        Notification::approvePendingRequest($_GET['question'], $_GET['option'], $_GET['user']);
        $this->redirect('/museum/gamify/notificationcontroller/new');
    }

    public function denyAction()
    {
        $this->throwToLoginPage();
        Notification::denyPendingRequest($_GET['question'], $_GET['option'], $_GET['user']);
        $this->redirect('/museum/gamify/notificationcontroller/new');
    }

    private function throwToLoginPage()
    {
        if (!isset($_SESSION['admin'])) {
            $this->redirect('/museum/gamify/admin/new');
        }
    }

}