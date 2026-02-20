<?php
session_start();
include 'db.php';
$user_id = $_SESSION['user_id'];
$target = (int)$_POST['target_id'];
$status = (int)$_POST['status']; // 1 නම් ටයිප් කරනවා, 0 නම් නැහැ

if($status == 1) {
    mysqli_query($conn, "UPDATE users SET typing_to = '$target' WHERE id = '$user_id'");
} else {
    mysqli_query($conn, "UPDATE users SET typing_to = 0 WHERE id = '$user_id'");
}
?>