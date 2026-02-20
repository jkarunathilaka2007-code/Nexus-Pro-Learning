<?php
session_start();
include 'db.php';
$user_id = $_SESSION['user_id'];
$receiver_id = (int)$_POST['receiver_id'];
$message = mysqli_real_escape_string($conn, $_POST['message']);

if(!empty($message)) {
    mysqli_query($conn, "INSERT INTO messages (sender_id, receiver_id, message) VALUES ('$user_id', '$receiver_id', '$message')");
}
?>