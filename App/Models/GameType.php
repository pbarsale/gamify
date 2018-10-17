<?php

namespace App\Models;

use PDO;
use \Core\View;

/**
 * Example GameType model
 *
 * PHP version 7.0
 */
class GameType extends \Core\Model
{
    public static function addGameType($game_type) {
        $game_type_obj = self::getGameType($game_type);
        if(!$game_type_obj) {
            $sql = "Insert into game_type(name, date_created, user_created, date_updated, user_updated, isdeleted)
                            values(:name, :date_created, :user_created, :date_updated, :user_updated, :isdeleted)";

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':name', $game_type, PDO::PARAM_STR);
            $stmt->bindValue(':date_created', date('Y-m-d H:i:s', time()), PDO::PARAM_STR);
            $stmt->bindValue(':user_created', 16, PDO::PARAM_INT);
            $stmt->bindValue(':date_updated', date('Y-m-d H:i:s', time()), PDO::PARAM_STR);
            $stmt->bindValue(':user_updated', 16, PDO::PARAM_INT);
            $stmt->bindValue(':isdeleted', false, PDO::PARAM_BOOL);
            return $stmt->execute();
        } else {

        }
    }

    public function deleteGameType() {
        $sql = 'UPDATE game_type SET isdeleted = :isdeleted, date_updated = :date_updated, user_updated = :user_updated WHERE id=:id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':isdeleted', true, PDO::PARAM_BOOL);
        $stmt->bindValue(':date_updated', date('Y-m-d H:i:s', time()),PDO::PARAM_STR);
        $stmt->bindValue(':user_updated', 16, PDO::PARAM_INT);
        $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function updateGameType($game_type) {
        $sql = 'UPDATE game_type SET name = :name, date_updated = :date_updated, user_updated = :user_updated WHERE id=:id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':name', $game_type, PDO::PARAM_STR);
        $stmt->bindValue(':date_updated', date('Y-m-d H:i:s', time()),PDO::PARAM_STR);
        $stmt->bindValue(':user_updated', 16, PDO::PARAM_INT);
        $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);

        return $stmt->execute();

    }

    public static function getAllGameType() {
        $sql = "SELECT * FROM game_type WHERE isdeleted=:isdeleted";

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':isdeleted', false, PDO::PARAM_BOOL);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        return $stmt->fetchAll();
    }

    public static function getGameType($id) {
        $sql = "SELECT * from game_type where id=:id";
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_STR);

        $stmt->setFetchMode(PDO::FETCH_CLASS,get_called_class());

        $stmt->execute();
        return $stmt->fetch();
    }

}