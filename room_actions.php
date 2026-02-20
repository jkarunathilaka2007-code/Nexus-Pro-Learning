<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include 'db.php';

// සිංහල අකුරු ප්‍රශ්නය ඇති නොවීමට encoding සැකසීම
mysqli_set_charset($conn, "utf8mb4");

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['kick' => true]);
    exit();
}

$user_id = $_SESSION['user_id'];
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

// 1. පරිශීලකයා සිටින Room ID එක ලබා ගැනීම
$u_res = mysqli_query($conn, "SELECT current_room_id FROM users WHERE id = '$user_id'");
$u_data = mysqli_fetch_assoc($u_res);
$room_id = $u_data['current_room_id'];

// Room එකක නැතිනම් kick කිරීමේ සංඥාව යැවීම
if (!$room_id && $action == 'get_stats') {
    echo json_encode(['kick' => true]);
    exit();
}

// --- ක්‍රියාව 1: ප්‍රශ්න ගණන තීරණය කිරීම (Host/Admin සඳහා) ---
if ($action == 'set_count') {
    $count = (int)$_POST['count'];
    mysqli_query($conn, "UPDATE battle_rooms SET total_questions = '$count' WHERE id = '$room_id'");
    echo "success";
    exit();
}

// --- ක්‍රියාව 2: ප්‍රශ්නයක් සහ පිළිතුරු Database එකට එකතු කිරීම ---
if ($action == 'save_q') {
    $q_text = mysqli_real_escape_string($conn, $_POST['question_text']);
    $correct_idx = (int)$_POST['correct_ans'];
    $options = $_POST['options']; // පිළිතුරු Array එක

    // තෝරාගත් නිවැරදි පිළිතුරේ TEXT එක ලබා ගැනීම
    $correct_answer_text = mysqli_real_escape_string($conn, $options[$correct_idx]);

    // ප්‍රශ්නය ඇතුළත් කිරීම (ඔබේ table columns: room_id, user_id, question_text, correct_answer)
    $insert_q = "INSERT INTO room_questions (room_id, user_id, question_text, correct_answer) 
                 VALUES ('$room_id', '$user_id', '$q_text', '$correct_answer_text')";
    
    if (mysqli_query($conn, $insert_q)) {
        $q_id = mysqli_insert_id($conn);

        // පිළිතුරු ටික room_question_options table එකට ඇතුළත් කිරීම
        foreach ($options as $index => $opt) {
            $is_correct = ($index === $correct_idx) ? 1 : 0;
            $opt_text = mysqli_real_escape_string($conn, $opt);
            
            mysqli_query($conn, "INSERT INTO room_question_options (question_id, option_text, is_correct) 
                                 VALUES ('$q_id', '$opt_text', '$is_correct')");
        }
        echo "success";
    } else {
        // SQL වැරැද්දක් ඇත්නම් හඳුනා ගැනීමට (Local testing සඳහා පමණක් මෙතන error එක echo කරන්න පුළුවන්)
        // echo mysqli_error($conn);
    }
    exit();
}

// --- ක්‍රියාව 3: Real-time Status සහ සාමාජික ලැයිස්තුව ලබා ගැනීම ---
if ($action == 'get_stats') {
    // Room එකේ මුළු ප්‍රශ්න ඉලක්කය
    $r_res = mysqli_query($conn, "SELECT total_questions FROM battle_rooms WHERE id = '$room_id'");
    $room = mysqli_fetch_assoc($r_res);
    
    if (!$room) {
        echo json_encode(['kick' => true]);
        exit();
    }

    // දැනට සාදා ඇති ප්‍රශ්න ගණන
    $q_res = mysqli_query($conn, "SELECT COUNT(*) as count FROM room_questions WHERE room_id = '$room_id'");
    $q_data = mysqli_fetch_assoc($q_res);
    
    // සාමාජික ලැයිස්තුව (Profile image එක සමඟ)
    $m_res = mysqli_query($conn, "SELECT name, profile_image FROM users WHERE current_room_id = '$room_id'");
    $members_html = "";
    $count_members = 0;

    while ($m = mysqli_fetch_assoc($m_res)) {
        $count_members++;
        $img = !empty($m['profile_image']) ? $m['profile_image'] : 'https://cdn-icons-png.flaticon.com/512/149/149071.png';
        
        $members_html .= "
        <div class='member-item d-flex align-items-center mb-3 p-2'>
            <div class='member-avatar-wrapper'>
                <img src='$img' class='member-avatar' style='width: 45px; height: 45px; border-radius: 12px; border: 2px solid #0075ff; object-fit: cover;'>
                <div class='online-status' style='width: 12px; height: 12px; background: #2dce89; border: 2px solid #030518; border-radius: 50%; position: absolute; bottom: 0; right: 0;'></div>
            </div>
            <div class='ms-3'>
                <div class='member-name text-white' style='font-weight: 700; font-size: 0.95rem;'>".htmlspecialchars($m['name'])."</div>
                <div class='member-role text-muted small' style='font-size: 10px;'>ACTIVE PARTICIPANT</div>
            </div>
        </div>";
    }

    echo json_encode([
        'total' => (int)$room['total_questions'],
        'created' => (int)$q_data['count'],
        'remaining' => max(0, (int)$room['total_questions'] - (int)$q_data['count']),
        'members' => $members_html,
        'member_count' => $count_members
    ]);
    exit();
}
?>