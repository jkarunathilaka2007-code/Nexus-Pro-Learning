<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    
    // input field වල නම් timer.php එකේ තියෙන විදිහටම ගන්න (goal_h සහ goal_m)
    $hours = isset($_POST['goal_h']) ? (int)$_POST['goal_h'] : 0;
    $minutes = isset($_POST['goal_m']) ? (int)$_POST['goal_m'] : 0;
    
    // මුළු තත්පර ගණන ගණනය කිරීම
    $total_goal_seconds = ($hours * 3600) + ($minutes * 60);

    // Focus සහ Interval කාලයන් (විනාඩි වලින් එන්නේ)
    $focus = isset($_POST['focus']) ? (int)$_POST['focus'] : 25;
    $interval = isset($_POST['interval']) ? (int)$_POST['interval'] : 5;

    // තත්පර ගණන 0 ට වඩා වැඩි නම් පමණක් insert කරන්න
    if ($total_goal_seconds > 0) {
        $stmt = $conn->prepare("INSERT INTO goals (user_id, goal_duration_seconds, focus_period, interval_period, done) VALUES (?, ?, ?, ?, 0)");
        $stmt->bind_param("iiii", $user_id, $total_goal_seconds, $focus, $interval);
        
        if ($stmt->execute()) {
            $goal_id = $stmt->insert_id;
            // සාර්ථකව insert වුණාම timer එකට යවනවා
            header("Location: start_timer.php?goal_id=$goal_id");
            exit();
        } else {
            echo "Error: " . $conn->error;
        }
    } else {
        // කාලය 0 නම් ආපහු dashboard එකට වැරැද්දක් පෙන්වන්න යවනවා
        header("Location: timer.php?error=zero_duration");
        exit();
    }
}
?>