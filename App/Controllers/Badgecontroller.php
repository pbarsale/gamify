<?php
/**
 * Created by PhpStorm.
 * User: priyankanaik
 * Date: 16/10/2018
 * Time: 23:44
 */
namespace App\Controllers;

use App\Flash;
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
        $badges = Badge::getAllBadges();
        View::renderTemplate('Admin/badge.html', array('badges' => $badges));
    }

    public function addAction()
    {
        if (isset($_POST['add'])) {
            if(Badge::addBadge($_POST['badge-name'], $_FILES['badge-add'],$_POST['description'])) {
                Flash::addMessage('Badge Added Successfully!');
            } else {
                Flash::addMessage('Badge Addition Failed!', 'warning');
            }
            $this->redirect('/museum/gamify/badgecontroller/new');
        }
    }

    public function updateAction()
    {
        if(isset($_POST['update'])) {
            $badge = Badge::getBadgeById($_POST['select-badge-update']);
            if($badge) {
                if($badge->updateBadge($_FILES['badge-update'])) {
                    Flash::addMessage('Badge Updated Successfully!');
                } else {
                    Flash::addMessage('Badge Update Failed!', 'warning');
                }
                $this->redirect('/museum/gamify/badgecontroller/new');
            }
        }
    }

    public function deleteAction()
    {
        if (isset($_POST['delete'])) {
            $badge = Badge::getBadgeById($_POST['select-badge-delete']);
            if($badge) {
                if($badge->deleteBadge()) {
                    Flash::addMessage('Badge Deleted Successfully!');
                } else {
                    Flash::addMessage('Badge Deletion Failed!', 'warning');
                }
            }
            $this->redirect('/museum/gamify/badgecontroller/new');
        }
    }

}