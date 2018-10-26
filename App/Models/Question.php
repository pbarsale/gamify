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
    const QUESTION_TABLE_NAME = "questions";
    const OPTION_TABLE_NAME = "options";
    const OPTION = "option";
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

            self::addOptions($db, $id, $options);
            self::updateAnswer($db, $id, $options, $answer);

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

    private static function getLatestOptionID($db) {
        $sql = "SELECT MAX(id) from options";

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
        $stmt->bindValue(':table_n', self::QUESTION_TABLE_NAME, PDO::PARAM_STR);
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
        $stmt->bindValue(':table_n', self::QUESTION_TABLE_NAME, PDO::PARAM_STR);
        $stmt->bindValue(':column_n', self::DESCRIPTION, PDO::PARAM_STR);
        $stmt->bindValue(':row_id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':text', $description, PDO::PARAM_STR);
        $stmt->bindValue(':lang', self::LANGUAGE, PDO::PARAM_STR);

        $stmt->execute();
    }

    private static function addOptions($db, $id, $options) {
        foreach($options as $key => $value) {
            $sql = "Insert into options(question_id, iscorrect, date_created, user_created, date_updated, user_updated, isdeleted)
                            values(:question_id, :iscorrect, :date_created, :user_created, :date_updated, :user_updated, :isdeleted)";

            $stmt = $db->prepare($sql);
            $stmt->bindValue(':question_id', $id, PDO::PARAM_INT);
            $stmt->bindValue(':iscorrect', false, PDO::PARAM_BOOL);
            $stmt->bindValue(':date_created', date('Y-m-d H:i:s', time()), PDO::PARAM_STR);
            $stmt->bindValue(':user_created', $_SESSION['user_id'], PDO::PARAM_INT);
            $stmt->bindValue(':date_updated', date('Y-m-d H:i:s', time()), PDO::PARAM_STR);
            $stmt->bindValue(':user_updated', $_SESSION['user_id'], PDO::PARAM_INT);
            $stmt->bindValue(':isdeleted', false, PDO::PARAM_BOOL);

            $stmt->execute();

            $option_id = self::getLatestOptionID($db);
            self::addOption($db, $option_id, $value);
        }
    }

    private static function addOption($db, $option_id, $option) {
        $sql = "Insert into resource(table_n, column_n, row_id, text, lang)
                            values(:table_n, :column_n, :row_id, :text, :lang)";

        $stmt = $db->prepare($sql);
        $stmt->bindValue(':table_n', self::OPTION_TABLE_NAME, PDO::PARAM_STR);
        $stmt->bindValue(':column_n', self::OPTION, PDO::PARAM_STR);
        $stmt->bindValue(':row_id', $option_id, PDO::PARAM_INT);
        $stmt->bindValue(':text', $option, PDO::PARAM_STR);
        $stmt->bindValue(':lang', self::LANGUAGE, PDO::PARAM_STR);

        $stmt->execute();
    }

    private static function updateAnswer($db, $id, $options, $answer) {
        foreach($answer as $ans) {
            $option_id = self::getOptionByName($db, $id, $options[$ans]);
            if($option_id !== -1) {
                $sql = "UPDATE options SET iscorrect=:iscorrect WHERE id=:id";
                $stmt = $db->prepare($sql);
                $stmt->bindValue(':id', $option_id, PDO::PARAM_INT);
                $stmt->bindValue(':iscorrect', true, PDO::PARAM_BOOL);
                $stmt->execute();
            }
        }
    }

    private static function getOptionByName($db, $id, $ans) {
        $sql = "Select * from options where question_id=:question_id";

        $stmt = $db->prepare($sql);
        $stmt->bindValue(':question_id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $options = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach($options as $option) {
            $sql = "Select * from resource where row_id=:row_id and column_n=:column_n and table_n=:table_n and text=:text";

            $stmt = $db->prepare($sql);
            $stmt->bindValue(':row_id', $option['id'], PDO::PARAM_INT);
            $stmt->bindValue(':column_n', self::OPTION, PDO::PARAM_STR);
            $stmt->bindValue(':table_n', self::OPTION_TABLE_NAME, PDO::PARAM_STR);
            $stmt->bindValue(':text', $ans, PDO::PARAM_STR);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if($result['row_id'] === $option['id']) {
                return $option['id'];
            }
        }
        return -1;
    }


}