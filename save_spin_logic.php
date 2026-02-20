<?php
session_start();
include 'db.php';
header('Content-Type: application/json');

$user_id = $_SESSION['user_id'];
$win = intval($_POST['amount']);
$cost = intval($_POST['cost']);

// Balance check for paid spins
if ($cost > 0) {
    $user = $conn->query("SELECT total_coins FROM users WHERE id = $user_id")->fetch_assoc();
    if ($user['total_coins'] < $cost) {
        echo json_encode(['status' => 'error', 'message' => 'Insufficient Balance']);
        exit;
    }
}

// 1. Record Transaction (received_coins = දිනපු ගාණ, abort_coin = වියදම් කරපු ගාණ)
$stmt = $conn->prepare("INSERT INTO coin_transactions (user_id, received_coins, abort_coin, type) VALUES (?, ?, ?, 'spin')");
$stmt->bind_param("iii", $user_id, $win, $cost);
$stmt->execute();

// 2. Update User Total Coins
$net = $win - $cost;
$conn->query("UPDATE users SET total_coins = total_coins + ($net) WHERE id = $user_id");

echo json_encode(['status' => 'success']);
?>