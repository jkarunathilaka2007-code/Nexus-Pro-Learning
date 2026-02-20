<?php
session_start();
include 'db.php';

if (isset($_GET['id']) && isset($_SESSION['user_id'])) {
    $room_id = (int)$_GET['id'];
    $user_id = $_SESSION['user_id'];

    // 1. ඔයාව Room Members ලැයිස්තුවෙන් අයින් කරනවා
    mysqli_query($conn, "DELETE FROM room_members WHERE room_id = '$room_id' AND user_id = '$user_id'");

    // 2. Room එකේ තව කවුරුහරි ඉන්නවද කියලා බලනවා
    $check_members = mysqli_query($conn, "SELECT id FROM room_members WHERE room_id = '$room_id'");

    if (mysqli_num_rows($check_members) == 0) {
        // 3. Room එක හිස් නම්, ඒකට අදාළ Messages සහ Room එක මකනවා
        mysqli_query($conn, "DELETE FROM study_group_messages WHERE room_id = '$room_id'");
        mysqli_query($conn, "DELETE FROM study_rooms WHERE id = '$room_id'");
    }

    header("Location: study_lobby.php");
    exit();
}
?>