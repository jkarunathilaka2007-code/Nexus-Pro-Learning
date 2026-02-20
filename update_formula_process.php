<?php
session_start();
include 'db.php';

// User login වෙලාද බලමු
if (!isset($_SESSION['user_id'])) { 
    die("Unauthorized access"); 
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    
    // POST එකෙන් එන දත්ත clean කරගමු
    $id = mysqli_real_escape_string($conn, $_POST['id']);
    $subject = mysqli_real_escape_string($conn, $_POST['subject']);
    $lesson = mysqli_real_escape_string($conn, $_POST['lesson']);
    $topic = mysqli_real_escape_string($conn, $_POST['topic']);
    $subtopic = mysqli_real_escape_string($conn, $_POST['subtopic']);
    $formula = mysqli_real_escape_string($conn, $_POST['formula']);

    // --- වැදගත්ම කොටස: SQL Query එක ---
    // ඔයාගේ table එකේ column names: subject_name, lesson_name, topic, sub_topic, formula_text
    $sql = "UPDATE user_formulas SET 
            subject_name = '$subject', 
            lesson_name = '$lesson', 
            topic = '$topic', 
            sub_topic = '$subtopic', 
            formula_text = '$formula' 
            WHERE id = '$id' AND user_id = '$user_id'";

    if (mysqli_query($conn, $sql)) {
        echo "Success"; // JS එකට Success කියලා යවනවා
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>