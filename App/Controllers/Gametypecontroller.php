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
use \App\Models\GameType;

/**
 * GameType controller
 *
 * PHP version 7.0
 */
class Gametypecontroller extends \Core\Controller
{
    public function newAction()
    {
        $this->throwToLoginPage();
        $game_types = GameType::getAllGameTypes();
        $notifications = Notification::getAllPendingScavengerHunt();
        View::renderTemplate('Admin/gametype.html', array('game_types' => $game_types, 'notifications' => $notifications));
    }

    public function addAction()
    {
        $this->throwToLoginPage();
        if (isset($_POST['add'])) {
            if(GameType::addGameType($_POST['game-type-add'])) {
                Flash::addMessage('GameType Added Successfully!');
            } else {
                Flash::addMessage('GameType Addition Failed!', 'warning');
            }
        }
        $this->redirect('/museum/gamify/gametypecontroller/new');
    }

    public function deleteAction()
    {
        $this->throwToLoginPage();
        if (isset($_POST['delete'])) {
            $game_type = GameType::getGameTypeById($_POST['select-game-type-delete']);
            if($game_type) {
                if ($game_type->deleteGameType()) {
                    Flash::addMessage('GameType Deleted Successfully!');
                } else {
                    Flash::addMessage('GameType Deletion Failed!', 'warning');
                }
            } else {
                Flash::addMessage('GameType Not Found!', 'warning');
            }
        }
        $this->redirect('/museum/gamify/gametypecontroller/new');
    }

    public function updateAction()
    {
        $this->throwToLoginPage();
        if(isset($_POST['update'])) {
            $game_type = GameType::getGameTypeById($_POST['select-game-type-update']);
            if($game_type) {
                if ($game_type->updateGameType($_POST['game-type-update'])) {
                    Flash::addMessage('GameType Updated Successfully!');
                } else {
                    Flash::addMessage('GameType Update Failed!', 'warning');
                }
            } else {
                Flash::addMessage('GameType Not Found!', 'warning');
            }
        }
        $this->redirect('/museum/gamify/gametypecontroller/new');
    }

    private function throwToLoginPage()
    {
        if (!isset($_SESSION['admin'])) {
            $this->redirect('/museum/gamify/admin/new');
        }
    }

}