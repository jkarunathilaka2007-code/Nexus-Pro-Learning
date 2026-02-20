<?php
/**
 * SUBMIT_PAPER.PHP - Exam Submission & Logic
 * Nexus Pro Revision System
 */
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include 'db.php';

// සිංහල අකුරු ගැටලු මගහැරීමට
mysqli_set_charset($conn, "utf8mb4");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// 1. පරිශීලකයා සිටින Room ID එක ලබා ගැනීම
$u_res = mysqli_query($conn, "SELECT current_room_id FROM users WHERE id = '$user_id'");
$u_data = mysqli_fetch_assoc($u_res);
$room_id = $u_data['current_room_id'];

// Room එකක් නොමැති නම් Battle පිටුවට යවන්න
if (!$room_id) {
    header("Location: battle.php");
    exit();
}

$score = 0;
$total_q = 0;
$user_answers = [];

// 2. ලකුණු ගණනය කිරීමේ Logic එක
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST as $key => $selected_opt_id) {
        // Form එකෙන් එන q_123 වැනි input පමණක් තෝරා ගැනීම
        if (strpos($key, 'q_') === 0) {
            $total_q++;
            $q_id = str_replace('q_', '', $key);
            
            // පරිශීලකයා තේරූ පිළිතුර Save කරගැනීම (Review සඳහා)
            $user_answers[$q_id] = $selected_opt_id; 
            
            $selected_opt_id = mysqli_real_escape_string($conn, $selected_opt_id);
            
            // තේරූ පිළිතුර නිවැරදි දැයි පරීක්ෂා කිරීම
            $check = mysqli_query($conn, "SELECT is_correct FROM room_question_options WHERE id = '$selected_opt_id'");
            if ($check) {
                $ans = mysqli_fetch_assoc($check);
                if ($ans && $ans['is_correct'] == 1) { 
                    $score++; 
                }
            }
        }
    }
}

// Review එක පසුව පෙන්වීමට පිළිතුරු JSON බවට පත් කිරීම
$answers_json = mysqli_real_escape_string($conn, json_encode($user_answers));

// 3. Database එකට දත්ත ඇතුළත් කිරීම
// කලින් ප්‍රතිඵල තිබේ නම් ඒවා මකා අලුත් ඒවා ඇතුළත් කිරීම (Duplicate Entry වැළැක්වීමට)
mysqli_query($conn, "DELETE FROM room_results WHERE room_id = '$room_id' AND user_id = '$user_id'");

// INSERT query (මෙහි id column එක සඳහන් නොකරන්නේ එය Auto Increment නිසාය)
$sql = "INSERT INTO room_results (room_id, user_id, score, total_questions, answers_json) 
        VALUES ('$room_id', '$user_id', '$score', '$total_q', '$answers_json')";

if (mysqli_query($conn, $sql)) {
    // Result එක පෙන්වීමට Session එකක තබා ගැනීම
    $_SESSION['last_room_id'] = $room_id;
    
    // වැදගත්: විභාගය අවසන් වූ පසු පරිශීලකයා Room එකෙන් ඉවත් කිරීම
    mysqli_query($conn, "UPDATE users SET current_room_id = NULL WHERE id = '$user_id'");
    
    // සාර්ථක නම් Results පිටුවට යවන්න
    header("Location: results.php");
    exit();
} else {
    // මොකක් හරි වැරැද්දක් වුණොත් දැනගන්න
    die("Submission Error: " . mysqli_error($conn));
}
?>