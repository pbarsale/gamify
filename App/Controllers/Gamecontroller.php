<?php
/**
 * Created by PhpStorm.
 * User: priyankanaik
 * Date: 16/10/2018
 * Time: 23:44
 */
namespace App\Controllers;

use App\Models\AgeGroup;
use App\Models\GameType;
use \Core\View;
use \App\Models\Game;

/**
 * GameType controller
 *
 * PHP version 7.0
 */
class Gamecontroller extends \Core\Controller
{
    public function newAction()
    {
        $games = Game::getAllGames();
        $game_types = GameType::getAllGameTypes();
        $age_groups = AgeGroup::getAllAgeGroups();
        View::renderTemplate('Admin/game.html', array('games' => $games, 'game_types' => $game_types, 'age_groups' => $age_groups));
    }

    public function modifyAction()
    {
        if (isset($_POST['add'])) {
            $id = Game::addGame($_POST['game'], $_POST['select-game-type'], $_POST['select-age-group']);
            if($id) {
                $_SESSION['game_id'] = $id;
                $this->redirect('/museum/gamify/questioncontroller/new');
            }
        } elseif (isset($_POST['delete']) or isset($_POST['update'])) {
            $game = Game::getGameById($_POST['select-game']);
            if($game) {
                if(isset($_POST['delete'])) {
                    $game->deleteGame();
                } elseif(isset($_POST['update'])) {
                    $game->updateGame($_POST['game']);
                }
            }
        }
    }

}