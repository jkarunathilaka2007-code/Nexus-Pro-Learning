<?php
/**
 * DB.PHP - Core Configuration & Logic
 * Nexus Pro Revision System (Goal & Permanent Skip Logic)
 */
date_default_timezone_set('Asia/Colombo');

// 1. Database Connection
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'exam_db'; // ඔබගේ Database නම නිවැරදිදැයි බලන්න


$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    die("Database Connection failed: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");

/**
 * FUNCTION: getDailyStatus
 * අද දිනට නියමිත සියලුම විෂයන් සඳහා ප්‍රශ්න ඇතුළත් කර ඇත්දැයි පරීක්ෂා කරයි.
 * මෙහිදී 'skipped_subjects' හි ඇති විෂයන් දිනපතා "Done" ලෙස ස්වයංක්‍රීයව සලකනු ලැබේ.
 */
function getDailyStatus($conn, $user_id) {
    // A. පරිශීලකයාගේ සියලුම විෂයන් සහ Skip කිරීමට තෝරාගත් විෂයන් ලබා ගැනීම
    $user_query = mysqli_query($conn, "SELECT subjects, skipped_subjects FROM users WHERE id = '$user_id'");
    $user_data = mysqli_fetch_assoc($user_query);
    
    // විෂයන් Array එකක් බවට පත් කිරීම
    $total_subjects = !empty($user_data['subjects']) ? array_map('trim', explode(',', $user_data['subjects'])) : [];
    
    // Skip කිරීමට (ප්‍රශ්න අවශ්‍ය නැතැයි) තෝරාගත් විෂයන් Array එකක් බවට පත් කිරීම
    $skipped_list = !empty($user_data['skipped_subjects']) ? array_map('trim', explode(',', $user_data['skipped_subjects'])) : [];

    // B. අද දවසේ (Today) අලුතින් ප්‍රශ්න ඇතුළත් කර ඇති විෂයන් බැලීම (user_revisions table)
    $today = date('Y-m-d');
    $upload_res = mysqli_query($conn, "SELECT DISTINCT subject_name FROM user_revisions 
                                      WHERE user_id = '$user_id' 
                                      AND DATE(created_at) = '$today'");
    
    $uploaded_today = [];
    if ($upload_res) {
        while ($row = mysqli_fetch_assoc($upload_res)) {
            $uploaded_today[] = $row['subject_name'];
        }
    }

    // C. සම්පූර්ණ වූ ලැයිස්තුව (අද ඇතුළත් කළ + ස්ථිරවම Skip කළ විෂයන්)
    // මේ ලැයිස්තු දෙකම එකතු කර අනුපිටපත් ඉවත් කරයි.
    $final_done_list = array_unique(array_merge($uploaded_today, $skipped_list));

    // D. තවමත් සම්පූර්ණ නොවූ විෂයන් සෙවීම
    $pending = array_diff($total_subjects, $final_done_list);

    // E. සියලු විස්තර ආපසු ලබා දීම
    return [
        'is_complete'      => empty($pending),           // සියල්ල අවසන් නම් true
        'pending_subjects' => array_values($pending),    // තවමත් කළ යුතු විෂයන්
        'done_list'        => $final_done_list,         // අවසන් වූ සියලුම විෂයන් (Uploaded + Skipped)
        'skipped_list'     => $skipped_list,            // පරිශීලකයා "ප්‍රශ්න අවශ්‍ය නැත" කී විෂයන්
        'total_count'      => count($total_subjects),    // මුළු විෂයන් ගණන
        'done_count'       => count(array_intersect($total_subjects, $final_done_list)), // මුළු විෂයන් අතරින් අවසන් කළ ගණන
        'percentage'       => (count($total_subjects) > 0) ? round((count(array_intersect($total_subjects, $final_done_list)) / count($total_subjects)) * 100) : 0
    ];
}

// Global Timezone සැකසීම
date_default_timezone_set('Asia/Colombo');

?>