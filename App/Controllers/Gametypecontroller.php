<?php
/**
 * Created by PhpStorm.
 * User: priyankanaik
 * Date: 16/10/2018
 * Time: 23:44
 */
namespace App\Controllers;

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

    public function modifyAction()
    {
        if (isset($_POST['add'])) {
            GameType::addGameType($_POST['game-type']);
        } elseif (isset($_POST['delete'])) {
            $game_type = GameType::getGameTypeById($_POST['select-game-type-delete']);
            if($game_type) {
                $game_type->deleteGameType();
            }
        } elseif(isset($_POST['update'])) {
            $game_type = GameType::getGameTypeById($_POST['select-game-type-update']);
            if($game_type) {
                $game_type->updateGameType($_POST['game-type']);
            }
        }
    }

}