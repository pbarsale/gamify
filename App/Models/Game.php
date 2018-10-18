<?php

namespace App\Models;

use PDO;
use \Core\View;

/**
 * Example Game model
 *
 * PHP version 7.0
 */
class Game extends \Core\Model
{
    public static function addGame($game, $selected_game_type) {
        $game_obj = self::getGameByName($game);
        $game_type_obj = GameType::getGameTypeById($selected_game_type);
        if(!$game_obj && $game_type_obj) {
            $sql = "Insert into game(name, date_created, user_created, date_updated, user_updated, isdeleted, game_type_id, age_group)
                            values(:name, :date_created, :user_created, :date_updated, :user_updated, :isdeleted, :game_type_id, :age_group)";

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':name', $game, PDO::PARAM_STR);
            $stmt->bindValue(':date_created', date('Y-m-d H:i:s', time()), PDO::PARAM_STR);
            $stmt->bindValue(':user_created', 16, PDO::PARAM_INT);
            $stmt->bindValue(':date_updated', date('Y-m-d H:i:s', time()), PDO::PARAM_STR);
            $stmt->bindValue(':user_updated', 16, PDO::PARAM_INT);
            $stmt->bindValue(':isdeleted', false, PDO::PARAM_BOOL);
            $stmt->bindValue(':game_type_id', $game_type_obj->id, PDO::PARAM_INT);
            $stmt->bindValue(':age_group', '0-5', PDO::PARAM_STR);
            return $stmt->execute();
        } else {
            var_dump("Already exists!");
        }
    }

    public function deleteGame() {
        $sql = 'UPDATE game SET isdeleted = :isdeleted, date_updated = :date_updated, user_updated = :user_updated WHERE id=:id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':isdeleted', true, PDO::PARAM_BOOL);
        $stmt->bindValue(':date_updated', date('Y-m-d H:i:s', time()),PDO::PARAM_STR);
        $stmt->bindValue(':user_updated', 16, PDO::PARAM_INT);
        $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function updateGame($game) {
        $sql = 'UPDATE game SET name = :name, date_updated = :date_updated, user_updated = :user_updated WHERE id=:id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':name', $game, PDO::PARAM_STR);
        $stmt->bindValue(':date_updated', date('Y-m-d H:i:s', time()),PDO::PARAM_STR);
        $stmt->bindValue(':user_updated', 16, PDO::PARAM_INT);
        $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);

        return $stmt->execute();

    }

    public static function getAllGames() {
        $sql = "SELECT * FROM game WHERE isdeleted=:isdeleted";

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':isdeleted', false, PDO::PARAM_BOOL);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        return $stmt->fetchAll();
    }

    public static function getGameByName($name) {
        $sql = "SELECT * from game where name=:name";
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);

        $stmt->setFetchMode(PDO::FETCH_CLASS,get_called_class());

        $stmt->execute();
        return $stmt->fetch();
    }

    public static function getGameById($id) {
        $sql = "SELECT * from game where id=:id";
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        $stmt->setFetchMode(PDO::FETCH_CLASS,get_called_class());

        $stmt->execute();
        return $stmt->fetch();
    }

}