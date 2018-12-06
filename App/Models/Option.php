<?php

namespace App\Models;

use PDO;

/**
 * Example GameType model
 *
 * PHP version 7.0
 */
class Option extends \Core\Model
{
    const TABLE_NAME = "options";
    const OPTION = "option";
    const LANGUAGE = "english";

    public static function addOptions($db, $id, $options) {
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

            if($stmt->rowcount() > 0) {
                $option_id = self::getLatestOptionID($db);
                if ($option_id) {
                    if (!self::addOption($db, $option_id, $value)) {
                        return false;
                    }
                }
            }
        }
        return true;
    }

    private static function getLatestOptionID($db) {
        $sql = "SELECT MAX(id) from options";

        $stmt = $db->prepare($sql);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $result = $stmt->fetch();
        if($result) {
            $id = $result['MAX(id)'];
            return $id;
        }
        return 0;
    }

    private static function addOption($db, $option_id, $option) {
        $sql = "Insert into resource(table_n, column_n, row_id, text, lang)
                            values(:table_n, :column_n, :row_id, :text, :lang)";

        $stmt = $db->prepare($sql);
        $stmt->bindValue(':table_n', self::TABLE_NAME, PDO::PARAM_STR);
        $stmt->bindValue(':column_n', self::OPTION, PDO::PARAM_STR);
        $stmt->bindValue(':row_id', $option_id, PDO::PARAM_INT);
        $stmt->bindValue(':text', $option, PDO::PARAM_STR);
        $stmt->bindValue(':lang', self::LANGUAGE, PDO::PARAM_STR);

        $stmt->execute();
        return $stmt->rowcount() > 0;
    }

    public static function updateAnswer($db, $id, $options, $answer) {
        foreach($answer as $ans) {
            $option_id = self::getOptionByName($db, $id, $options[$ans]);
            if($option_id) {
                $sql = "UPDATE options SET iscorrect=:iscorrect WHERE id=:id";
                $stmt = $db->prepare($sql);
                $stmt->bindValue(':id', $option_id, PDO::PARAM_INT);
                $stmt->bindValue(':iscorrect', true, PDO::PARAM_BOOL);
                $stmt->execute();
            }
        }
    }

    public static function updatePoints($db, $id, $options, $points) {
        foreach($points as $key => $value) {
            $option_id = self::getOptionByName($db, $id, $options[$key]);
            if($option_id) {
                $sql = "UPDATE options SET points=:points WHERE id=:id";
                $stmt = $db->prepare($sql);
                $stmt->bindValue(':id', $option_id, PDO::PARAM_INT);
                $stmt->bindValue(':points', $value, PDO::PARAM_INT);
                $stmt->execute();
            }
        }
    }

    public static function updateBadges($db, $id, $options, $badges) {
        foreach($badges as $key => $value) {
            $option_id = self::getOptionByName($db, $id, $options[$key]);
            if($option_id) {
                $sql = "UPDATE options SET badge_id=:badge_id WHERE id=:id";
                $stmt = $db->prepare($sql);
                $stmt->bindValue(':id', $option_id, PDO::PARAM_INT);
                $stmt->bindValue(':badge_id', $value == 0 ? null : $value, PDO::PARAM_INT);
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
            $sql = "Select * from resource where row_id=:row_id and column_n=:column_n and table_n=:table_n and text=:text and lang=:lang";

            $stmt = $db->prepare($sql);
            $stmt->bindValue(':row_id', $option['id'], PDO::PARAM_INT);
            $stmt->bindValue(':column_n', self::OPTION, PDO::PARAM_STR);
            $stmt->bindValue(':table_n', self::TABLE_NAME, PDO::PARAM_STR);
            $stmt->bindValue(':text', $ans, PDO::PARAM_STR);
            $stmt->bindValue(':lang', self::LANGUAGE, PDO::PARAM_STR);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if($result['row_id'] === $option['id']) {
                return $option['id'];
            }
        }
        return null;
    }

    public static function getAllOptions($id)
    {
        $sql = "SELECT * FROM options WHERE isdeleted=:isdeleted and question_id=:question_id";

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':isdeleted', false, PDO::PARAM_BOOL);
        $stmt->bindValue(':question_id', $id, PDO::PARAM_INT);

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
            $options = $rows;
        } else {
            $options = $result;
        }
        return $options;
    }

    private static function getResourceForId($id, $db) {
        $sql = "SELECT * from resource where row_id=:row_id and table_n=:table_n and column_n=:column_n and lang=:lang";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':row_id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':table_n', self::TABLE_NAME, PDO::PARAM_STR);
        $stmt->bindValue(':column_n', self::OPTION, PDO::PARAM_STR);
        $stmt->bindValue(':lang', self::LANGUAGE, PDO::PARAM_STR);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public static function updateOptions($db, $id, $newOptions) {
        $options = self::getAllOptions($id);
        $i = 1;
        foreach($options as $option) {
            $sql = "UPDATE options SET date_updated=:date_updated, user_updated=:user_updated WHERE question_id=:question_id and id=:id";
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':question_id', $id, PDO::PARAM_INT);
            $stmt->bindValue(':date_updated', date('Y-m-d H:i:s', time()), PDO::PARAM_STR);
            $stmt->bindValue(':user_updated', $_SESSION['user_id'], PDO::PARAM_INT);
            $stmt->bindValue(':id', $option['id'], PDO::PARAM_INT);

            $stmt->execute();
            if($option['option'] !== $newOptions['optionA'.$i]) {
                self::updateOption($db, $option['id'], $newOptions['optionA' . $i]);
            }
            $i++;
        }
    }

    private static function updateOption($db, $option_id, $option) {
        $sql = "UPDATE resource SET text=:text WHERE row_id=:row_id and table_n=:table_n and column_n=:column_n and lang=:lang";

        $stmt = $db->prepare($sql);
        $stmt->bindValue(':table_n', self::TABLE_NAME, PDO::PARAM_STR);
        $stmt->bindValue(':column_n', self::OPTION, PDO::PARAM_STR);
        $stmt->bindValue(':row_id', $option_id, PDO::PARAM_INT);
        $stmt->bindValue(':text', $option, PDO::PARAM_STR);
        $stmt->bindValue(':lang', self::LANGUAGE, PDO::PARAM_STR);

        $stmt->execute();
        return $stmt->rowcount() > 0;
    }

    public static function getCorrectOptions($questionid) {

        $sql = "Select id from options where question_id=:question_id and iscorrect=:iscorrect";
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':question_id', $questionid, PDO::PARAM_INT);
        $stmt->bindValue(':iscorrect', true, PDO::PARAM_BOOL);
        
        $stmt->execute();

        $options = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $options;
    }

    public static function getOptionById($id)
    {
        $sql = "SELECT * FROM options WHERE id=:id";

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

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
            $option = $rows;
        } else {
            $option = $result;
        }
        return $option;
    }

    public static function updatePOptions($db, $id, $newOptions, $options_ids, $answer, $points, $badges)
    {
        $options = self::getAllOptions($id);
        $i = 1;
        foreach($options as $option) {
            if(in_array($option['id'], $options_ids)) {
                $sql = "UPDATE options SET points=:points, badge_id=:badge_id, iscorrect=:iscorrect, date_updated=:date_updated, user_updated=:user_updated WHERE question_id=:question_id and id=:id";
                $stmt = $db->prepare($sql);
                $stmt->bindValue(':question_id', $id, PDO::PARAM_INT);
                $stmt->bindValue(':iscorrect', false, PDO::PARAM_BOOL);
                $stmt->bindValue(':points', 0, PDO::PARAM_INT);
                $stmt->bindValue(':badge_id', null, PDO::PARAM_INT);
                $stmt->bindValue(':date_updated', date('Y-m-d H:i:s', time()), PDO::PARAM_STR);
                $stmt->bindValue(':user_updated', $_SESSION['user_id'], PDO::PARAM_INT);
                $stmt->bindValue(':id', $option['id'], PDO::PARAM_INT);

                $stmt->execute();
                if (array_key_exists('optionA' . $i, $newOptions)) {
                    self::updateOption($db, $option['id'], $newOptions['optionA' . $i]);
                }
                $i++;
            } else {
                $sql = "UPDATE options SET points=:points, badge_id=:badge_id, date_updated=:date_updated, user_updated=:user_updated, iscorrect=:iscorrect, isdeleted=:isdeleted WHERE question_id=:question_id and id=:id";
                $stmt = $db->prepare($sql);
                $stmt->bindValue(':question_id', $id, PDO::PARAM_INT);
                $stmt->bindValue(':isdeleted', true, PDO::PARAM_BOOL);
                $stmt->bindValue(':iscorrect', false, PDO::PARAM_BOOL);
                $stmt->bindValue(':points', 0, PDO::PARAM_INT);
                $stmt->bindValue(':badge_id', null, PDO::PARAM_INT);
                $stmt->bindValue(':date_updated', date('Y-m-d H:i:s', time()), PDO::PARAM_STR);
                $stmt->bindValue(':user_updated', $_SESSION['user_id'], PDO::PARAM_INT);
                $stmt->bindValue(':id', $option['id'], PDO::PARAM_INT);

                $stmt->execute();
            }
        }
        foreach($newOptions as $key => $value) {
            $option = self::getOptionByName($db, $id, $value);
            if($option) {
                $sql = "UPDATE options SET points=:points, badge_id=:badge_id, date_updated=:date_updated, user_updated=:user_updated, isdeleted=:isdeleted WHERE question_id=:question_id and id=:id";
                $stmt = $db->prepare($sql);
                $stmt->bindValue(':question_id', $id, PDO::PARAM_INT);
                $stmt->bindValue(':isdeleted', false, PDO::PARAM_BOOL);
                $stmt->bindValue(':points', 0, PDO::PARAM_INT);
                $stmt->bindValue(':badge_id', null, PDO::PARAM_INT);
                $stmt->bindValue(':date_updated', date('Y-m-d H:i:s', time()), PDO::PARAM_STR);
                $stmt->bindValue(':user_updated', $_SESSION['user_id'], PDO::PARAM_INT);
                $stmt->bindValue(':id', $option, PDO::PARAM_INT);

                $stmt->execute();
            } else {
                $sql = "Insert into options(question_id, iscorrect, date_created, user_created, date_updated, user_updated, isdeleted, points, badge_id)
                            values(:question_id, :iscorrect, :date_created, :user_created, :date_updated, :user_updated, :isdeleted, :points, :badge_id)";

                $stmt = $db->prepare($sql);
                $stmt->bindValue(':question_id', $id, PDO::PARAM_INT);
                $stmt->bindValue(':iscorrect', false, PDO::PARAM_BOOL);
                $stmt->bindValue(':points', 0, PDO::PARAM_INT);
                $stmt->bindValue(':badge_id', null, PDO::PARAM_INT);
                $stmt->bindValue(':date_created', date('Y-m-d H:i:s', time()), PDO::PARAM_STR);
                $stmt->bindValue(':user_created', $_SESSION['user_id'], PDO::PARAM_INT);
                $stmt->bindValue(':date_updated', date('Y-m-d H:i:s', time()), PDO::PARAM_STR);
                $stmt->bindValue(':user_updated', $_SESSION['user_id'], PDO::PARAM_INT);
                $stmt->bindValue(':isdeleted', false, PDO::PARAM_BOOL);
                $stmt->execute();

                if ($stmt->rowcount() > 0) {
                    $option_id = self::getLatestOptionID($db);
                    if ($option_id) {
                        if (!self::addOption($db, $option_id, $value)) {
                            return false;
                        }
                    }
                }
            }
        }
        if($answer) {
            self::updateAnswer($db, $id, $newOptions, $answer);
        }
        if($points) {
            self::updatePoints($db, $id, $newOptions, $points);
        }
        if($badges) {
            self::updateBadges($db, $id, $newOptions, $badges);
        }
    }

}