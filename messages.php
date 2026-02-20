<?php
session_start();
include 'db.php';

// 1. AUTH CHECK
if (!isset($_SESSION['user_id'])) { 
    header("Location: login.php"); 
    exit(); 
}

$user_id = $_SESSION['user_id'];
$chat_with = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// 2. MARK AS READ LOGIC
if ($chat_with > 0) {
    mysqli_query($conn, "UPDATE messages SET is_read = 1 WHERE sender_id = '$chat_with' AND receiver_id = '$user_id' AND is_read = 0");
}

$user_q = mysqli_query($conn, "SELECT * FROM users WHERE id = '$user_id'");
$user_data = mysqli_fetch_assoc($user_q);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nexus | Messenger Pro</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        :root { --bg: #020410; --accent: #0075ff; --glass: rgba(17, 25, 40, 0.9); }
        body { background: var(--bg); color: #fff; font-family: 'Plus Jakarta Sans', sans-serif; height: 100vh; overflow: hidden; margin: 0; }
        
        .messenger-wrapper { height: 100vh; display: flex; overflow: hidden; }

        /* Sidebar */
        .chat-sidebar { width: 350px; background: rgba(6, 11, 38, 0.95); border-right: 1px solid rgba(255,255,255,0.1); display: flex; flex-direction: column; }
        .user-link { display: flex; align-items: center; padding: 15px; color: #fff; text-decoration: none; border-bottom: 1px solid rgba(255,255,255,0.05); transition: 0.2s; }
        .user-link:hover, .user-link.active { background: rgba(0, 117, 255, 0.1); }
        .user-link.active { border-right: 3px solid var(--accent); }

        /* Chat Window */
        .chat-main { flex-grow: 1; display: flex; flex-direction: column; background: rgba(2, 4, 16, 0.5); position: relative; }
        .chat-header { padding: 15px 25px; background: rgba(6, 11, 38, 0.98); border-bottom: 1px solid rgba(255,255,255,0.1); display: flex; align-items: center; gap: 12px; z-index: 10; }
        
        .chat-messages { flex-grow: 1; padding: 25px; overflow-y: auto; display: flex; flex-direction: column; gap: 12px; }
        
        /* Bubbles */
        .msg-bubble { max-width: 70%; padding: 10px 16px; border-radius: 18px; font-size: 0.95rem; line-height: 1.5; position: relative; word-wrap: break-word; }
        .msg-sent { align-self: flex-end; background: var(--accent); color: white; border-bottom-right-radius: 4px; box-shadow: 0 4px 15px rgba(0, 117, 255, 0.3); }
        .msg-received { align-self: flex-start; background: rgba(255,255,255,0.1); border-bottom-left-radius: 4px; border: 1px solid rgba(255,255,255,0.05); }

        /* Input Area */
        .input-area { padding: 20px; background: rgba(6, 11, 38, 0.98); border-top: 1px solid rgba(255,255,255,0.1); }
        .custom-input-group { background: rgba(255,255,255,0.05); border-radius: 30px; padding: 5px 15px; border: 1px solid rgba(255,255,255,0.1); display: flex; align-items: center; }
        .custom-input-group input { background: transparent; border: none; color: white; flex-grow: 1; padding: 10px; outline: none; }

        .unread-badge { width: 10px; height: 10px; background: #ff3b3b; border-radius: 50%; display: inline-block; margin-left: auto; box-shadow: 0 0 10px #ff3b3b; }

        @media (max-width: 768px) {
            .chat-sidebar { width: 85px; }
            .chat-sidebar .user-info, .chat-sidebar h5 { display: none; }
            .chat-sidebar .user-link { justify-content: center; }
        }
    </style>
</head>
<body>

<div class="messenger-wrapper">
    <div class="chat-sidebar">
        <div class="p-4 border-bottom border-white border-opacity-10 d-flex align-items-center justify-content-between">
            <h5 class="fw-800 m-0">NEXUS CHAT</h5>
            <a href="dashboard.php" class="text-white-50"><i class="fa fa-arrow-left"></i></a>
        </div>
        
        <div class="overflow-auto">
            <?php
            $friends = mysqli_query($conn, "SELECT u.id, u.name, u.profile_image FROM friend_requests fr 
                        JOIN users u ON (fr.sender_id = u.id OR fr.receiver_id = u.id) 
                        WHERE (fr.sender_id = '$user_id' OR fr.receiver_id = '$user_id') 
                        AND fr.status = 'accepted' AND u.id != '$user_id'");
            
            while($f = mysqli_fetch_assoc($friends)):
                $f_id = $f['id'];
                $unread = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(id) as total FROM messages WHERE sender_id = '$f_id' AND receiver_id = '$user_id' AND is_read = 0"));
            ?>
            <a href="messages.php?id=<?= $f['id'] ?>" class="user-link <?= ($chat_with == $f['id']) ? 'active' : '' ?>">
                <img src="<?= $f['profile_image'] ?: 'default.png' ?>" width="45" height="45" class="rounded-circle border border-primary border-opacity-25">
                <div class="ms-3 user-info">
                    <div class="fw-bold small"><?= $f['name'] ?></div>
                    <div class="text-white-50" style="font-size: 10px;"><?= $unread['total'] > 0 ? 'New message' : 'Online' ?></div>
                </div>
                <?php if($unread['total'] > 0): ?>
                    <span class="unread-badge"></span>
                <?php endif; ?>
            </a>
            <?php endwhile; ?>
        </div>
    </div>

    <div class="chat-main">
        <?php if($chat_with > 0): 
            $curr_chat = mysqli_fetch_assoc(mysqli_query($conn, "SELECT name, profile_image FROM users WHERE id = '$chat_with'"));
        ?>
            <div class="chat-header">
                <img src="<?= $curr_chat['profile_image'] ?: 'default.png' ?>" width="40" height="40" class="rounded-circle border border-primary">
                <div>
                    <h6 class="m-0 fw-bold"><?= $curr_chat['name'] ?></h6>
                    <small class="text-success" style="font-size: 10px;"><i class="fa fa-circle me-1"></i> Active Now</small>
                </div>
            </div>

            <div class="chat-messages" id="chatBox">
                </div>

            <div class="input-area">
                <form id="msgForm" class="custom-input-group shadow-lg">
                    <input type="text" id="msgText" placeholder="Type a message..." autocomplete="off">
                    <button type="submit" class="btn btn-primary rounded-pill px-4 ms-2">
                        <i class="fa fa-paper-plane"></i>
                    </button>
                </form>
            </div>
        <?php else: ?>
            <div class="h-100 d-flex flex-column align-items-center justify-content-center opacity-25">
                <i class="fa fa-comments fa-5x mb-3"></i>
                <h5>Select a friend to chat</h5>
            </div>
        <?php endif; ?>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    const chatWith = <?= $chat_with ?>;
    let lastMsgCount = 0;
    let typingTimer;
    const doneTypingInterval = 2000;

    // 1. Fetch Messages & Typing Status
    function loadMessages() {
        if(chatWith === 0) return;
        $.ajax({
            url: 'fetch_messages_logic.php',
            type: 'GET',
            data: { chat_with: chatWith },
            success: function(data) {
                $('#chatBox').html(data);
                
                let currentCount = $('.msg-bubble').length;
                if(currentCount > lastMsgCount) {
                    var objDiv = document.getElementById("chatBox");
                    objDiv.scrollTop = objDiv.scrollHeight;
                    lastMsgCount = currentCount;
                }
            }
        });
    }

    // 2. Send Message
    $('#msgForm').on('submit', function(e) {
        e.preventDefault();
        let message = $('#msgText').val().trim();
        if(message === "") return;

        $.ajax({
            url: 'send_message_logic.php',
            type: 'POST',
            data: { receiver_id: chatWith, message: message },
            success: function() {
                $('#msgText').val(''); 
                // Typing status එක 0 කරනවා මැසේජ් එක යැව්ව ගමන්
                $.post('update_typing.php', { target_id: chatWith, status: 0 });
                loadMessages();
            }
        });
    });

    // 3. Typing Status Logic
    $('#msgText').on('keyup', function() {
        clearTimeout(typingTimer);
        $.post('update_typing.php', { target_id: chatWith, status: 1 });
        
        typingTimer = setTimeout(function() {
            $.post('update_typing.php', { target_id: chatWith, status: 0 });
        }, doneTypingInterval);
    });

    // Initial Load & Interval
    if(chatWith > 0) {
        setInterval(loadMessages, 2000);
        loadMessages();
    }
</script>

</body>
</html>