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
    const QUIZ_CONST = 4;
    const SCAVENGER_HUNT_CONST = 16;

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
            $stmt->execute();

            if ($stmt->rowcount() > 0) {

                $id = self::getLatestID($db);

                if($id) {
                    if(self::insertGameInResource($db, $id, $game)) {
                        return $id;
                    } else {
                        Flash::addMessage('Game Addition failed', 'warning');
                    }
                } else {
                    Flash::addMessage('Game Not Found', 'warning');
                }
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
        return $stmt->rowcount() > 0;
    }

    private static function getLatestID($db) {
        $sql = "SELECT MAX(id) from games";

        $stmt = $db->prepare($sql);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $result = $stmt->fetch();
        if($result) {
            $id = $result['MAX(id)'];
            return $id;
        }
        return null;
    }

    public static function getGameType($game_id)
    {
        $game = self::getGameById($game_id);
        if($game) {
            return $game->game_type_id;
        }
        return null;
    }

    public function deleteGame() {
        $sql = 'UPDATE games SET isdeleted = :isdeleted, date_updated = :date_updated, user_updated = :user_updated WHERE id=:id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':isdeleted', true, PDO::PARAM_BOOL);
        $stmt->bindValue(':date_updated', date('Y-m-d H:i:s', time()),PDO::PARAM_STR);
        $stmt->bindValue(':user_updated', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->rowcount() > 0;
    }

    public function updateGame($game) {
        $sql = 'UPDATE games SET date_updated = :date_updated, user_updated = :user_updated WHERE id=:id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':date_updated', date('Y-m-d H:i:s', time()),PDO::PARAM_STR);
        $stmt->bindValue(':user_updated', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);

        $stmt->execute();
        if($stmt->rowcount() > 0) {

            $sql = 'UPDATE resource SET text=:text WHERE row_id=:row_id and column_n=:column_n and table_n=:table_n and lang=:lang';

            $stmt = $db->prepare($sql);
            $stmt->bindValue(':text', $game, PDO::PARAM_STR);
            $stmt->bindValue(':column_n', self::NAME, PDO::PARAM_STR);
            $stmt->bindValue(':table_n', self::TABLE_NAME, PDO::PARAM_STR);
            $stmt->bindValue(':row_id', $this->id, PDO::PARAM_INT);
            $stmt->bindValue(':lang', self::LANGUAGE, PDO::PARAM_STR);

            $stmt->execute();
            return $stmt->rowcount() > 0;
        }
        return false;
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

    public static function getResourceForId($id, $db = null) {

        if($db==null)
            $db = static::getDB();

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

    public static function getGameByName($name) {
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
        $stmt->bindValue(':game_type_id', $gameTypeID, PDO::PARAM_INT);
        $stmt->bindValue(':age_group_id', $ageGroupID, PDO::PARAM_INT);

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

    public static function getGamePointsAndBadges($games){

        foreach ($games as $key => $value){
            $index=0;
            foreach($value as $game){

                if($game['game_type_id']==static::QUIZ_CONST){
                    $game = static::getGamePointsQuiz($game);
                    $game = static::getGameBadgesQuiz($game);
                }
                else if($game['game_type_id']==static::SCAVENGER_HUNT_CONST){
                    $game = static::getGamePointsScavenger($game);
                    $game = static::getGameBadgesScavenger($game);
                }
                $value[$index] = $game;
                $index++;
            }
            $games[$key] = $value;
        }
        return $games;
    }

    public static function getGamePointsQuiz($game){

        try{
            $sql = "SELECT sum(points) as points FROM questions WHERE isdeleted=:isdeleted and game_id=:game_id";
            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':isdeleted', false, PDO::PARAM_BOOL);
            $stmt->bindValue(':game_id', $game['id'], PDO::PARAM_INT);

            $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
            $stmt->execute();

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if($result[0]['points'])
                $game['points'] = (int)$result[0]['points'];
            else
                $game['points'] = 0;
            return $game;
        }
        catch (Exception $msg){
            $game['points'] = 0;
            return $game;
        }
    }

    public static function getGameBadgesQuiz($game){
        try{
            $sql = "SELECT q.badge_id,b.badge
                    FROM questions q inner join badges b
                    ON q.badge_id=b.id and b.isdeleted=:isdeleted
                    WHERE q.isdeleted=:isdeleted and q.game_id=:game_id";

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':isdeleted', false, PDO::PARAM_BOOL);
            $stmt->bindValue(':game_id', $game['id'], PDO::PARAM_INT);

            $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
            $stmt->execute();

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $game['badges'] = $result;

            return $game;
        }
        catch (Exception $msg){
            $game['points'] = 0;
            return $game;
        }
    }

    public static function getGamePointsScavenger($game){

        try{
            $sql = "SELECT sum(o.points) as points 
                    FROM questions q inner join options o
                    ON q.id=o.question_id AND q.isdeleted=:isdeleted 
                    AND q.game_id=:game_id AND o.isdeleted=:isdeleted";
            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':isdeleted', false, PDO::PARAM_BOOL);
            $stmt->bindValue(':game_id', $game['id'], PDO::PARAM_INT);

            $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
            $stmt->execute();

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if($result[0]['points'])
                $game['points'] = (int)$result[0]['points'];
            else
                $game['points'] = 0;
            return $game;
        }
        catch (Exception $msg){
            $game['points'] = 0;
            return $game;
        }
    }

    public static function getGameBadgesScavenger($game){
        try{
            $sql = "SELECT badge_data.badge_id,b.badge
                    FROM 
                    (SELECT o.badge_id as badge_id
                    FROM questions q inner join options o
                    ON q.id=o.question_id AND q.isdeleted=:isdeleted 
                    AND q.game_id=:game_id AND o.isdeleted=:isdeleted) as badge_data
                    inner join badges b
                    ON badge_data.badge_id=b.id and b.isdeleted=:isdeleted";

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':isdeleted', false, PDO::PARAM_BOOL);
            $stmt->bindValue(':game_id', $game['id'], PDO::PARAM_INT);

            $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
            $stmt->execute();

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $game['badges'] = $result;

            return $game;
        }
        catch (Exception $msg){
            $game['points'] = 0;
            return $game;
        }
        return $game;
    }
}