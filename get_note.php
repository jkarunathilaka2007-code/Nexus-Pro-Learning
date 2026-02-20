<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    die("");
}

$user_id = $_SESSION['user_id'];
$subject = mysqli_real_escape_string($conn, $_GET['subject']);
$year    = mysqli_real_escape_string($conn, $_GET['year']);
$type    = mysqli_real_escape_string($conn, $_GET['type']);
$q_no    = isset($_GET['q_no']) ? (int)$_GET['q_no'] : 0;

$sql = "SELECT note FROM question_notes 
        WHERE user_id = '$user_id' 
        AND subject = '$subject' 
        AND paper_year = '$year' 
        AND paper_type = '$type' 
        AND question_no = $q_no 
        LIMIT 1";

$result = mysqli_query($conn, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    echo $row['note'];
} else {
    echo ""; // මොකුත් නැත්නම් හිස්ව යවන්න
}
?>