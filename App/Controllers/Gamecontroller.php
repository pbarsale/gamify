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
        $this->throwToLoginPage();
        $games = Game::getAllGames();
        $game_types = GameType::getAllGameTypes();
        $age_groups = AgeGroup::getAllAgeGroups();
        $badges = Badge::getAllBadges();
        $pendingGames = Game::getAllPendingGames();
        $notifications = Notification::getAllPendingScavengerHunt();
        View::renderTemplate('Admin/game.html', array('games' => $games, 'game_types' => $game_types, 'age_groups' => $age_groups, 'badges' => $badges, 'notifications' => $notifications, 'pendingGames' => $pendingGames));
    }

    public function pendingAction()
    {
        $this->throwToLoginPage();
        $pendingGames = Game::getAllPendingGames();
        $notifications = Notification::getAllPendingScavengerHunt();
        View::renderTemplate('Admin/pendinggame.html', array('notifications' => $notifications, 'pendingGames' => $pendingGames));
    }

    public function approveAction()
    {
        $this->throwToLoginPage();
        if(isset($_GET['game'])) {
            if (Game::approvePendingGame($_GET['game'])) {
                Flash::addMessage('Game Published!');
            } else {
                Flash::addMessage('Game could not be Published!', 'warning');
            }
        }
        $this->redirect('/museum/gamify/gamecontroller/pending');
    }


    public function addAction()
    {
        $this->throwToLoginPage();
        if (isset($_POST['add'])) {
            $id = Game::addGame($_POST['game-add'], $_POST['select-game-type'], $_POST['select-age-group']);
            if ($id) {
                $_SESSION['game_id'] = $id;
                $_SESSION['game_type_id'] = $_POST['select-game-type'];
                Flash::addMessage('Game Added Successfully!');
                $this->redirect('/museum/gamify/questioncontroller/new');
            }
        }
        $this->redirect('/museum/gamify/gamecontroller/new');
    }

    public function updateAction()
    {
        $this->throwToLoginPage();
        if(isset($_POST['update'])) {
            $game = Game::getGameById($_POST['select-game-update']);
            if($game) {
                if($game->updateGame($_POST['game-update'])) {
                    Flash::addMessage('Game Updated Successfully!');
                } else {
                    Flash::addMessage('Game Update Failed!', 'warning');
                }
            } else {
                Flash::addMessage('Game Not Found!', 'warning');
            }
        }
        $this->redirect('/museum/gamify/gamecontroller/new');
    }

    public function deleteAction()
    {
        $this->throwToLoginPage();
        if (isset($_POST['delete'])) {
            $game = Game::getGameById($_POST['select-game-delete']);
            if($game) {
                if($game->deleteGame()) {
                    Flash::addMessage('Game Deleted Successfully!');
                } else {
                    Flash::addMessage('Game Deletion Failed!', 'warning');
                }
            } else {
                Flash::addMessage('Game Not Found!', 'warning');
            }
        }
        $this->redirect('/museum/gamify/gamecontroller/new');
    }

    public function editAction()
    {
        $this->throwToLoginPage();
        if(isset($_POST['edit'])) {
            $game = Game::getGameById($_POST['select-game-edit']);
            if($game) {
                $_SESSION['game_id'] = $game->id;
                $this->redirect('/museum/gamify/questioncontroller/edit');
            } else {
                Flash::addMessage('Game Not Found!', 'warning');
            }
        }
        $this->redirect('/museum/gamify/gamecontroller/new');
    }

    private function throwToLoginPage()
    {
        if (!isset($_SESSION['admin'])) {
            $this->redirect('/museum/gamify/admin/new');
        }
    }

}