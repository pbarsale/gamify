<?php
/**
 * Created by PhpStorm.
 * User: priyankanaik
 * Date: 07/10/2018
 * Time: 01:06
 */

include_once 'dbh.inc.php';

if (isset($_POST['add'])) {
    addBadge($conn);
} elseif (isset($_POST['delete'])) {
    deleteBadge($conn);
} elseif (isset($_POST['update'])) {
    updateBadge($conn);
} else {
    header("Location: ../badge.php");
    exit();
}

function addBadge($conn) {
    $badge = mysqli_real_escape_string($conn, $_POST['badge']);

    if (empty($badge)) {
        header("Location: ../badge.php?badge=empty");
        exit();
    } else {
        $sql = "INSERT INTO badge (name, date_created, user_created, date_updated, user_updated, isdeleted) VALUES ('$badge', now(), 4, now(), 4, false)";
        mysqli_query($conn, $sql);
        header("Location: ../badge.php?badge=success");
        exit();
    }
}

function deleteBadge($conn) {
    $selected_badge = mysqli_real_escape_string($conn, $_POST['select-badge']);

    if (empty($game)) {
        header("Location: ../badge.php?badge=empty");
        exit();
    } else {
        $sql = "UPDATE badge SET isdeleted = true, date_updated = now(), user_updated = 4 WHERE name = $selected_badge";
        mysqli_query($conn, $sql);
        header("Location: ../badge.php?badge=success");
        exit();
    }
}

function updateBadge($conn) {
    $selected_badge = mysqli_real_escape_string($conn, $_POST['select-badge']);
    $badge = mysqli_real_escape_string($conn, $_POST['badge']);

    if (empty($game)) {
        header("Location: ../badge.php?badge=empty");
        exit();
    } else {
        $sql = "UPDATE badge SET name = $badge, date_updated = now(), user_updated = 4 WHERE name = $selected_badge";
        mysqli_query($conn, $sql);
        header("Location: ../badge.php?badge=success");
        exit();
    }
}

function getAllGame($conn) {
    $sql = "SELECT * FROM badge WHERE isdeleted = false";
    $result = mysqli_query($conn, $sql);
    $resultCheck = mysqli_num_rows($result);
    if($resultCheck < 1) {
        header("Location: ../badge.php?badge=error");
        exit();
    } else {
        header("Location: ../badge.php?badge=success");
        exit();
    }
}

function getGame($conn, $name) {
    $sql = "SELECT * FROM badge WHERE name = $name";
    $result = mysqli_query($conn, $sql);
    $resultCheck = mysqli_num_rows($result);
    if($resultCheck < 1) {
        header("Location: ../badge.php?badge=error");
        exit();
    } else {
        header("Location: ../badge.php?badge=success");
        exit();
    }
}
