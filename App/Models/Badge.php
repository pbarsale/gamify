<?php

namespace App\Models;

use PDO;
use \Core\View;

/**
 * Example GameType model
 *
 * PHP version 7.0
 */
class Badge extends \Core\Model
{
    public static function addBadge($badge_name, $uploaded_file) {
        $badge_obj = self::getBadgeByName($badge_name);

        $badge_tmp = $uploaded_file['tmp_name'];
        $badge = $uploaded_file['name'];
        $badge_type = $uploaded_file['type'];
        $filepath = "images/" . $badge;

        if(!$badge_obj and
            self::validateImage($badge_type) and
            move_uploaded_file($badge_tmp, $filepath)) {
            $sql = "Insert into badge(name, badge, description, date_created, user_created, date_updated, user_updated, isdeleted)
                            values(:name, :badge, :description, :date_created, :user_created, :date_updated, :user_updated, :isdeleted)";

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':name', $badge_name, PDO::PARAM_STR);
            $stmt->bindValue(':badge', $filepath, PDO::PARAM_STR);
            $stmt->bindValue(':description', '', PDO::PARAM_STR);
            $stmt->bindValue(':date_created', date('Y-m-d H:i:s', time()), PDO::PARAM_STR);
            $stmt->bindValue(':user_created', 16, PDO::PARAM_INT);
            $stmt->bindValue(':date_updated', date('Y-m-d H:i:s', time()), PDO::PARAM_STR);
            $stmt->bindValue(':user_updated', 16, PDO::PARAM_INT);
            $stmt->bindValue(':isdeleted', false, PDO::PARAM_BOOL);
            return $stmt->execute();
        } else {
            var_dump("Already present");
        }
    }

    public function deleteBadge() {
        $sql = 'UPDATE badge SET isdeleted = :isdeleted, date_updated = :date_updated, user_updated = :user_updated WHERE id=:id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':isdeleted', true, PDO::PARAM_BOOL);
        $stmt->bindValue(':date_updated', date('Y-m-d H:i:s', time()), PDO::PARAM_STR);
        $stmt->bindValue(':user_updated', 16, PDO::PARAM_INT);
        $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function updateBadge($uploaded_file) {
        $sql = 'UPDATE badge SET badge = :badge, date_updated = :date_updated, user_updated = :user_updated WHERE id=:id';

        $badge_tmp = $uploaded_file['tmp_name'];
        $badge = $uploaded_file['name'];
        $badge_type = $uploaded_file['type'];
        $filepath = "images/" . $badge;

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':badge', $filepath, PDO::PARAM_STR);
        $stmt->bindValue(':date_updated', date('Y-m-d H:i:s', time()), PDO::PARAM_STR);
        $stmt->bindValue(':user_updated', 16, PDO::PARAM_INT);
        $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);

        if (self::validateImage($badge_type) and
            move_uploaded_file($badge_tmp, $filepath)) {
            return $stmt->execute();
        } else {
            var_dump("Already present");
        }
    }

    public static function getAllBadges() {
        $sql = "SELECT * FROM badge WHERE isdeleted=:isdeleted";

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':isdeleted', false, PDO::PARAM_BOOL);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        return $stmt->fetchAll();
    }

    public static function getBadgeByName($name) {
        $sql = "SELECT * from badge where name=:name";
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);

        $stmt->setFetchMode(PDO::FETCH_CLASS,get_called_class());

        $stmt->execute();
        return $stmt->fetch();
    }

    public static function getBadgeById($id) {
        $sql = "SELECT * from badge where id=:id";
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