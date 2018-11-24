<?php
/**
 * Created by PhpStorm.
 * User: priyankanaik
 * Date: 16/10/2018
 * Time: 23:44
 */
namespace App\Controllers;

use App\Flash;
use App\Models\Notification;
use \Core\View;
use \App\Models\Badge;

/**
 * GameType controller
 *
 * PHP version 7.0
 */
class Badgecontroller extends \Core\Controller
{
    public function newAction()
    {
        $this->throwToLoginPage();
        $badges = Badge::getAllBadges();
        $notifications = Notification::getAllPendingScavengerHunt();
        View::renderTemplate('Admin/badge.html', array('badges' => $badges, 'notifications' => $notifications));
    }

    public function addAction()
    {
        $this->throwToLoginPage();
        if (isset($_POST['add'])) {
            if(Badge::addBadge($_POST['badge-name'], $_FILES['badge-add'], isset($_POST['description'])) ? $_POST['description'] : null) {
                Flash::addMessage('Badge Added Successfully!');
            } else {
                Flash::addMessage('Badge Addition Failed!', 'warning');
            }
        }
        $this->redirect('/museum/gamify/badgecontroller/new');
    }

    public function updateAction()
    {
        $this->throwToLoginPage();
        if(isset($_POST['update'])) {
            $badge = Badge::getBadgeById($_POST['select-badge-update']);
            if($badge) {
                if($badge->updateBadge($_FILES['badge-update'])) {
                    Flash::addMessage('Badge Updated Successfully!');
                } else {
                    Flash::addMessage('Badge Update Failed!', 'warning');
                }
            } else {
                Flash::addMessage('Badge Not Found!', 'warning');
            }
        }
        $this->redirect('/museum/gamify/badgecontroller/new');
    }

    public function deleteAction()
    {
        $this->throwToLoginPage();
        if (isset($_POST['delete'])) {
            $badge = Badge::getBadgeById($_POST['select-badge-delete']);
            if($badge) {
                if($badge->deleteBadge()) {
                    Flash::addMessage('Badge Deleted Successfully!');
                } else {
                    Flash::addMessage('Badge Deletion Failed!', 'warning');
                }
            } else {
                Flash::addMessage('Badge Not Found!', 'warning');
            }
        }
        $this->redirect('/museum/gamify/badgecontroller/new');
    }

    private function throwToLoginPage()
    {
        if (isset($_SESSION['admin'])) {
            $this->redirect('/museum/gamify/admin/new');
        }
    }

}