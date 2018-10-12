<?php
/**
 * Created by PhpStorm.
 * User: priyankanaik
 * Date: 07/10/2018
 * Time: 23:46
 */

include_once 'dbh.inc.php';

function getAllGameType($conn) {
    $sql = "SELECT * FROM game_type WHERE isdeleted = false";
    $result = mysqli_query($conn, $sql);
    return $result;
//    $resultCheck = mysqli_num_rows($result);
//    if($resultCheck < 1) {
//        header("Location: ../gametype.php?gametype=error");
//        exit();
//    } else {
//    }
}

function getAllGame($conn) {
    $sql = "SELECT * FROM game WHERE isdeleted = false";
    $result = mysqli_query($conn, $sql);
    return $result;
//    $resultCheck = mysqli_num_rows($result);
//    if($resultCheck < 1) {
//        header("Location: ../game.php?game=error");
//        exit();
//    } else {
//        header("Location: ../game.php?game=success");
//        exit();
//    }
}