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
    public static function getLeaderBoardForQuiz($users) {
        return self::getAllUsersFromQuiz($users);
    }

    public static function getLeaderBoardForScavengerHunt($users) {
        return self::getAllUsersFromScavengerHunt($users);
    }

    private static function getAllUsersFromQuiz($users)
    {
        $sql = "SELECT user_id, sum(points) FROM quiz_points group by user_id";

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $leaderBoardUsers = array();

        if($result) {
            foreach ($result as $selected_row) {
                $user = User::ifUserPresent($selected_row['user_id'], $users);
                if($user) {
                    $user->points = $selected_row['sum(points)'];
                    $user->badges = self::getBadgesOfUserForQuiz($selected_row['user_id']);
                    array_push($leaderBoardUsers, $user);
                }
            }
        } else {
            $leaderBoardUsers = $result;
        }
        return $leaderBoardUsers;
    }

    private static function getAllUsersFromScavengerHunt($users)
    {
        $sql = "SELECT user_id, sum(points) FROM scavenger_hunt_points group by user_id";

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $leaderBoardUsers = array();

        if($result) {
            foreach ($result as $selected_row) {
                $user = User::ifUserPresent($selected_row['user_id'], $users);
                if($user) {
                    $user->points = $selected_row['sum(points)'];
                    $user->badges = self::getBadgesOfUserForScavengerHunt($selected_row['user_id']);
                    array_push($leaderBoardUsers, $user);
                }
            }
        } else {
            $leaderBoardUsers = $result;
        }
        return $leaderBoardUsers;
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
        $sql = "SELECT badge_id FROM scavenger_hunt_points where user_id=:user_id";

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);

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