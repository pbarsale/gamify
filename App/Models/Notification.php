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

    public static function getAllPendingScavengerHunt()
    {
        $sql = "SELECT * FROM scavenger_hunt_points WHERE status=:status";

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':status', "pending", PDO::PARAM_STR);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if($result) {
            foreach ($result as $selected_row) {
                $resource = Option::getOptionById($selected_row['id']);
                foreach ($resource as $selected_resource) {
                    $selected_row[$selected_resource['column_n']] = $selected_resource['text'];
                }
                $rows[] = array_map(null, $selected_row);
            }
            $notifications = $rows;
        } else {
            $notifications = $result;
        }
        return $notifications;
    }

    public static function approvePendingRequest($question, $option)
    {
        $sql = "UPDATE scavenger_hunt_points SET status=:status WHERE question_id=:question_id and option_id=:option_id";

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':question_id', $question, PDO::PARAM_INT);
        $stmt->bindValue(':option_id', $option, PDO::PARAM_INT);
        $stmt->bindValue(':status', "completed", PDO::PARAM_STR);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        return $stmt->execute();
    }
}