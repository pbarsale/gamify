<?php
/**
 * Created by PhpStorm.
 * User: prati
 * Date: 10/24/2018
 * Time: 10:17 PM
 */

namespace App\Models;
use PDO;

class Quiz extends \Core\Model
{
    public static function getAllGames() {

        if(!isset($_SESSION['user_id']))
            return;

        $user = User::findById($_SESSION['user_id']);
        $user->getAgeFromBirthDate();
        $agegroup = AgeGroup::getAgeGroupIdByAge($user->age);
        $gametypes = GameType::getAllGameTypes();

        if($gametypes){
            foreach ($gametypes as $selected_row) {
                $games[$selected_row['name']] = Game::getAllGamesForGameType($selected_row['id'],$agegroup->id);
            }
        }
        return $games;
    }
}