<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include 'db.php';

if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }
$user_id = $_SESSION['user_id'];

$u_res = mysqli_query($conn, "SELECT current_room_id FROM users WHERE id = '$user_id'");
$u_data = mysqli_fetch_assoc($u_res);
$room_id = $u_data['current_room_id'];

if(!$room_id){ header("Location: battle.php"); exit(); }

$r_res = mysqli_query($conn, "SELECT * FROM battle_rooms WHERE id = '$room_id'");
$room = mysqli_fetch_assoc($r_res);
$is_admin = ($room['admin_id'] == $user_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Lobby | Nexus Pro</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        :root { --vision-blue: #0075ff; --vision-bg: #030518; --glass: rgba(6, 11, 40, 0.9); }
        body { 
            background: var(--vision-bg); color: #ffffff; font-family: 'Plus Jakarta Sans', sans-serif;
            background-image: url('https://raw.githubusercontent.com/creativetimofficial/public-assets/master/vision-ui-dashboard-chakra/surface.png');
            background-size: cover; background-attachment: fixed;
        }
        .glass { background: var(--glass); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.12); border-radius: 24px; padding: 25px; }
        
        .member-item { background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 16px; transition: 0.3s; margin-bottom: 12px; padding: 10px; }
        .member-item:hover { background: rgba(0, 117, 255, 0.1); transform: translateX(5px); border-color: var(--vision-blue); }
        .member-avatar { width: 45px; height: 45px; border-radius: 12px; border: 2px solid var(--vision-blue); object-fit: cover; }
        .member-name { font-weight: 700; font-size: 0.95rem; color: #fff; }

        .stat-glow { color: #fff; font-size: 2.2rem; font-weight: 800; text-shadow: 0 0 15px rgba(0, 117, 255, 0.7); }
        .form-control { background: rgba(0, 0, 0, 0.3) !important; border: 1px solid rgba(255,255,255,0.2) !important; color: #fff !important; border-radius: 12px; }
        .form-control::placeholder { color: #a0aec0 !important; }
        .form-label { color: #cbd5e0 !important; font-weight: 600; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; }

        .btn-vision { background: linear-gradient(135deg, #0075ff, #005bc5); border: none; border-radius: 15px; font-weight: 700; color: white; padding: 12px; transition: 0.3s; }
        .btn-vision:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0, 117, 255, 0.4); }
        
        /* Invite Button Styling */
        .btn-invite { background: #25d366; color: white; border-radius: 12px; border: none; font-weight: 700; transition: 0.3s; }
        .btn-invite:hover { background: #1ebd5b; transform: scale(1.05); }

        .btn-complete { background: linear-gradient(135deg, #2dce89, #2dcecc); animation: glow-green 1.5s infinite alternate; border-radius: 18px; }
        @keyframes glow-green { from { box-shadow: 0 0 10px rgba(45, 206, 137, 0.4); } to { box-shadow: 0 0 25px rgba(45, 206, 137, 0.7); } }

        #member-list::-webkit-scrollbar { width: 4px; }
        #member-list::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 10px; }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="row g-4">
        <div class="col-lg-4">
            <div class="glass mb-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="flex-grow-1">
                        <h3 class="fw-800 mb-0"><?= htmlspecialchars($room['room_name']) ?></h3>
                        <span class="text-primary small fw-bold">ID: <?= $room['referral_code'] ?></span>
                    </div>
                    <div class="badge bg-primary px-3 py-2" id="member-count-badge">0</div>
                </div>

                <button onclick="inviteWhatsApp()" class="btn btn-invite w-100 mb-3 py-2 shadow-sm">
                    <i class="fab fa-whatsapp me-2"></i> Invite Friends
                </button>

                <hr style="border-color: rgba(255,255,255,0.1)">
                <h6 class="form-label mb-3">Active Members</h6>
                <div id="member-list" style="max-height: 350px; overflow-y: auto;">
                </div>
                <a href="room_logic.php?exit=true" class="btn btn-outline-danger w-100 btn-sm mt-3 border-0 opacity-75">Exit Lobby</a>
            </div>

            <div class="glass text-center">
                <h6 class="form-label mb-3">Progress Statistics</h6>
                <?php if($is_admin): ?>
                    <div class="input-group mb-4">
                        <input type="number" id="total_q_input" class="form-control" value="<?= $room['total_questions'] ?>" placeholder="Target">
                        <button class="btn btn-vision" onclick="setQuestionCount()">SET</button>
                    </div>
                <?php else: ?>
                    <div class="py-2">
                        <span class="text-muted small d-block">TARGET QUESTIONS</span>
                        <span class="stat-glow" id="target_display"><?= $room['total_questions'] ?></span>
                    </div>
                <?php endif; ?>
                <div class="row mt-2">
                    <div class="col-6">
                        <small class="text-muted d-block small uppercase">Created</small>
                        <h4 class="fw-800" id="created_count">0</h4>
                    </div>
                    <div class="col-6 border-start border-secondary">
                        <small class="text-muted d-block small uppercase">Remaining</small>
                        <h4 class="fw-800 text-info" id="remaining_count">0</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div id="question-area" class="glass h-100 d-none">
                <div id="form-content">
                    <h4 class="fw-800 mb-4"><i class="fa fa-magic text-primary me-2"></i> Create Challenge</h4>
                    <form id="q-form">
                        <div class="mb-4">
                            <label class="form-label">Question Text</label>
                            <textarea name="question_text" class="form-control" rows="4" placeholder="What is the capital of..." required></textarea>
                        </div>
                        <div id="options-container" class="mb-3">
                            <label class="form-label">Answer Options (Tick the correct one)</label>
                            <div class="input-group mb-2">
                                <div class="input-group-text"><input type="radio" name="correct_ans" value="0" required></div>
                                <input type="text" name="options[]" class="form-control" placeholder="Option 1" required>
                            </div>
                        </div>
                        <button type="button" class="btn btn-link text-info text-decoration-none small p-0 mb-4" onclick="addOption()">+ Add More Options</button>
                        <button type="submit" class="btn btn-vision w-100 fw-800">SAVE QUESTION</button>
                    </form>
                </div>

                <div id="finish-msg" class="text-center py-5 d-none h-100 d-flex flex-column justify-content-center">
                    <div class="mb-4">
                        <i class="fa fa-check-double fa-4x text-success" style="filter: drop-shadow(0 0 10px rgba(45,206,137,0.5));"></i>
                    </div>
                    <h2 class="fw-800">Arena Ready!</h2>
                    <p class="text-muted px-md-5">All required questions have been added. Click the button below to launch the battle paper for all participants.</p>
                    <div class="mt-4">
                        <button type="button" class="btn btn-vision btn-complete px-5 py-3 fw-800" onclick="location.href='paper.php'">
                            COMPLETE & START BATTLE <i class="fa fa-bolt ms-2"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div id="waiting-msg" class="glass h-100 text-center d-flex flex-column justify-content-center py-5">
                <i class="fa fa-hourglass-half fa-3x text-primary mb-4 opacity-50"></i>
                <h5 class="fw-700">Waiting for Configuration...</h5>
                <p class="text-muted">The room host needs to set the question target before we can start.</p>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    // --- ADDED: WhatsApp Invite Logic ---
    function inviteWhatsApp() {
        const roomName = "<?= htmlspecialchars($room['room_name']) ?>";
        const roomCode = "<?= $room['referral_code'] ?>";
        const siteUrl = window.location.origin; // Automatically gets your domain

        const message = `🔥 *BATTLE CHALLENGE: ${roomName}* 🔥%0A%0Aමචං! මම Nexus Pro එකේ අලුත් Battle Room එකක් හැදුවා. %0A%0Aදැන්ම මේකට ඇවිත් මට challenge කරන්න! %0A%0A🔑 *Room ID:* ${roomCode}%0A🌐 *Join here:* ${siteUrl}%0A%0A#NexusPro #BattleArena`;

        window.open(`https://wa.me/?text=${message}`, '_blank');
    }

    let optionCount = 1;
    function addOption() {
        let html = `<div class="input-group mb-2"><div class="input-group-text"><input type="radio" name="correct_ans" value="${optionCount}"></div><input type="text" name="options[]" class="form-control" placeholder="Option ${optionCount + 1}" required></div>`;
        $('#options-container').append(html);
        optionCount++;
    }

    function setQuestionCount() {
        let count = $('#total_q_input').val();
        if(count > 0) $.post('room_actions.php', { action: 'set_count', count: count }, function() { location.reload(); });
    }

    $('#q-form').submit(function(e) {
        e.preventDefault();
        $.post('room_actions.php', $(this).serialize() + '&action=save_q', function() {
            $('#q-form')[0].reset();
            updateStats();
        });
    });

    function updateStats() {
        $.getJSON('room_actions.php?action=get_stats', function(data) {
            if(data.kick) { window.location.href = 'battle.php'; return; }
            $('#member-list').html(data.members);
            $('#member-count-badge').text(data.member_count);
            $('#target_display').text(data.total);
            $('#created_count').text(data.created);
            $('#remaining_count').text(data.remaining);
            
            if(parseInt(data.total) > 0) {
                $('#question-area').removeClass('d-none');
                $('#waiting-msg').addClass('d-none');
            }

            if(parseInt(data.created) >= parseInt(data.total) && parseInt(data.total) > 0) {
                $('#form-content').addClass('d-none');
                $('#finish-msg').removeClass('d-none').addClass('d-flex');
            } else {
                $('#form-content').removeClass('d-none');
                $('#finish-msg').addClass('d-none').removeClass('d-flex');
            }
        });
    }

    setInterval(updateStats, 2000);
    updateStats();
</script>
</body>
</html>