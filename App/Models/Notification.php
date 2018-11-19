<?php

namespace App\Models;

use PDO;
/**
 * Example GameType model
 *
 * PHP version 7.0
 */
class Notification extends \Core\Model
{
    const PENDING = "pending";
    const COMPLETED = "completed";
    const DENIED = "denied";

    public static function getAllPendingScavengerHunt()
    {
        $sql = "SELECT * FROM scavenger_hunt_points WHERE status=:status";

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':status', self::PENDING, PDO::PARAM_STR);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if($result) {
            foreach ($result as $selected_row) {
                $resource = Option::getOptionById($selected_row['option_id']);
                foreach ($resource as $selected_resource) {
                    $selected_row['option'] = $selected_resource['option'];
                    $selected_row['points'] = $selected_resource['points'];
                    $selected_row['badge_id'] = $selected_resource['badge_id'];
                }
                $rows[] = array_map(null, $selected_row);
            }
            $notifications = $rows;
        } else {
            $notifications = $result;
        }
        return $notifications;
    }

    public static function approvePendingRequest($question, $option, $user_id)
    {
        $sql = "UPDATE scavenger_hunt_points SET status=:status WHERE question_id=:question_id and option_id=:option_id and user_id=:user_id";

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':question_id', $question, PDO::PARAM_INT);
        $stmt->bindValue(':option_id', $option, PDO::PARAM_INT);
        $stmt->bindValue(':status', self::COMPLETED, PDO::PARAM_STR);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        return $stmt->execute();
    }

    public static function denyPendingRequest($question, $option, $user_id)
    {
        $sql = "UPDATE scavenger_hunt_points SET status=:status WHERE question_id=:question_id and option_id=:option_id and user_id=:user_id";

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':question_id', $question, PDO::PARAM_INT);
        $stmt->bindValue(':option_id', $option, PDO::PARAM_INT);
        $stmt->bindValue(':status', self::DENIED, PDO::PARAM_STR);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        return $stmt->execute();
    }
}