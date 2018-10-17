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
    $badge_name = mysqli_real_escape_string($conn, $_POST['badge-name']);

    $badge_tmp = $_FILES['badge']['tmp_name'];
    $badge = $_FILES['badge']['name'];
    $badge_type = $_FILES['badge']['type'];
    $filepath = "images/" . $badge;

    if (!empty($badge_name) and
        preg_match('/^image\\/p?jpeg$/i', $badge_type) or
        preg_match('/^image\\/gif$/i', $badge_type) or
        preg_match('/^image\\/(x-)?png$/i', $badge_type) and
        move_uploaded_file($badge_tmp, "../" . $filepath)) {
        $sql = "INSERT INTO badge (name, badge, description, date_created, user_created, date_updated, user_updated, isdeleted) VALUES ('$badge_name', '$filepath', '', now(), 4, now(), 4, false)";
        mysqli_query($conn, $sql);
        header("Location: ../badge.php?badge=success");
        exit();
    } else {
        header("Location: ../badge.php?badge=invalid");
        exit();
    }
}

function deleteBadge($conn) {
    $selected_badge = mysqli_real_escape_string($conn, $_POST['select-badge']);

    if ($selected_badge === '0') {
        header("Location: ../badge.php?badge=empty");
        exit();
    } else {
        $sql = "UPDATE badge SET isdeleted = true, date_updated = now(), user_updated = 4 WHERE id = $selected_badge";
        mysqli_query($conn, $sql);
        header("Location: ../badge.php?badge=success");
        exit();
    }
}

function updateBadge($conn) {
    $selected_badge = mysqli_real_escape_string($conn, $_POST['select-badge']);

    $badge_tmp = $_FILES['badge']['tmp_name'];
    $badge = $_FILES['badge']['name'];
    $badge_type = $_FILES['badge']['type'];
    $filepath = "images/" . $badge;

    if ($selected_badge === '0') {
        header("Location: ../badge.php?badge=empty");
        exit();
    } else {
        if (preg_match('/^image\\/p?jpeg$/i', $badge_type) or
            preg_match('/^image\\/gif$/i', $badge_type) or
            preg_match('/^image\\/(x-)?png$/i', $badge_type) and
            move_uploaded_file($badge_tmp, "../" . $filepath)) {
            $sql = "UPDATE badge SET badge = '$filepath', date_updated = now(), user_updated = 4 WHERE id = '$selected_badge'";
            mysqli_query($conn, $sql);
            header("Location: ../badge.php?badge=success");
            exit();
        }
    }
}

function getAllBadge($conn) {
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

function getBadge($conn, $name) {
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
