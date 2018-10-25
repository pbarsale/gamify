<?php
/**
 * Created by PhpStorm.
 * User: priyankanaik
 * Date: 16/10/2018
 * Time: 23:44
 */
namespace App\Controllers;

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

    public function modifyAction()
    {
        if (isset($_POST['add'])) {
            Badge::addBadge($_POST['badge-name'], $_FILES['badge'],$_POST['description']);
        } elseif (isset($_POST['delete']) or isset($_POST['update'])) {
            $badge = Badge::getBadgeById($_POST['select-badge']);
            if($badge) {
                if(isset($_POST['delete'])) {
                    $badge->deleteBadge();
                } elseif(isset($_POST['update'])) {
                    $badge->updateBadge($_FILES['badge']);
                }
            }
        }
    }

}