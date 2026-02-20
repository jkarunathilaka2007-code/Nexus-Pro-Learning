<?php
session_start();
include 'db.php';

if (isset($_POST['note_id'])) {
    $note_id = (int)$_POST['note_id'];
    $me = $_SESSION['user_id'];

    // මුල් නෝට් එකේ දත්ත ගැනීම
    $res = mysqli_query($conn, "SELECT * FROM question_notes WHERE id = '$note_id'");
    $n = mysqli_fetch_assoc($res);

    if ($n) {
        $subject = mysqli_real_escape_string($conn, $n['subject']);
        $py = mysqli_real_escape_string($conn, $n['paper_year']);
        $pt = mysqli_real_escape_string($conn, $n['paper_type']);
        $qn = mysqli_real_escape_string($conn, $n['question_no']);
        $note = mysqli_real_escape_string($conn, $n['note']);
        $img = mysqli_real_escape_string($conn, $n['image_path']);

        // INSERT එකේදී 'id' එක ඇතුළත් කරන්නේ නැත (එය Auto හැදේ)
        $sql = "INSERT INTO question_notes 
                (user_id, subject, paper_year, paper_type, question_no, note, image_path, is_favorite, revision_count) 
                VALUES 
                ('$me', '$subject', '$py', '$pt', '$qn', '$note', '$img', 0, 0)";
        
        if(mysqli_query($conn, $sql)) {
            echo "Note added to your collection successfully!";
        } else {
            echo "Database Error: " . mysqli_error($conn);
        }
    } else {
        echo "Note not found.";
    }
}
?>