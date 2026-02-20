<?php
session_start();
include 'db.php';
$user_id = $_SESSION['user_id'];

// Create Room
if(isset($_POST['create_room'])){
    $name = $_POST['room_name'];
    $max = $_POST['max_members'];
    $code = $_POST['ref_code'];

    $sql = "INSERT INTO battle_rooms (room_name, max_members, referral_code, admin_id) VALUES ('$name', '$max', '$code', '$user_id')";
    if(mysqli_query($conn, $sql)){
        $room_id = mysqli_insert_id($conn);
        mysqli_query($conn, "UPDATE users SET current_room_id = '$room_id' WHERE id = '$user_id'");
        header("Location: room.php");
    }
}

// Join Room
if(isset($_POST['join_room'])){
    $name = $_POST['room_name_join'];
    $code = $_POST['ref_code_join'];

    $res = mysqli_query($conn, "SELECT id FROM battle_rooms WHERE room_name = '$name' AND referral_code = '$code' AND status = 'waiting'");
    if($row = mysqli_fetch_assoc($res)){
        $room_id = $row['id'];
        mysqli_query($conn, "UPDATE users SET current_room_id = '$room_id' WHERE id = '$user_id'");
        header("Location: room.php");
    }
}

// Exit Room
if(isset($_GET['exit'])){
    $res = mysqli_query($conn, "SELECT * FROM battle_rooms WHERE admin_id = '$user_id'");
    if(mysqli_num_rows($res) > 0){
        // Admin නම් - Room එකම අයින් කර සියලු දෙනා එලියට දමන්න
        $room = mysqli_fetch_assoc($res);
        $rid = $room['id'];
        mysqli_query($conn, "UPDATE users SET current_room_id = NULL WHERE current_room_id = '$rid'");
        mysqli_query($conn, "DELETE FROM battle_rooms WHERE id = '$rid'");
    } else {
        // User නම් - තමා පමණක් එලියට යන්න
        mysqli_query($conn, "UPDATE users SET current_room_id = NULL WHERE id = '$user_id'");
    }
    header("Location: battle.php");
}
?>