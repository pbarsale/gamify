<?php

namespace App\Models;

use PDO;

/**
 * Example GameType model
 *
 * PHP version 7.0
 */
class Question extends \Core\Model
{
    public static function addQuestion($question, $options, $points, $answer, $description)
    {
        $sql = "Insert into question(question, options, answer, description, points, date_created, user_created, date_updated, user_updated, isdeleted)
                            values(:question, :options, :answer, :description, :points, :date_created, :user_created, :date_updated, :user_updated, :isdeleted)";

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':question', $question, PDO::PARAM_STR);
        $stmt->bindValue(':options', $options, PDO::PARAM_STR);
        $stmt->bindValue(':answer', $answer, PDO::PARAM_STR);
        $stmt->bindValue(':description', $description, PDO::PARAM_STR);
        $stmt->bindValue(':points', $points, PDO::PARAM_STR);
        $stmt->bindValue(':date_created', date('Y-m-d H:i:s', time()), PDO::PARAM_STR);
        $stmt->bindValue(':user_created', 16, PDO::PARAM_INT);
        $stmt->bindValue(':date_updated', date('Y-m-d H:i:s', time()), PDO::PARAM_STR);
        $stmt->bindValue(':user_updated', 16, PDO::PARAM_INT);
        $stmt->bindValue(':isdeleted', false, PDO::PARAM_BOOL);
        return $stmt->execute();
    }

}