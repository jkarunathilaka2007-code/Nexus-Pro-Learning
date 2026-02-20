function getDailyStatus($conn, $user_id) {
    // 1. ඔක්කොම විෂයන් ලැයිස්තුව ගන්න
    $subjects_res = mysqli_query($conn, "SELECT DISTINCT subject_name FROM user_revisions WHERE user_id = '$user_id'");
    $total_subjects = [];
    while($row = mysqli_fetch_assoc($subjects_res)) {
        $total_subjects[] = $row['subject_name'];
    }

    // 2. අද දවසේ කරපු විෂයන් ලැයිස්තුව ගන්න (question_results table එකෙන්)
    $today = date('Y-m-d');
    $done_res = mysqli_query($conn, "SELECT DISTINCT subject_name FROM question_results 
                                    WHERE user_id = '$user_id' 
                                    AND DATE(attempted_at) = '$today'");
    $done_subjects = [];
    while($row = mysqli_fetch_assoc($done_res)) {
        $done_subjects[] = $row['subject_name'];
    }

    // 3. ඉතිරි වෙලා තියෙන විෂයන් (Pending Subjects)
    $pending = array_diff($total_subjects, $done_subjects);

    return [
        'is_complete' => empty($pending),
        'pending_subjects' => $pending,
        'total_count' => count($total_subjects),
        'done_count' => count($done_subjects)
    ];
}