<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$room_id = isset($_SESSION['last_room_id']) ? $_SESSION['last_room_id'] : 0;

if ($room_id != 0) {
    // 1. මේ Room එක හැදුවේ දැනට Exit වෙන්න හදන කෙනාද (Host ද) කියා පරීක්ෂා කිරීම
    $room_query = mysqli_query($conn, "SELECT host_id FROM rooms WHERE id = '$room_id'");
    $room_data = mysqli_fetch_assoc($room_query);

    if ($room_data && $room_data['host_id'] == $user_id) {
        // --- මෙතනින් පල්ලෙහාට වෙන්නේ Host Exit වෙද්දී සිදුවන දේ ---
        
        // Host ගේ current_room_id එක NULL කිරීම
        mysqli_query($conn, "UPDATE users SET current_room_id = NULL WHERE id = '$user_id'");

        // Room එකට අදාළ Questions මැකීම
        mysqli_query($conn, "DELETE FROM room_questions WHERE room_id = '$room_id'");

        // Room එකට අදාළ Results මැකීම 
        // (සටහන: සහතික පසුවටත් පෙන්වන්න ඕන නම් මේ පේළිය අයින් කරන්න)
        mysqli_query($conn, "DELETE FROM room_results WHERE room_id = '$room_id'");

        // Room එක සම්පූර්ණයෙන්ම Database එකෙන් මැකීම
        mysqli_query($conn, "DELETE FROM rooms WHERE id = '$room_id'");

        // අනෙක් සියලුම Players ලාවත් මේ රූම් එකෙන් නිදහස් කිරීම
        mysqli_query($conn, "UPDATE users SET current_room_id = NULL WHERE current_room_id = '$room_id'");

    } else {
        // --- මෙතනින් පල්ලෙහාට වෙන්නේ සාමාන්‍ය Player කෙනෙක් Exit වෙද්දී සිදුවන දේ ---
        
        // Player ගේ current_room_id එක පමණක් NULL කිරීම
        mysqli_query($conn, "UPDATE users SET current_room_id = NULL WHERE id = '$user_id'");
    }

    // Session දත්ත ඉවත් කිරීම
    unset($_SESSION['last_room_id']);
}

header("Location: dashboard.php");
exit();
?>