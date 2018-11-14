<?php

namespace App\Models;

use App\Flash;
use PDO;
/**
 * Example GameType model
 *
 * PHP version 7.0
 */
class GameType extends \Core\Model
{
    const NAME = "name";
    const TABLE_NAME = "game_types";
    const LANGUAGE = "english";

    public static function addGameType($game_type)
    {
        $game_type_obj = self::getGameTypeByName($game_type);
        if (!$game_type_obj) {

            $sql = "Insert into game_types(date_created, user_created, date_updated, user_updated, isdeleted)
                            values(:date_created, :user_created, :date_updated, :user_updated, :isdeleted)";

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':date_created', date('Y-m-d H:i:s', time()), PDO::PARAM_STR);
            $stmt->bindValue(':user_created', $_SESSION['user_id'], PDO::PARAM_INT);
            $stmt->bindValue(':date_updated', date('Y-m-d H:i:s', time()), PDO::PARAM_STR);
            $stmt->bindValue(':user_updated', $_SESSION['user_id'], PDO::PARAM_INT);
            $stmt->bindValue(':isdeleted', false, PDO::PARAM_BOOL);

            if ($stmt->execute()) {

                $id = self::getLatestID($db);

                return self::insertGameTypeInResource($db, $id, $game_type);

            } else {
                Flash::addMessage('Query execution failed', 'warning');
            }
        } else {
            Flash::addMessage('GameType Already Exists!', 'warning');
        }
        return false;
    }

    private static function insertGameTypeInResource($db, $id, $game_type) {
        $sql = "Insert into resource(table_n, column_n, row_id, text, lang)
                            values(:table_n, :column_n, :row_id, :text, :lang)";

        $stmt = $db->prepare($sql);
        $stmt->bindValue(':table_n', self::TABLE_NAME, PDO::PARAM_STR);
        $stmt->bindValue(':column_n', self::NAME, PDO::PARAM_STR);
        $stmt->bindValue(':row_id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':text', $game_type, PDO::PARAM_STR);
        $stmt->bindValue(':lang', self::LANGUAGE, PDO::PARAM_STR);
        return $stmt->execute();
    }

    private static function getLatestID($db) {
        $sql = "SELECT MAX(id) from game_types";

        $stmt = $db->prepare($sql);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $result = $stmt->fetch();
        $id = $result['MAX(id)'];
        return $id;
    }

    public function deleteGameType()
    {
        $sql = 'UPDATE game_types SET isdeleted = :isdeleted, date_updated = :date_updated, user_updated = :user_updated WHERE id=:id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':isdeleted', true, PDO::PARAM_BOOL);
        $stmt->bindValue(':date_updated', date('Y-m-d H:i:s', time()), PDO::PARAM_STR);
        $stmt->bindValue(':user_updated', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function updateGameType($game_type)
    {
        $sql = 'UPDATE game_types SET date_updated = :date_updated, user_updated = :user_updated WHERE id=:id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':date_updated', date('Y-m-d H:i:s', time()), PDO::PARAM_STR);
        $stmt->bindValue(':user_updated', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);

        $stmt->execute();

        $sql = 'UPDATE resource SET text=:text WHERE row_id=:row_id and table_n=:table_n and column_n=:column_n';

        $stmt = $db->prepare($sql);
        $stmt->bindValue(':text', $game_type, PDO::PARAM_STR);
        $stmt->bindValue(':row_id', $this->id, PDO::PARAM_INT);
        $stmt->bindValue(':table_n', self::TABLE_NAME, PDO::PARAM_STR);
        $stmt->bindValue(':column_n', self::NAME, PDO::PARAM_STR);

        return $stmt->execute();
    }

    public static function getAllGameTypes()
    {
        $sql = "SELECT * FROM game_types WHERE isdeleted=:isdeleted";
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
            $game_types = $rows;
        } else {
            $game_types = $result;
        }
        return $game_types;
    }

    private static function getResourceForId($id, $db) {
        $sql = "SELECT * from resource where row_id=:row_id and table_n=:table_n and column_n=:column_n and lang=:lang";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':row_id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':table_n', self::TABLE_NAME, PDO::PARAM_STR);
        $stmt->bindValue(':column_n', self::NAME, PDO::PARAM_STR);
        $stmt->bindValue(':lang', self::LANGUAGE, PDO::PARAM_STR);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getGameTypeByName($name)
    {
        $sql = "SELECT * from resource where text=:text and column_n=:column_n and table_n=:table_n and lang=:lang";
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':text', $name, PDO::PARAM_STR);
        $stmt->bindValue(':column_n', self::NAME, PDO::PARAM_STR);
        $stmt->bindValue(':table_n', self::TABLE_NAME, PDO::PARAM_STR);
        $stmt->bindValue(':lang', self::LANGUAGE, PDO::PARAM_STR);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        return $stmt->fetch();
    }

    public static function getGameTypeById($id)
    {
        $sql = "SELECT * from game_types where id=:id";
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();
        return $stmt->fetch();
    }

}