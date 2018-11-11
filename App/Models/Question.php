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
    const TABLE_NAME = "questions";
    const QUESTION = "question";
    const LANGUAGE = "english";
    const DESCRIPTION = "description";

    public static function addQuestion($question, $options, $points, $answer, $description, $badge)
    {
        $sql = "Insert into questions(game_id, points, badge_id, date_created, user_created, date_updated, user_updated, isdeleted)
                            values(:game_id, :points, :badge_id, :date_created, :user_created, :date_updated, :user_updated, :isdeleted)";

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':game_id', $_SESSION['game_id'], PDO::PARAM_INT);
        $stmt->bindValue(':points', $points, PDO::PARAM_INT);
        $stmt->bindValue(':badge_id', $badge, PDO::PARAM_INT);
        $stmt->bindValue(':date_created', date('Y-m-d H:i:s', time()), PDO::PARAM_STR);
        $stmt->bindValue(':user_created', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->bindValue(':date_updated', date('Y-m-d H:i:s', time()), PDO::PARAM_STR);
        $stmt->bindValue(':user_updated', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->bindValue(':isdeleted', false, PDO::PARAM_BOOL);

        if($stmt->execute()) {

            $id = self::getLatestQuestionID($db);
            self::addQuestionResource($db, $id, $question);
            self::addDescriptionResource($db, $id, $description);
            Option::addOptions($db, $id, $options);
            Option::updateAnswer($db, $id, $options, $answer);
        }
    }

    private static function getLatestQuestionID($db) {
        $sql = "SELECT MAX(id) from questions";

        $stmt = $db->prepare($sql);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $result = $stmt->fetch();
        $id = $result['MAX(id)'];
        return $id;
    }

    private static function addQuestionResource($db, $id, $question) {
        $sql = "Insert into resource(table_n, column_n, row_id, text, lang)
                            values(:table_n, :column_n, :row_id, :text, :lang)";

        $stmt = $db->prepare($sql);
        $stmt->bindValue(':table_n', self::TABLE_NAME, PDO::PARAM_STR);
        $stmt->bindValue(':column_n', self::QUESTION, PDO::PARAM_STR);
        $stmt->bindValue(':row_id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':text', $question, PDO::PARAM_STR);
        $stmt->bindValue(':lang', self::LANGUAGE, PDO::PARAM_STR);

        $stmt->execute();
    }

    private static function addDescriptionResource($db, $id, $description) {
        $sql = "Insert into resource(table_n, column_n, row_id, text, lang)
                            values(:table_n, :column_n, :row_id, :text, :lang)";

        $stmt = $db->prepare($sql);
        $stmt->bindValue(':table_n', self::TABLE_NAME, PDO::PARAM_STR);
        $stmt->bindValue(':column_n', self::DESCRIPTION, PDO::PARAM_STR);
        $stmt->bindValue(':row_id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':text', $description, PDO::PARAM_STR);
        $stmt->bindValue(':lang', self::LANGUAGE, PDO::PARAM_STR);

        $stmt->execute();
    }

    public static function getAllQuestions() {
        $sql = "SELECT * FROM questions WHERE isdeleted=:isdeleted";

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
                $options = Option::getAllOptions($selected_row['id']);
                $selected_row['options'] = $options;
                $rows[] = array_map(null, $selected_row);
            }
            $questions = $rows;
        } else {
            $questions = $result;
        }
        return $questions;
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

    public function deleteQuestion() {
        $sql = 'UPDATE questions SET isdeleted = :isdeleted, date_updated = :date_updated, user_updated = :user_updated WHERE id=:id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':isdeleted', true, PDO::PARAM_BOOL);
        $stmt->bindValue(':date_updated', date('Y-m-d H:i:s', time()),PDO::PARAM_STR);
        $stmt->bindValue(':user_updated', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public static function getQuestionById($id) {
        $sql = "SELECT * from questions where id=:id";
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        $stmt->setFetchMode(PDO::FETCH_CLASS,get_called_class());

        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if($result) {
            $resource = self::getResourceForId($id, $db);
            foreach ($resource as $selected_resource) {
                $result[$selected_resource['column_n']] = $selected_resource['text'];
            }
            $options = Option::getAllOptions($id);
            $result['options'] = $options;
            $rows[] = array_map(null, $result);
            $question = $rows;
        } else {
            $question = $result;
        }
        return $question;
    }

    public static function updateQuestion($id, $question, $options, $description)
    {
        $sql = "UPDATE questions SET date_updated=:date_updated, user_updated=:user_updated WHERE id=:id";

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':date_updated', date('Y-m-d H:i:s', time()), PDO::PARAM_STR);
        $stmt->bindValue(':user_updated', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        if($stmt->execute()) {

            self::updateQuestionResource($db, $id, $question);
            if($description) {
                self::updateDescriptionResource($db, $id, $description);
            }

            Option::updateOptions($db, $id, $options);
        }
    }

    private static function updateQuestionResource($db, $id, $question) {
        $sql = "UPDATE resource SET text=:text WHERE row_id=:row_id and table_n=:table_n and column_n=:column_n";

        $stmt = $db->prepare($sql);
        $stmt->bindValue(':table_n', self::TABLE_NAME, PDO::PARAM_STR);
        $stmt->bindValue(':column_n', self::QUESTION, PDO::PARAM_STR);
        $stmt->bindValue(':row_id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':text', $question, PDO::PARAM_STR);

        $stmt->execute();
    }

    private static function updateDescriptionResource($db, $id, $description) {
        $sql = "UPDATE resource SET text=:text WHERE row_id=:row_id and table_n=:table_n and column_n=:column_n";

        $stmt = $db->prepare($sql);
        $stmt->bindValue(':table_n', self::TABLE_NAME, PDO::PARAM_STR);
        $stmt->bindValue(':column_n', self::DESCRIPTION, PDO::PARAM_STR);
        $stmt->bindValue(':row_id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':text', $description, PDO::PARAM_STR);

        $stmt->execute();
    }

    public static function getAllQuestionsByGameId($game_id) {

        $sql = "SELECT * FROM questions WHERE isdeleted=:isdeleted and game_id=:game_id";
        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':isdeleted', false, PDO::PARAM_BOOL);
        $stmt->bindValue(':game_id', $game_id, PDO::PARAM_INT);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if($result) {
            foreach ($result as $selected_row) {
                $resource = self::getResourceForId($selected_row['id'], $db);
                foreach ($resource as $selected_resource) {
                    $selected_row[$selected_resource['column_n']] = $selected_resource['text'];
                }
                $options = Option::getAllOptions($selected_row['id']);
                $selected_row['options'] = $options;
                $rows[] = array_map(null, $selected_row);
            }
            $questions = $rows;
        } else {
            $questions = $result;
        }
        return $questions;
    }

    public static function getUserScoreQuiz($questionid){

        $sql = "SELECT points,badge_id FROM quiz_points WHERE question_id=:question_id 
                          and user_id=:user_id";
        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':question_id', $questionid, PDO::PARAM_INT);
        $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $stmt->execute();
        $result = $stmt->fetch();

        if($result){
            $data['answered'] = true;
            $data['userpoints'] = $result->points;
            $data['userbadge'] = $result->badge_id;
        }else{
            $data['answered'] = false;
            $data['userpoints'] = 0;
            $data['userbadge'] = null;
        }
        return $data;
    }
}