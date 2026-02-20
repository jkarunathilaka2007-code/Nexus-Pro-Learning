<?php
session_start();
include 'db.php';

// JSON request එකක් ආවොත් ඒක කියවන්න (AJAX සඳහා)
$data = json_decode(file_get_contents('php://input'), true);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
    
    $user_id = $_SESSION['user_id'];
    
    // Form data හෝ JSON data දෙකම handle කිරීමට
    $subject = mysqli_real_escape_string($conn, $_POST['subject'] ?? $data['subject']);
    $type = mysqli_real_escape_string($conn, $_POST['type'] ?? $data['type']);
    $year = (int)($_POST['year'] ?? $data['year']);
    $q_no = (int)($_POST['q_no'] ?? $data['q_no']);
    $action = $_POST['action'] ?? $data['action']; 
    $is_hard = (isset($_POST['is_hard']) || isset($data['is_hard'])) ? (int)($_POST['is_hard'] ?? $data['is_hard']) : 0;

    if ($action === 'add') {
        $sql = "INSERT INTO completed_questions (user_id, subject, paper_type, paper_year, question_no, is_hard) 
                VALUES ('$user_id', '$subject', '$type', '$year', '$q_no', '$is_hard')
                ON DUPLICATE KEY UPDATE is_hard = '$is_hard'";
    } else {
        $sql = "DELETE FROM completed_questions 
                WHERE user_id = '$user_id' 
                AND subject = '$subject' 
                AND paper_type = '$type' 
                AND paper_year = '$year' 
                AND question_no = '$q_no'";
    }

    if (mysqli_query($conn, $sql)) {
        
        // --- STREAK LOGIC ---
        $today = date('Y-m-d');
        $user_res = mysqli_query($conn, "SELECT last_activity, streak_count FROM users WHERE id = '$user_id'");
        $user_data = mysqli_fetch_assoc($user_res);

        if ($user_data) {
            $last_active = $user_data['last_activity'];
            $current_streak = $user_data['streak_count'];

            if ($last_active != $today) {
                $yesterday = date('Y-m-d', strtotime("-1 day"));
                $new_streak = ($last_active == $yesterday) ? $current_streak + 1 : 1;
                mysqli_query($conn, "UPDATE users SET streak_count = '$new_streak', last_activity = '$today' WHERE id = '$user_id'");
            }
        }
        echo "success";
    } else {
        echo "error";
    }
} else {
    echo "unauthorized";
}
?>