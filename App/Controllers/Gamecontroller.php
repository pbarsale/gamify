<?php
/**
 * Created by PhpStorm.
 * User: priyankanaik
 * Date: 16/10/2018
 * Time: 23:44
 */
namespace App\Controllers;

use App\Flash;
use App\Models\AgeGroup;
use App\Models\Badge;
use App\Models\GameType;
use App\Models\Notification;
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
        $notifications = Notification::getAllPendingScavengerHunt();
        View::renderTemplate('Admin/game.html', array('games' => $games, 'game_types' => $game_types, 'age_groups' => $age_groups, 'badges' => $badges, 'notifications' => $notifications));
    }

    public function addAction()
    {
        if (isset($_POST['add'])) {
            $id = Game::addGame($_POST['game-add'], $_POST['select-game-type'], $_POST['select-age-group']);
            if ($id != null) {
                $_SESSION['game_id'] = $id;
                $_SESSION['game_type_id'] = $_POST['select-game-type'];
                Flash::addMessage('Game Added Successfully!');
                $this->redirect('/museum/gamify/questioncontroller/new');
            } else {
                Flash::addMessage('Game Addition Failed!', 'warning');
                $this->redirect('/museum/gamify/gamecontroller/new');
            }
        }
    }

    public function updateAction()
    {
        if(isset($_POST['update'])) {
            $game = Game::getGameById($_POST['select-game-update']);
            if($game) {
                if($game->updateGame($_POST['game-update'])) {
                    Flash::addMessage('Game Updated Successfully!');
                } else {
                    Flash::addMessage('Game Update Failed!', 'warning');
                }
                $this->redirect('/museum/gamify/gamecontroller/new');
            }
        }
    }

    public function deleteAction()
    {
        if (isset($_POST['delete'])) {
            $game = Game::getGameById($_POST['select-game-delete']);
            if($game) {
                if($game->deleteGame()) {
                    Flash::addMessage('Game Deleted Successfully!');
                } else {
                    Flash::addMessage('Game Deletion Failed!', 'warning');
                }
                $this->redirect('/museum/gamify/gamecontroller/new');
            }
        }
    }

    public function editAction()
    {
        if(isset($_POST['edit'])) {
            $game = Game::getGameById($_POST['select-game-edit']);
            $_SESSION['game_id'] = $game->id;
            if($game) {
                $this->redirect('/museum/gamify/questioncontroller/edit');
            }
        }
    }

}