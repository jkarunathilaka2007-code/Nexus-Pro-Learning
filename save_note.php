<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    die("Unauthorized");
}

$user_id = $_SESSION['user_id'];

// NULL නොවී අගයන් එන බව තහවුරු කරගැනීම
$subject = mysqli_real_escape_string($conn, $_POST['subject'] ?? '');
$year    = mysqli_real_escape_string($conn, $_POST['year'] ?? '');
$type    = mysqli_real_escape_string($conn, $_POST['type'] ?? '');
$note    = mysqli_real_escape_string($conn, $_POST['note'] ?? '');

// මෙන්න මෙතනයි වැදගත්ම දේ: 0 කියන්නේ 'empty' නෙවෙයි කියලා කියන්න ඕනේ
$q_no = isset($_POST['q_no']) ? (int)$_POST['q_no'] : 0;

if ($subject == '' || $year == '' || $type == '') {
    die("Data missing");
}

$image_path = null;
if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
    $target_dir = "uploads/notes/";
    if (!file_exists($target_dir)) mkdir($target_dir, 0777, true);
    $new_filename = time() . "_" . uniqid() . "." . pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
    $target_file = $target_dir . $new_filename;
    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        $image_path = $target_file;
    }
}

// 1. කලින් Record එකක් තියෙනවද බලමු (Strict check for q_no)
$check_sql = "SELECT id, image_path FROM question_notes 
              WHERE user_id = '$user_id' 
              AND subject = '$subject' 
              AND paper_year = '$year' 
              AND paper_type = '$type' 
              AND question_no = $q_no";

$result = mysqli_query($conn, $check_sql);

if ($result && mysqli_num_rows($result) > 0) {
    // 2. තිබේ නම් UPDATE
    $row = mysqli_fetch_assoc($result);
    $img_sql = ($image_path) ? ", image_path = '$image_path'" : "";
    
    $update_sql = "UPDATE question_notes SET 
                   note = '$note' 
                   $img_sql 
                   WHERE id = " . $row['id'];
    
    if (mysqli_query($conn, $update_sql)) {
        echo "SUCCESS_SAVED";
    } else {
        // SQL Error එක දැනගන්න මේක වැදගත්
        error_log("SQL Update Error: " . mysqli_error($conn));
    }
} else {
    // 3. නැත්නම් INSERT
    $insert_sql = "INSERT INTO question_notes (user_id, subject, paper_year, paper_type, question_no, note, image_path) 
                   VALUES ('$user_id', '$subject', '$year', '$type', $q_no, '$note', " . ($image_path ? "'$image_path'" : "NULL") . ")";
    
    if (mysqli_query($conn, $insert_sql)) {
        echo "SUCCESS_SAVED";
    } else {
        error_log("SQL Insert Error: " . mysqli_error($conn));
    }
}
mysqli_close($conn);
?>