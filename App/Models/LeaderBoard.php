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

    public static function getLeaderBoard($users) {
        foreach($users as $user) {
            $points = LeaderBoard::getPointsOfUser($user);
            $user->points = $points ? $points : 0;
            $badges = LeaderBoard::getBadgesOfUser($user);
            $user->badges = $badges ? $badges : null;
        }
        usort($users, function($a, $b) {
            return $b->points == $a->points ? count($b->badges) - count($a->badges) : $b->points - $a->points;
        });
        return $users;
    }

    public static function getPointsOfUser($user = null){
        if((isset($_SESSION['user_id']) && !$_SESSION['admin']) || $user){

            $sql = "select sum(points) as points from 
                          (SELECT q.user_id, q.points from quiz_points q where user_id=:id
                            union all
                            SELECT sh.user_id,sh.points from scavenger_hunt_points sh where user_id=:id and status=:status
                            union all 
                            SELECT up.user_id, up.points from user_points up where user_id=:id) 
                    as points_table";

            $db = static::getDB();
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':status', self::COMPLETED, PDO::PARAM_STR);
            if($user) {
                $stmt->bindValue(':id', $user->id, PDO::PARAM_INT);
            } else {
                $stmt->bindValue(':id', $_SESSION['user_id'], PDO::PARAM_INT);
            }

            $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if($result){
                return $result['points'];
            }
            return $result;
        }
        return 0;
    }

    public static function getBadgesOfUser($user = null){
        if((isset($_SESSION['user_id']) && !$_SESSION['admin']) || $user){

            $sql = "select badge_id, badge, count from badges inner join
                        (select badge_id, count(*) as count from 
                                (SELECT q.user_id, q.badge_id 
                                from quiz_points q
                                where user_id=:id
                                union all
                                SELECT sh.user_id,sh.badge_id 
                                from scavenger_hunt_points sh
                                where user_id=:id
                                and status=:status
                                union all 
                                SELECT up.user_id,up.badge_id 
                                from user_badges up
                                where user_id=:id) as badge_all
                        group by badge_id) as badge_total
                    where badges.id = badge_total.badge_id";

            $db = static::getDB();
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':status', self::COMPLETED, PDO::PARAM_STR);
            if($user) {
                $stmt->bindValue(':id', $user->id, PDO::PARAM_INT);
            } else {
                $stmt->bindValue(':id', $_SESSION['user_id'], PDO::PARAM_INT);
            }

            $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        }
        return null;
    }
}