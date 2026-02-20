<?php
session_start();
include 'db.php';
$user_id = $_SESSION['user_id'];
$subject = $_GET['subject'];

$res = mysqli_query($conn, "SELECT paper_year, paper_type, question_no FROM question_notes WHERE user_id='$user_id' AND subject='$subject' AND note != ''");
$data = [];
while($row = mysqli_fetch_assoc($res)) { $data[] = $row; }
echo json_encode($data);
?>