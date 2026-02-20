<?php
session_start();
include 'db.php';

if (isset($_POST['subject'])) {
    $user_id = $_SESSION['user_id'];
    $subject = mysqli_real_escape_string($conn, $_POST['subject']);
    $total = (int)$_POST['total'];
    $correct = (int)$_POST['correct'];
    $time = (int)$_POST['time'];

    $sql = "INSERT INTO exam_results (user_id, subject_name, total_questions, correct_answers, time_taken_seconds) 
            VALUES ('$user_id', '$subject', '$total', '$correct', '$time')";
    
    mysqli_query($conn, $sql);
}
?>