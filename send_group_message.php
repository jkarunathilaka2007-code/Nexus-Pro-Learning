<?php
include 'db.php';
session_start();

if(isset($_POST['message'])) {
    $user_id = $_SESSION['user_id'];
    $room_id = (int)$_POST['room_id'];
    $msg = mysqli_real_escape_string($conn, $_POST['message']);
    
    mysqli_query($conn, "INSERT INTO study_group_messages (room_id, user_id, message) VALUES ('$room_id', '$user_id', '$msg')");
}
?>