<?php
/**
 * Created by PhpStorm.
 * User: priyankanaik
 * Date: 04/10/2018
 * Time: 21:30
 */

include_once 'dbh.inc.php';

if (isset($_POST['add'])) {
    addGameType($conn);
} elseif (isset($_POST['delete'])) {
    deleteGameType($conn);
} elseif (isset($_POST['update'])) {
    updateGameType($conn);
} else {
    header("Location: ../gametype.php");
    exit();
}

function addGameType($conn) {
    $game_type = mysqli_real_escape_string($conn, $_POST['game-type']);

    if (empty($game_type)) {
        header("Location: ../gametype.php?gametype=empty");
        exit();
    } else {
        $sql = "INSERT INTO game_type (name, date_created, user_created, date_updated, user_updated, isdeleted) VALUES ('$game_type', now(), 4, now(), 4, false)";
        mysqli_query($conn, $sql);
        header("Location: ../gametype.php?gametype=success");
        exit();
    }
}

function deleteGameType($conn) {
    $selected_game_type = mysqli_real_escape_string($conn, $_POST['select-game-type']);

    if (empty($game_type)) {
        header("Location: ../gametype.php?gametype=empty");
        exit();
    } else {
        $sql = "UPDATE game_type SET isdeleted = true, date_updated = now(), user_updated = 4 WHERE name = $selected_game_type";
        mysqli_query($conn, $sql);
        header("Location: ../gametype.php?gametype=success");
        exit();
    }
}

function updateGameType($conn) {
    $selected_game_type = mysqli_real_escape_string($conn, $_POST['select-game-type']);
    $game_type = mysqli_real_escape_string($conn, $_POST['game-type']);

    if (empty($game_type)) {
        header("Location: ../gametype.php?gametype=empty");
        exit();
    } else {
        $sql = "UPDATE game_type SET name = $game_type, date_updated = now(), user_updated = 4 WHERE name = $selected_game_type";
        mysqli_query($conn, $sql);
        header("Location: ../gametype.php?gametype=success");
        exit();
    }
}

function getAllGameType($conn) {
    $sql = "SELECT * FROM game_type WHERE isdeleted = false";
    $result = mysqli_query($conn, $sql);
    $resultCheck = mysqli_num_rows($result);
    if($resultCheck < 1) {
        header("Location: ../gametype.php?gametype=error");
        exit();
    } else {
        header("Location: ../gametype.php?gametype=success");
        exit();
    }
}

function getGameType($conn, $name) {
    $sql = "SELECT * FROM game_type WHERE name = $name";
    $result = mysqli_query($conn, $sql);
    $resultCheck = mysqli_num_rows($result);
    if($resultCheck < 1) {
        header("Location: ../gametype.php?gametype=error");
        exit();
    } else {
        header("Location: ../gametype.php?gametype=success");
        exit();
    }
}
