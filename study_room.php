<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }

$user_id = $_SESSION['user_id'];
$room_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$room_q = mysqli_query($conn, "SELECT * FROM study_rooms WHERE id = '$room_id'");
if (mysqli_num_rows($room_q) == 0) { header("Location: study_lobby.php"); exit(); }
$room = mysqli_fetch_assoc($room_q);

mysqli_query($conn, "INSERT IGNORE INTO room_members (room_id, user_id) VALUES ('$room_id', '$user_id')");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $room['room_name'] ?> | Nexus Study</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --bg: #05070a; --accent: #007bff; --glass: rgba(255, 255, 255, 0.05); }
        body { background: var(--bg); color: #e0e0e0; font-family: 'Inter', sans-serif; height: 100vh; overflow: hidden; margin: 0; }
        
        .main-wrapper { display: flex; height: 100vh; position: relative; }

        /* Responsive Sidebar */
        .sidebar { 
            width: 280px; background: #0a0e14; border-right: 1px solid rgba(255,255,255,0.1); 
            display: flex; flex-direction: column; padding: 20px; transition: transform 0.3s ease;
            z-index: 2000;
        }

        /* Chat Area */
        .chat-area { flex-grow: 1; display: flex; flex-direction: column; width: 100%; position: relative; }
        .chat-header { padding: 12px 20px; background: rgba(10, 14, 20, 0.95); border-bottom: 1px solid rgba(255,255,255,0.1); backdrop-filter: blur(10px); }
        .chat-box { flex-grow: 1; overflow-y: auto; padding: 15px; display: flex; flex-direction: column; gap: 12px; }
        
        /* Message Bubbles */
        .msg-bubble { max-width: 85%; padding: 10px 14px; border-radius: 18px; font-size: 0.92rem; word-wrap: break-word; }
        .msg-me { align-self: flex-end; background: var(--accent); color: white; border-bottom-right-radius: 4px; }
        .msg-other { align-self: flex-start; background: #1c232d; border-bottom-left-radius: 4px; }

        /* Input Area */
        .input-wrapper { padding: 15px; background: #0a0e14; border-top: 1px solid rgba(255,255,255,0.1); }
        .input-group { background: #161b22; border-radius: 25px; padding: 2px 10px; }
        .input-group input { background: transparent; border: none; color: white; box-shadow: none !important; }

        /* Mobile Adjustments */
        @media (max-width: 768px) {
            .sidebar { position: absolute; left: 0; transform: translateX(-100%); height: 100%; }
            .sidebar.active { transform: translateX(0); }
            .overlay { display: none; position: absolute; inset: 0; background: rgba(0,0,0,0.7); z-index: 1500; }
            .overlay.active { display: block; }
        }

        .menu-toggle { display: none; cursor: pointer; font-size: 1.4rem; color: var(--accent); margin-right: 15px; }
        @media (max-width: 768px) { .menu-toggle { display: block; } }
    </style>
</head>
<body>

<div class="overlay" onclick="toggleSidebar()"></div>

<div class="main-wrapper">
    <div class="sidebar" id="sidebar">
        <h6 class="text-uppercase small fw-bold text-muted mb-3">Study Partners</h6>
        <div id="memberList" class="flex-grow-1 overflow-auto"></div>
        
        <div class="mt-auto pt-3 border-top border-white border-opacity-10">
            <button class="btn btn-warning w-100 rounded-pill fw-bold mb-2 btn-sm" data-bs-toggle="modal" data-bs-target="#noteModal">Share Note</button>
            <button class="btn btn-primary w-100 rounded-pill fw-bold mb-3 btn-sm" data-bs-toggle="modal" data-bs-target="#pollModal">Create Poll</button>
            <a href="exit_room.php?id=<?= $room_id ?>" class="btn btn-outline-danger w-100 rounded-pill btn-sm">Exit Room</a>
        </div>
    </div>

    <div class="chat-area">
        <div class="chat-header d-flex align-items-center">
            <i class="fa fa-bars menu-toggle" onclick="toggleSidebar()"></i>
            <h6 class="m-0 fw-bold text-info"><?= strtoupper($room['room_name']) ?></h6>
        </div>

        <div class="chat-box" id="chatBox"></div>

        <div class="input-wrapper">
            <form id="chatForm" class="input-group">
                <input type="text" id="msgInput" class="form-control" placeholder="Discuss..." autocomplete="off">
                <button type="submit" class="btn btn-primary rounded-circle ms-2"><i class="fa fa-paper-plane"></i></button>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="pollModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content bg-dark border-secondary">
            <div class="modal-body p-4">
                <h6 class="text-info mb-3">Create Poll</h6>
                <input type="text" id="pollQ" class="form-control bg-dark text-white border-secondary mb-3" placeholder="Question">
                <div id="optionsContainer">
                    <input type="text" class="form-control bg-dark text-white border-secondary mb-2 poll-opt" placeholder="Option 1">
                    <input type="text" class="form-control bg-dark text-white border-secondary mb-2 poll-opt" placeholder="Option 2">
                    <input type="text" class="form-control bg-dark text-white border-secondary mb-2 poll-opt" placeholder="Option 3">
                </div>
                <button class="btn btn-sm text-info p-0 mb-3" onclick="addOptionField()">+ Add Option</button>
                <button class="btn btn-info w-100 fw-bold" onclick="submitDynamicPoll()">Launch</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="noteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-dark border-secondary">
            <div class="modal-header border-secondary"><h6 class="modal-title text-warning">Share a Note</h6></div>
            <div class="modal-body p-0" style="max-height: 350px; overflow-y: auto;">
                <?php
                $my_notes = mysqli_query($conn, "SELECT id, subject, paper_year FROM question_notes WHERE user_id = '$user_id' ORDER BY id DESC");
                while($n = mysqli_fetch_assoc($my_notes)): ?>
                    <div class="d-flex justify-content-between p-3 border-bottom border-white border-opacity-10">
                        <span class="small fw-bold"><?= htmlspecialchars($n['subject']) ?></span>
                        <button class="btn btn-primary btn-sm rounded-pill" onclick="shareNote(<?= $n['id'] ?>)">Share</button>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const roomId = <?= $room_id ?>;
    let optionCount = 3;

    function toggleSidebar() { $('#sidebar, .overlay').toggleClass('active'); }

    function loadMessages() {
        $.get('fetch_group_messages.php', { room_id: roomId }, function(data) {
            $('#chatBox').html(data);
            $('#chatBox').scrollTop($('#chatBox')[0].scrollHeight);
        });
    }

    $('#chatForm').on('submit', function(e) {
        e.preventDefault();
        let msg = $('#msgInput').val().trim();
        if(!msg) return;
        $.post('send_group_message.php', { room_id: roomId, message: msg }, () => { $('#msgInput').val(''); loadMessages(); });
    });

    function submitDynamicPoll() {
        let q = $('#pollQ').val(), opts = [];
        $('.poll-opt').each(function() { if($(this).val()) opts.push($(this).val()); });
        $.post('submit_poll.php', { room_id: roomId, question: q, options: opts }, () => { $('#pollModal').modal('hide'); loadMessages(); });
    }

    function vote(pollId, option) {
        $.post('vote_logic.php', { poll_id: pollId, option: option }, (res) => { if(res === 'already_voted') alert("Already voted!"); loadMessages(); });
    }

    function shareNote(id) { $.post('share_note_logic.php', { note_id: id, room_id: roomId }, () => { $('#noteModal').modal('hide'); loadMessages(); }); }
    function useNote(id) { $.post('use_note_logic.php', { note_id: id }, res => alert(res)); }
    function addOptionField() { if(optionCount < 5) { optionCount++; $('#optionsContainer').append(`<input type="text" class="form-control bg-dark text-white border-secondary mb-2 poll-opt" placeholder="Option ${optionCount}">`); } }

    setInterval(() => { $.get('fetch_room_members.php', { room_id: roomId }, d => $('#memberList').html(d)); loadMessages(); }, 3000);
    loadMessages();
</script>
</body>
</html>