<?php

namespace App\Models;

use PDO;

/**
 * Example GameType model
 *
 * PHP version 7.0
 */
class LeaderBoard extends \Core\Model
{
    const COMPLETED = "completed";
    const PENDING = "pending";

    public static function getLeaderBoard($users) {
        self::getAllUsersFromQuiz($users);
        self::getAllUsersFromScavengerHunt($users);
        usort($users, function($a, $b) {
            return $b->points == $a->points ? count($b->badges) - count($a->badges) : $b->points - $a->points;
        });
        return $users;
    }

    private static function getAllUsersFromQuiz($users)
    {
        $sql = "SELECT user_id, sum(points) FROM quiz_points group by user_id";

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if($result) {
            foreach ($users as $user) {
                $presentUser = self::ifUserPresent($user->id, $result);
                $user->points = $presentUser === null ? 0 : $presentUser['sum(points)'];
                $user->badges = self::getBadgesOfUserForQuiz($user->id);
            }
        }
        return $users;
    }

    public static function ifUserPresent($id, $users) {
        foreach ($users as $user) {
            if($user['user_id'] === $id) {
                return $user;
            }
        }
        return null;
    }

    private static function getAllUsersFromScavengerHunt($users)
    {
        $sql = "SELECT user_id, sum(points) FROM scavenger_hunt_points where status=:status group by user_id";

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':status', self::COMPLETED, PDO::PARAM_STR);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if($result) {
            foreach ($users as $user) {
                $presentUser = self::ifUserPresent($user->id, $result);
                $user->points += $presentUser === null ? 0 : $presentUser['sum(points)'];
                array_push($user->badges, self::getBadgesOfUserForScavengerHunt($user->id));
            }
        }
        return $users;
    }

    private static function getBadgesOfUserForQuiz($user_id)
    {
        $sql = "SELECT badge_id FROM quiz_points where user_id=:user_id";

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        $badge_ids = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return self::getBadges($badge_ids);
    }

    private static function getBadgesOfUserForScavengerHunt($user_id)
    {
        $sql = "SELECT badge_id FROM scavenger_hunt_points where user_id=:user_id and status=:status";

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindValue(':status', self::COMPLETED, PDO::PARAM_STR);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        $badge_ids = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return self::getBadges($badge_ids);
    }

    private static function getBadges($badge_ids) {
        $badges = array();
        foreach ($badge_ids as $badge_id) {
            $sql = "SELECT badge FROM badges where id=:id";

            $db = static::getDB();
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':id', $badge_id['badge_id'], PDO::PARAM_INT);

            $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

            $stmt->execute();
            array_push($badges, $stmt->fetch(PDO::FETCH_ASSOC));
        }
        return $badges;
    }

}