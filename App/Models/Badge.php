<?php

namespace App\Models;

use App\Flash;
use PDO;
/**
 * Example GameType model
 *
 * PHP version 7.0
 */
class Badge extends \Core\Model
{
    const TABLE_NAME = "badges";
    const DESCRIPTION = "description";
    const LANGUAGE = "english";
    const NAME = "name";
    const FILEPATH = "/museum/Gamify/";

    public static function addBadge($badge_name, $uploaded_file, $description) {
        $badge_obj = self::getBadgeByName($badge_name);

        $last_id = self::getLatestBadgeID();

        $badge_tmp = $uploaded_file['tmp_name'];
        $badge_type = $uploaded_file['type'];
        $file_type = self::getFileType($badge_type);
        $filepath = "images/badge_" . ($last_id + 1) . "." . $file_type;

        if(!$badge_obj and
            self::validateImage($badge_type) and
            move_uploaded_file($badge_tmp, $filepath)) {
            chmod($filepath, 0777);

            $sql = "Insert into badges(badge, date_created, user_created, date_updated, user_updated, isdeleted)
                            values(:badge, :date_created, :user_created, :date_updated, :user_updated, :isdeleted)";

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':badge', self::FILEPATH . $filepath, PDO::PARAM_STR);
            $stmt->bindValue(':date_created', date('Y-m-d H:i:s', time()), PDO::PARAM_STR);
            $stmt->bindValue(':user_created', $_SESSION['user_id'], PDO::PARAM_INT);
            $stmt->bindValue(':date_updated', date('Y-m-d H:i:s', time()), PDO::PARAM_STR);
            $stmt->bindValue(':user_updated', $_SESSION['user_id'], PDO::PARAM_INT);
            $stmt->bindValue(':isdeleted', false, PDO::PARAM_BOOL);
            $stmt->execute();

            if ($stmt->rowcount() > 0) {

                $id = self::getLatestID($db);

                if($id) {
                    if(self::insertBadgeInResource($db, $id, $badge_name)) {
                        if($description) {
                            return self::insertDescriptionInResource($db, $id, $description);
                        }
                        return true;
                    } else {
                        Flash::addMessage('Badge Addition Failed!', 'warning');
                    }
                } else {
                    Flash::addMessage('Badge Not Found!', 'warning');
                }
            } else {
                Flash::addMessage('Query execution failed', 'warning');
            }
        } else {
            Flash::addMessage('Badge Already Exists!', 'warning');
        }
        return false;
    }

    private static function getLatestBadgeID() {
        $sql = "SELECT MAX(id) from badges";

        $db = static::getDB();
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

    private static function insertBadgeInResource($db, $id, $badge_name) {
        $sql = "Insert into resource(table_n, column_n, row_id, text, lang)
                            values(:table_n, :column_n, :row_id, :text, :lang)";

        $stmt = $db->prepare($sql);
        $stmt->bindValue(':table_n', self::TABLE_NAME, PDO::PARAM_STR);
        $stmt->bindValue(':column_n', self::NAME, PDO::PARAM_STR);
        $stmt->bindValue(':row_id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':text', $badge_name, PDO::PARAM_STR);
        $stmt->bindValue(':lang', self::LANGUAGE, PDO::PARAM_STR);

        $stmt->execute();
        return $stmt->rowcount() > 0;
    }

    private static function insertDescriptionInResource($db, $id, $description) {
        $sql = "Insert into resource(table_n, column_n, row_id, text, lang)
                            values(:table_n, :column_n, :row_id, :text, :lang)";

        $stmt = $db->prepare($sql);
        $stmt->bindValue(':table_n', self::TABLE_NAME, PDO::PARAM_STR);
        $stmt->bindValue(':column_n', self::DESCRIPTION, PDO::PARAM_STR);
        $stmt->bindValue(':row_id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':text', $description, PDO::PARAM_STR);
        $stmt->bindValue(':lang', self::LANGUAGE, PDO::PARAM_STR);

        $stmt->execute();
        return $stmt->rowcount() > 0;
    }

    private static function getLatestID($db) {
        $sql = "SELECT MAX(id) from badges";

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

    private static function getFileType($badge_type)
    {
        $i = strlen($badge_type) - 1;
        while($badge_type[$i] !== '/') {
            $i--;
        }
        return substr($badge_type, $i + 1);
    }

    public function deleteBadge() {
        $sql = 'UPDATE badges SET isdeleted = :isdeleted, date_updated = :date_updated, user_updated = :user_updated WHERE id=:id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':isdeleted', true, PDO::PARAM_BOOL);
        $stmt->bindValue(':date_updated', date('Y-m-d H:i:s', time()), PDO::PARAM_STR);
        $stmt->bindValue(':user_updated', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->rowcount() > 0;
    }

    public function updateBadge($uploaded_file) {
        $sql = 'UPDATE badges SET badge = :badge, date_updated = :date_updated, user_updated = :user_updated WHERE id=:id';

        $badge_tmp = $uploaded_file['tmp_name'];
        $badge = $uploaded_file['name'];
        $badge_type = $uploaded_file['type'];
        $filepath = "images/" . $badge;

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':badge', self::FILEPATH . $filepath, PDO::PARAM_STR);
        $stmt->bindValue(':date_updated', date('Y-m-d H:i:s', time()), PDO::PARAM_STR);
        $stmt->bindValue(':user_updated', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);

        if (self::validateImage($badge_type) and move_uploaded_file($badge_tmp, $filepath)) {
            chmod($filepath, 0777);
            $stmt->execute();
            return $stmt->rowcount() > 0;
        } else {
            Flash::addMessage('Badge File Exists!');
        }
        return false;
    }

    public static function getAllBadges() {
        $sql = "SELECT * FROM badges WHERE isdeleted=:isdeleted";

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
            $badges = $rows;
        } else {
            $badges = $result;
        }
        return $badges;
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

    public static function getBadgeByName($name) {
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

    public static function getBadgeById($id) {
        $sql = "SELECT * from badges where id=:id";
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        $stmt->setFetchMode(PDO::FETCH_CLASS,get_called_class());

        $stmt->execute();
        return $stmt->fetch();
    }

    private static function validateImage($badge_type) {
        return preg_match('/^image\\/p?jpeg$/i', $badge_type) or
        preg_match('/^image\\/gif$/i', $badge_type) or
        preg_match('/^image\\/(x-)?png$/i', $badge_type);
    }
}