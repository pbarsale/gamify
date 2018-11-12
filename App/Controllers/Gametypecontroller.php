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
        $game_types = GameType::getAllGameTypes();
        View::renderTemplate('Admin/gametype.html', array('game_types' => $game_types));
    }

    public function addAction()
    {
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
        if (isset($_POST['delete'])) {
            $game_type = GameType::getGameTypeById($_POST['select-game-type-delete']);
            if($game_type) {
                $game_type->deleteGameType();
                Flash::addMessage('GameType Deleted Successfully!');
            } else {
                Flash::addMessage('GameType Deletion Failed!', 'warning');
            }
        }
        $this->redirect('/museum/gamify/gametypecontroller/new');
    }

    public function updateAction()
    {
        if(isset($_POST['update'])) {
            $game_type = GameType::getGameTypeById($_POST['select-game-type-update']);
            if($game_type) {
                $game_type->updateGameType($_POST['game-type-update']);
                Flash::addMessage('GameType Updated Successfully!', 'warning');
            } else {
                Flash::addMessage('GameType Update Failed!');
            }
        }
        $this->redirect('/museum/gamify/gametypecontroller/new');
    }

}