<?php

namespace App\Models;

use PDO;
/**
 * Example GameType model
 *
 * PHP version 7.0
 */
class AgeGroup extends \Core\Model
{
    public function addAgeGroup($min, $max) {
        $sql = "INSERT INTO age_groups(min, max, date_created, user_created, date_updated, user_updated, isdeleted) 
                        values(:min, :max, :date_created, :user_created, :date_updated, :user_updated, :isdeleted)";

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':min', 10, PDO::PARAM_INT);
        $stmt->bindValue(':max', 15, PDO::PARAM_INT);
        $stmt->bindValue(':date_created', date('Y-m-d H:i:s', time()), PDO::PARAM_STR);
        $stmt->bindValue(':user_created', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->bindValue(':date_updated', date('Y-m-d H:i:s', time()), PDO::PARAM_STR);
        $stmt->bindValue(':user_updated', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->bindValue(':isdeleted', false, PDO::PARAM_BOOL);

        return $stmt->execute();
    }

    public static function getAllAgeGroups() {
        $sql = "SELECT * FROM age_groups where isdeleted=:isdeleted";
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':isdeleted', false, PDO::PARAM_BOOL);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function getAgeGroupById($id) {
        $sql = "SELECT * from age_groups where id=:id";
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();
        return $stmt->fetch();
    }

    public static function getAgeGroupIdByAge($age) {

        $sql = "SELECT * from age_groups where isdeleted=:isdeleted and $age between min and max";
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':isdeleted', false, PDO::PARAM_BOOL);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $stmt->execute();
        return $stmt->fetch();
    }
}