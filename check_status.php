<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];

// දැනට තියෙන balance එක
$user_query = $conn->query("SELECT total_coins FROM users WHERE id = $user_id");
$user = $user_query->fetch_assoc();

// අද දවසට 'free' (abort_coin = 0) spin එකක් කරලාද කියලා විතරක් බලන්න
// DATE(date) පාවිච්චි කරන්නේ කාලය (Time) නැතුව දිනය විතරක් සසඳන්න
$today = date('Y-m-d');
$free_check = $conn->query("SELECT id FROM coin_transactions 
                            WHERE user_id = $user_id 
                            AND DATE(date) = '$today' 
                            AND type = 'spin' 
                            AND abort_coin = 0");

echo json_encode([
    'total_coins' => (int)$user['total_coins'],
    'free_spin' => ($free_check->num_rows == 0) // record නැත්නම් true (can spin free)
]);
?>