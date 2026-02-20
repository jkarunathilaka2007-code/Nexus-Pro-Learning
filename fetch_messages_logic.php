<?php
session_start();
include 'db.php';

// 1. AUTH CHECK
if (!isset($_SESSION['user_id'])) { 
    exit("Access Denied"); 
}

$user_id = $_SESSION['user_id'];

// 2. GET TARGET USER ID
// JS එකෙන් GET හෝ POST දෙකෙන්ම එවන දත්ත ගන්න 'chat_with' පාවිච්චි කරනවා
$chat_with = isset($_REQUEST['chat_with']) ? (int)$_REQUEST['chat_with'] : 0;

if ($chat_with > 0) {
    // 3. FETCH MESSAGES
    $query = "SELECT * FROM messages 
              WHERE (sender_id = '$user_id' AND receiver_id = '$chat_with') 
              OR (sender_id = '$chat_with' AND receiver_id = '$user_id') 
              ORDER BY created_at ASC";
    
    $msgs = mysqli_query($conn, $query);

    if(mysqli_num_rows($msgs) > 0) {
        while($m = mysqli_fetch_assoc($msgs)) {
            $type = ($m['sender_id'] == $user_id) ? 'sent' : 'received';
            $class = ($type == 'sent') ? 'msg-sent' : 'msg-received';
            $time = date('H:i', strtotime($m['created_at']));
            
            echo '<div class="msg-bubble '.$class.' shadow-sm">';
            echo htmlspecialchars($m['message']);
            echo '<div class="text-white-50 mt-1" style="font-size: 8px; text-align: right;">'.$time.'</div>';
            echo '</div>';
        }
    } else {
        echo '<div class="text-center text-white-50 mt-5 small">
                <i class="fa fa-hand-wave d-block mb-2 opacity-25" style="font-size: 30px;"></i>
                No messages yet. Say Hi!
              </div>';
    }

    // 4. CHECK IF THE OTHER PERSON IS TYPING
    // අනිත් කෙනාගේ typing_to column එකේ ඉන්නේ මගේ ID එක නම් "Typing..." කියලා පෙන්වනවා.
    $check_typing = mysqli_query($conn, "SELECT id FROM users WHERE id = '$chat_with' AND typing_to = '$user_id'");
    
    if(mysqli_num_rows($check_typing) > 0) {
        echo '<div class="msg-bubble msg-received opacity-75" style="border-radius: 15px 15px 15px 4px; padding: 5px 15px; margin-top: 5px;">
                <small class="text-primary fw-bold" style="font-size: 10px;">
                    <i class="fa fa-pencil fa-fade me-1"></i> Typing...
                </small>
              </div>';
    }
}
?>