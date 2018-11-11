<?php
/**
 * Created by PhpStorm.
 * User: priyankanaik
 * Date: 16/10/2018
 * Time: 23:44
 */
namespace App\Controllers;

use App\Models\AgeGroup;
use App\Models\Badge;
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
        $badges = Badge::getAllBadges();
        View::renderTemplate('Admin/game.html', array('games' => $games, 'game_types' => $game_types, 'age_groups' => $age_groups, 'badges' => $badges));
    }

    public function modifyAction()
    {
        if (isset($_POST['add'])) {
            $id = Game::addGame($_POST['game'], $_POST['select-game-type'], $_POST['select-age-group'], $_POST['select-badge']);
            if ($id) {
                $_SESSION['game_id'] = $id;
                $_SESSION['game_type_id'] = $_POST['select-game-type'];
                $this->redirect('/museum/gamify/questioncontroller/new');
            }
        } elseif (isset($_POST['delete'])) {
            $game = Game::getGameById($_POST['select-game-delete']);
            if($game) {
                $game->deleteGame();
            }
        } elseif(isset($_POST['update'])) {
            $game = Game::getGameById($_POST['select-game-update']);
            if($game) {
                $game->updateGame($_POST['game']);
            }
        } elseif(isset($_POST['edit'])) {
            $game = Game::getGameById($_POST['select-game-edit']);
            $_SESSION['game_id'] = $game->id;
            if($game) {
                $this->redirect('/museum/gamify/questioncontroller/edit');
            }
        }
    }

}