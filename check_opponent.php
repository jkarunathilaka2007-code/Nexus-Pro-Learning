<?php
include 'db.php';
$code = $_GET['code'];
$res = mysqli_query($conn, "SELECT opponent_id FROM battles WHERE battle_code = '$code' AND status = 'active'");
$data = mysqli_fetch_assoc($res);

echo json_encode(['joined' => ($data && $data['opponent_id'] != null)]);
?>