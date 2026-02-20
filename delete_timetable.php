<?php
session_start();
include 'db.php';

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    mysqli_query($conn, "DELETE FROM timetables WHERE user_id = '$user_id'");
}

header("Location: shedule.php");
exit();
?>