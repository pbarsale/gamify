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
        if(!isset($_SESSION['user_id']) || $_SESSION['admin'])
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

    public static function calculatePoints($questionid,$points,$badge_id,$option) {
        
        $points_obtained = 0;
        $badge_obtained = null;

        $correctoptions = Option::getCorrectOptions($questionid);
        if(count(array_diff($option, $correctoptions[0]))==0 &&
            count(array_diff($correctoptions[0], $option))==0){
            $points_obtained = $points;
            if($badge_id!=0)
                $badge_obtained = $badge_id;
        }
        static::updateUserScore($questionid,$points_obtained,$badge_obtained);
    }

    public static function updateUserScore($questionid,$points_obtained,$badge_obtained){
        if(static::isUserQuestionPresent($questionid)){
            static::EditUserScore($questionid,$points_obtained,$badge_obtained);
        }else{
            static::AddUserScore($questionid,$points_obtained,$badge_obtained);
        }
    }

    public static function isUserQuestionPresent($questionid){

        $sql = "Select * from quiz_points where question_id=:question_id and user_id=:user_id";
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':question_id', $questionid, PDO::PARAM_INT);
        $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->execute();

        $quiz_points = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if($quiz_points)
            return true;
        else
            return false;
    }

    public static function EditUserScore($questionid,$points_obtained,$badge_obtained){

        $sql = 'UPDATE quiz_points SET points=:points, badge_id=:badge_id, 
                date_updated=:date_updated,user_updated=:user_updated 
                WHERE user_id=:user_id and question_id=:question_id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->bindValue(':question_id', $questionid , PDO::PARAM_INT);        
        $stmt->bindValue(':points', $points_obtained, PDO::PARAM_INT);
        $stmt->bindValue(':badge_id', $badge_obtained, PDO::PARAM_INT);
        $stmt->bindValue(':date_updated', date('Y-m-d H:i:s', time()), PDO::PARAM_STR);
        $stmt->bindValue(':user_updated', $_SESSION['user_id'], PDO::PARAM_INT);
        return $stmt->execute();
    }

    public static function AddUserScore($questionid,$points_obtained,$badge_obtained){
       
        $sql = "Insert into quiz_points(user_id, question_id, points, badge_id, date_created,   user_updated, date_updated)
            values(:user_id, :question_id, :points, :badge_id, :date_created, :user_updated, :date_updated)";

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->bindValue(':question_id', $questionid , PDO::PARAM_INT);        
        $stmt->bindValue(':points', $points_obtained, PDO::PARAM_INT);
        $stmt->bindValue(':badge_id', $badge_obtained, PDO::PARAM_INT);
        $stmt->bindValue(':date_created', date('Y-m-d H:i:s', time()), PDO::PARAM_STR);
        $stmt->bindValue(':date_updated', date('Y-m-d H:i:s', time()), PDO::PARAM_STR);
        $stmt->bindValue(':user_updated', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->execute();
    }
}