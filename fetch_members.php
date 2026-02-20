<?php
session_start();
include 'db.php';
$user_id = $_SESSION['user_id'];

$u_res = mysqli_query($conn, "SELECT current_room_id FROM users WHERE id = '$user_id'");
$u_data = mysqli_fetch_assoc($u_res);
$room_id = $u_data['current_room_id'];

if(!$room_id){ echo "kick"; exit(); }

$members = mysqli_query($conn, "SELECT name FROM users WHERE current_room_id = '$room_id'");
while($m = mysqli_fetch_assoc($members)){
    echo "<div class='member-card'>".$m['name']."</div>";
}
?>