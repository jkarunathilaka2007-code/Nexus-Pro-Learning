<?php
session_start();
include 'db.php';
date_default_timezone_set("Asia/Colombo");

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $subject = mysqli_real_escape_string($conn, $_POST['subject']);
    $duration = mysqli_real_escape_string($conn, $_POST['duration']); // "00:05:30" වගේ format එකක් එන්නේ

    // කාලය තත්පර වලට හරවමු start_time එක calculate කරන්න
    $parts = explode(':', $duration);
    $seconds = ($parts[0] * 3600) + ($parts[1] * 60) + $parts[2];

    // දැනට තියෙන වෙලාව end_time ලෙස ගනිමු
    $end_time = date('Y-m-d H:i:s');
    // දැනට තියෙන වෙලාවෙන් duration එක අඩු කරලා start_time එක ගමු
    $start_time = date('Y-m-d H:i:s', time() - $seconds);

    // Database එකට insert කිරීම
    $query = "INSERT INTO study_sessions (user_id, subject, start_time, end_time, duration) 
              VALUES ('$user_id', '$subject', '$start_time', '$end_time', '$duration')";
    
    if (mysqli_query($conn, $query)) {
        echo "Success";
    } else {
        http_response_code(500);
        echo "Error: " . mysqli_error($conn);
    }
}
?>