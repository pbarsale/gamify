<?php

namespace App\Models;

use App\Flash;
use PDO;
/**
 * Example Game model
 *
 * PHP version 7.0
 */
class Game extends \Core\Model
{
    const NAME = "name";
    const TABLE_NAME = "games";
    const LANGUAGE = "english";

    public static function addGame($game, $selected_game_type, $selected_age_group) {
        $game_obj = self::getGameByName($game);
        $game_type_obj = GameType::getGameTypeById($selected_game_type);
        $age_group_obj = AgeGroup::getAgeGroupById($selected_age_group);
        if(!$game_obj and $game_type_obj and $age_group_obj) {
            $sql = "Insert into games(date_created, user_created, date_updated, user_updated, isdeleted, game_type_id, age_group_id)
                            values(:date_created, :user_created, :date_updated, :user_updated, :isdeleted, :game_type_id, :age_group_id)";

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':date_created', date('Y-m-d H:i:s', time()), PDO::PARAM_STR);
            $stmt->bindValue(':user_created', $_SESSION['user_id'], PDO::PARAM_INT);
            $stmt->bindValue(':date_updated', date('Y-m-d H:i:s', time()), PDO::PARAM_STR);
            $stmt->bindValue(':user_updated', $_SESSION['user_id'], PDO::PARAM_INT);
            $stmt->bindValue(':isdeleted', false, PDO::PARAM_BOOL);
            $stmt->bindValue(':game_type_id', $game_type_obj->id, PDO::PARAM_INT);
            $stmt->bindValue(':age_group_id', $age_group_obj->id, PDO::PARAM_INT);
//            $stmt->bindValue(':badge_id', $selected_badge == 0 ? null : $selected_badge, PDO::PARAM_INT);

            if ($stmt->execute()) {

                $id = self::getLatestID($db);

                self::insertGameInResource($db, $id, $game);

                return $id;

            } else {
                Flash::addMessage('Query execution failed', 'warning');
            }
        } else {
            Flash::addMessage('Game Already Exists!', 'warning');
        }
        return null;
    }

    private static function insertGameInResource($db, $id, $game) {
        $sql = "Insert into resource(table_n, column_n, row_id, text, lang)
                            values(:table_n, :column_n, :row_id, :text, :lang)";

        $stmt = $db->prepare($sql);
        $stmt->bindValue(':table_n', self::TABLE_NAME, PDO::PARAM_STR);
        $stmt->bindValue(':column_n', self::NAME, PDO::PARAM_STR);
        $stmt->bindValue(':row_id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':text', $game, PDO::PARAM_STR);
        $stmt->bindValue(':lang', self::LANGUAGE, PDO::PARAM_STR);

        $stmt->execute();
    }

    private static function getLatestID($db) {
        $sql = "SELECT MAX(id) from games";

        $stmt = $db->prepare($sql);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $result = $stmt->fetch();
        $id = $result['MAX(id)'];
        return $id;
    }

    public static function getGameType($game_id)
    {
        $game = self::getGameById($game_id);
        return $game->game_type_id;
    }

    public function deleteGame() {
        $sql = 'UPDATE games SET isdeleted = :isdeleted, date_updated = :date_updated, user_updated = :user_updated WHERE id=:id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':isdeleted', true, PDO::PARAM_BOOL);
        $stmt->bindValue(':date_updated', date('Y-m-d H:i:s', time()),PDO::PARAM_STR);
        $stmt->bindValue(':user_updated', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function updateGame($game) {
        var_dump($game);
        $sql = 'UPDATE games SET date_updated = :date_updated, user_updated = :user_updated WHERE id=:id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':date_updated', date('Y-m-d H:i:s', time()),PDO::PARAM_STR);
        $stmt->bindValue(':user_updated', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);

        $stmt->execute();

        $sql = 'UPDATE resource SET text=:text WHERE row_id=:row_id and column_n=:column_n and table_n=:table_n';

        $stmt = $db->prepare($sql);
        $stmt->bindValue(':text', $game, PDO::PARAM_STR);
        $stmt->bindValue(':column_n', self::NAME, PDO::PARAM_STR);
        $stmt->bindValue(':table_n', self::TABLE_NAME, PDO::PARAM_STR);
        $stmt->bindValue(':row_id', $this->id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public static function getAllGames() {
        $sql = "SELECT * FROM games WHERE isdeleted=:isdeleted";

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':isdeleted', false, PDO::PARAM_BOOL);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if($result) {
            foreach ($result as $selected_row) {
                $resource = self::getResourceForId($selected_row['id'], $db);
                foreach ($resource as $selected_resource) {
                    $selected_row[$selected_resource['column_n']] = $selected_resource['text'];
                }
                $rows[] = array_map(null, $selected_row);
            }
            $games = $rows;
        } else {
            $games = $result;
        }
        return $games;
    }

    private static function getResourceForId($id, $db) {
        $sql = "SELECT * from resource where row_id=:row_id and table_n=:table_n";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':row_id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':table_n', self::TABLE_NAME, PDO::PARAM_STR);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getGameByName($name) {
        $sql = "SELECT * from resource where text=:text and column_n=:column_n and table_n=:table_n";
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':text', $name, PDO::PARAM_STR);
        $stmt->bindValue(':column_n', self::NAME, PDO::PARAM_STR);
        $stmt->bindValue(':table_n', self::TABLE_NAME, PDO::PARAM_STR);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        return $stmt->fetch();
    }

    public static function getGameById($id) {
        $sql = "SELECT * from games where id=:id";
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        $stmt->setFetchMode(PDO::FETCH_CLASS,get_called_class());

        $stmt->execute();
        return $stmt->fetch();
    }

    public static function getAllGamesForGameType($gameTypeID,$ageGroupID) {

        $sql = "SELECT * FROM games WHERE isdeleted=:isdeleted and game_type_id=:game_type_id and age_group_id=:age_group_id";
        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':isdeleted', false, PDO::PARAM_BOOL);
        $stmt->bindValue(':game_type_id', $gameTypeID, PDO::PARAM_STR);
        $stmt->bindValue(':age_group_id', $ageGroupID, PDO::PARAM_STR);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if($result) {
            foreach ($result as $selected_row) {
                $resource = self::getResourceForId($selected_row['id'], $db);
                foreach ($resource as $selected_resource) {
                    $selected_row[$selected_resource['column_n']] = $selected_resource['text'];
                }
                $rows[] = array_map(null, $selected_row);
            }
            $games = $rows;
        } else {
            $games = $result;
        }
        return $games;
    }

}