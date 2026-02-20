<?php
session_start();
include 'db.php'; 

// User login වෙලා නැත්නම් වැඩේ කරන්න දෙන්න එපා
if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = (int)$_SESSION['user_id']; // Integer casting for security
    
    // FormData හරහා එන දත්ත ටික පිරිසිදු කරලා ගන්නවා
    $subject  = mysqli_real_escape_string($conn, $_POST['subject']);
    $lesson   = mysqli_real_escape_string($conn, $_POST['lesson']);
    $topic    = mysqli_real_escape_string($conn, $_POST['topic']); 
    $subtopic = mysqli_real_escape_string($conn, $_POST['subtopic']); 
    $formula  = mysqli_real_escape_string($conn, $_POST['formula']);

    // Lesson එක සහ Formula එක අනිවාර්යයෙන්ම තිබිය යුතුයි
    if (!empty($lesson) && !empty($formula)) {
        
        // SQL Query එකේ table/column names වලට backticks (`) පාවිච්චි කිරීම වඩාත් සුදුසුයි
        $sql = "INSERT INTO `user_formulas` (`user_id`, `subject_name`, `lesson_name`, `topic`, `sub_topic`, `formula_text`) 
                VALUES ('$user_id', '$subject', '$lesson', '$topic', '$subtopic', '$formula')";

        if (mysqli_query($conn, $sql)) {
            // JavaScript එක බලන් ඉන්නේ "success" (all lowercase) කියන වචනය එනකල්
            echo "success";
        } else {
            echo "Database Error: " . mysqli_error($conn);
        }
    } else {
        echo "Required fields are missing.";
    }
} else {
    echo "Invalid request method.";
}
?>