<?php
session_start();
include 'db.php';

if (isset($_POST['note_id']) && isset($_POST['room_id'])) {
    $note_id = (int)$_POST['note_id'];
    $room_id = (int)$_POST['room_id'];
    $sender_id = $_SESSION['user_id'];

    $res = mysqli_query($conn, "SELECT subject, paper_year FROM question_notes WHERE id = '$note_id'");
    $note = mysqli_fetch_assoc($res);

    if ($note) {
        $title = mysqli_real_escape_string($conn, $note['subject'] . " (" . $note['paper_year'] . ")");
        // Special tag එකක් එක්ක message එක database දානවා
        $msg = "[NOTE_SHARE:" . $note_id . "] " . $title;
        
        if(mysqli_query($conn, "INSERT INTO study_group_messages (room_id, user_id, message) VALUES ('$room_id', '$sender_id', '$msg')")) {
            echo "success";
        }
    }
}
?>