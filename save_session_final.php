<?php
session_start();
include 'db.php';

if (isset($_POST['q_ids'])) {
    $user_id = $_SESSION['user_id'];
    $subject = mysqli_real_escape_string($conn, $_POST['subject']);
    $correct_total = (int)$_POST['correct'];
    $total_qs = (int)$_POST['total'];
    $time = (int)$_POST['time'];
    
    $q_ids = json_decode($_POST['q_ids']);
    $user_choices = json_decode($_POST['user_choices'], true);
    $correct_indices = json_decode($_POST['correct_indices'], true);

    // 1. Overall Summary එක සේව් කිරීම
    $res_sql = "INSERT INTO exam_results (user_id, subject_name, total_questions, correct_answers, time_taken_seconds) 
                VALUES ('$user_id', '$subject', '$total_qs', '$correct_total', '$time')";
    mysqli_query($conn, $res_sql);

    // 2. එක් එක් ප්‍රශ්නයට අදාළ record එක සේව් කිරීම සහ Attempt Count වැඩි කිරීම
    foreach ($q_ids as $index => $q_id) {
        $q_id = (int)$q_id;
        $u_ans = mysqli_real_escape_string($conn, $user_choices[$index]);
        $c_idx = $correct_indices[$index];
        
        // MCQ එකක් නම් හරි වැරදි බැලීම
        $is_correct = ($u_ans == $c_idx && $u_ans !== 'N/A') ? 1 : 0;

        // Detail table එකට සේව් කිරීම (Table එක CREATE කරලා තිබිය යුතුයි)
        $detail_sql = "INSERT INTO question_results (user_id, question_id, subject_name, user_answer, is_correct) 
                       VALUES ('$user_id', '$q_id', '$subject', '$u_ans', '$is_correct')";
        mysqli_query($conn, $detail_sql);

        // Attempt count එක වැඩි කිරීම (Smart sorting සඳහා)
        mysqli_query($conn, "UPDATE user_revisions SET attempt_count = attempt_count + 1 WHERE id = $q_id");
    }
    echo "Saved Successfully";
}
?>