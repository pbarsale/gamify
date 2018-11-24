<?php
/**
 * Created by PhpStorm.
 * User: prati
 * Date: 10/24/2018
 * Time: 10:17 PM
 */

namespace App\Models;
use PDO;

class Scavenger  extends \Core\Model{

    const FILEPATH = "/museum/gamify/";
    const PENDING = 'pending';

    const COMPLETED = 'completed';

    public static function calculateScore($gameid, $questionid, $optionid, $points,
                                          $badge_id, $iscorrect, $schunt) {

        $answer_tmp = $schunt['tmp_name'];
        $answer_type = $schunt['type'];
        $filepath = "images/" . $_SESSION['user_id']."_".$gameid."_".$questionid."_".$optionid.".jpg";

        if(self::validateImage($answer_type) and
                move_uploaded_file($answer_tmp, $filepath)){

            $sql = "Insert into scavenger_hunt_points(user_id, question_id, option_id, points, badge_id, 
                    image, status, date_created,   user_updated, date_updated)
            values(:user_id, :question_id, :option_id, :points, :badge_id, 
                    :image, :status, :date_created, :user_updated, :date_updated)";

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
            $stmt->bindValue(':question_id', $questionid , PDO::PARAM_INT);
            $stmt->bindValue(':option_id', $optionid , PDO::PARAM_INT);
            $stmt->bindValue(':points', $points, PDO::PARAM_INT);
            $stmt->bindValue(':badge_id', $badge_id, PDO::PARAM_INT);

            if($iscorrect)
                $stmt->bindValue(':status', self::PENDING, PDO::PARAM_STR);
            else
                $stmt->bindValue(':status', self::COMPLETED, PDO::PARAM_STR);

            $stmt->bindValue(':image', self::FILEPATH . $filepath, PDO::PARAM_STR);

            $stmt->bindValue(':date_created', date('Y-m-d H:i:s', time()), PDO::PARAM_STR);
            $stmt->bindValue(':date_updated', date('Y-m-d H:i:s', time()), PDO::PARAM_STR);
            $stmt->bindValue(':user_updated', $_SESSION['user_id'], PDO::PARAM_INT);
            $stmt->execute();
            return "Points updated successfully";
        }
        else{
            return "Please select valid file type (JPEG, JPG, GIF, PNG)";
        }
    }

    private static function validateImage($answer_type) {
        return preg_match('/^image\\/p?jpeg$/i', $answer_type) or
            preg_match('/^image\\/gif$/i', $answer_type) or
            preg_match('/^image\\/(x-)?png$/i', $answer_type);
    }
}