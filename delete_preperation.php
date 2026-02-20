<?php
session_start();
include 'db.php';

// පරිශීලකයා Login වී ඇත්ද සහ Subject එක ලැබී ඇත්දැයි පරීක්ෂා කිරීම
if (!isset($_SESSION['user_id']) || !isset($_GET['subject'])) {
    header("Location: add_subject_preparation.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$subject = mysqli_real_escape_string($conn, $_GET['subject']);

/**
 * 1. completed_questions table එකෙන් දත්ත මැකීම
 * Columns: user_id, subject
 */
mysqli_query($conn, "DELETE FROM completed_questions WHERE user_id = '$user_id' AND subject = '$subject'");

/**
 * 2. study_sessions table එකෙන් දත්ත මැකීම
 * Columns: user_id, subject
 */
mysqli_query($conn, "DELETE FROM study_sessions WHERE user_id = '$user_id' AND subject = '$subject'");

/**
 * 3. question_notes table එකෙන් දත්ත මැකීම
 * Columns: user_id, subject
 */
mysqli_query($conn, "DELETE FROM question_notes WHERE user_id = '$user_id' AND subject = '$subject'");

/**
 * 4. user_revisions table එකෙන් දත්ත මැකීම
 * සටහන: මෙහි column එක 'subject_name' ලෙස භාවිතා වේ.
 */
mysqli_query($conn, "DELETE FROM user_revisions WHERE user_id = '$user_id' AND subject_name = '$subject'");

/**
 * 5. trackers table එකෙන් ප්‍රධාන විෂය වාර්තාව මැකීම
 * Columns: user_id, subject
 */
$sql_main = "DELETE FROM trackers WHERE user_id = '$user_id' AND subject = '$subject'";

if (mysqli_query($conn, $sql_main)) {
    // සාර්ථකව මැකුණු පසු පණිවිඩයක් සමඟ redirect කිරීම
    header("Location: add_subject_preparation.php?msg=deleted");
    exit();
} else {
    // දෝෂයක් ඇත්නම් එය පෙන්වීම
    echo "Error deleting subject: " . mysqli_error($conn);
}
?>