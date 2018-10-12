<?php
/**
 * Created by PhpStorm.
 * User: priyankanaik
 * Date: 07/10/2018
 * Time: 00:32
 */

include_once 'dbh.inc.php';

if (isset($_POST['add'])) {
    addGame($conn);
} elseif (isset($_POST['delete'])) {
    deleteGame($conn);
} elseif (isset($_POST['update'])) {
    updateGame($conn);
} else {
    header("Location: ../game.php");
    exit();
}

function addGame($conn) {
    $game = mysqli_real_escape_string($conn, $_POST['game']);

    if (empty($game)) {
        header("Location: ../game.php?game=empty");
        exit();
    } else {
        $sql = "INSERT INTO game (name, date_created, user_created, date_updated, user_updated, isdeleted) VALUES ('$game', now(), 4, now(), 4, false)";
        mysqli_query($conn, $sql);
        header("Location: ../game.php?game=success");
        exit();
    }
}

function deleteGame($conn) {
    $selected_game = mysqli_real_escape_string($conn, $_POST['select-game']);

    if ($selected_game === '0') {
        header("Location: ../game.php?game=empty");
        exit();
    } else {
        $sql = "UPDATE game SET isdeleted = true, date_updated = now(), user_updated = 4 WHERE id = '$selected_game'";
        mysqli_query($conn, $sql);
        header("Location: ../game.php?game=success");
        exit();
    }
}

function updateGame($conn) {
    $selected_game = mysqli_real_escape_string($conn, $_POST['select-game']);
    $game = mysqli_real_escape_string($conn, $_POST['game']);

    if (empty($game) || $selected_game === '0') {
        header("Location: ../game.php?game=empty");
        exit();
    } else {
        $sql = "UPDATE game SET name = '$game', date_updated = now(), user_updated = 4 WHERE id = '$selected_game'";
        mysqli_query($conn, $sql);
        header("Location: ../game.php?game=success");
        exit();
    }
}

function getAllGame($conn) {
    $sql = "SELECT * FROM game WHERE isdeleted = false";
    $result = mysqli_query($conn, $sql);
    $resultCheck = mysqli_num_rows($result);
    if($resultCheck < 1) {
        header("Location: ../game.php?game=error");
        exit();
    } else {
        header("Location: ../game.php?game=success");
        exit();
    }
}

function getGame($conn, $name) {
    $sql = "SELECT * FROM game WHERE name = $name";
    $result = mysqli_query($conn, $sql);
    $resultCheck = mysqli_num_rows($result);
    if($resultCheck < 1) {
        header("Location: ../game.php?game=error");
        exit();
    } else {
        header("Location: ../game.php?game=success");
        exit();
    }
}
