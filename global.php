<?php
session_start();
include 'db.php';
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }

$user_id = $_SESSION['user_id'];

// 1. තමන්ගේ තොරතුරු ගැනීම
$me_q = mysqli_query($conn, "SELECT global_id FROM users WHERE id = '$user_id'");
$me = mysqli_fetch_assoc($me_q);

// 2. SEARCH LOGIC
$search_result = null;
if (isset($_POST['search_user'])) {
    $sid = mysqli_real_escape_string($conn, $_POST['search_query']);
    $q = mysqli_query($conn, "SELECT id, name, global_id, profile_image FROM users WHERE global_id = '$sid' AND id != '$user_id'");
    $search_result = mysqli_fetch_assoc($q);
}

// 3. SEND REQUEST LOGIC
if (isset($_POST['send_req'])) {
    $target = $_POST['target_id'];
    $check = mysqli_query($conn, "SELECT id FROM friend_requests WHERE (sender_id = '$user_id' AND receiver_id = '$target') OR (sender_id = '$target' AND receiver_id = '$user_id')");
    if (mysqli_num_rows($check) == 0) {
        mysqli_query($conn, "INSERT INTO friend_requests (sender_id, receiver_id) VALUES ('$user_id', '$target')");
    }
}

// 4. ACCEPT/DECLINE LOGIC
if (isset($_POST['manage_req'])) {
    $rid = $_POST['req_id'];
    $action = ($_POST['manage_req'] == 'accept') ? 'accepted' : 'declined';
    mysqli_query($conn, "UPDATE friend_requests SET status = '$action' WHERE id = '$rid'");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nexus | Global Network</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    
    <style>
        :root { --bg: #020410; --accent: #0075ff; --glass: rgba(17, 25, 40, 0.75); }
        body { 
            background: var(--bg); color: #fff; font-family: 'Plus Jakarta Sans', sans-serif;
            background-image: radial-gradient(circle at 50% 0%, rgba(0, 117, 255, 0.15) 0%, transparent 50%);
            min-height: 100vh;
        }
        .glass-card { background: var(--glass); backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.1); border-radius: 20px; }
        .search-bar { background: rgba(0,0,0,0.3); border: 1px solid var(--accent); border-radius: 50px; padding: 5px 20px; }
        .search-bar input { background: transparent; border: none; color: white; outline: none; padding: 10px; width: 100%; }
        
        .friend-list-item { 
            background: rgba(255,255,255,0.03); border-radius: 15px; padding: 15px; 
            margin-bottom: 10px; border: 1px solid rgba(255,255,255,0.05); transition: 0.3s;
        }
        .friend-list-item:hover { background: rgba(255,255,255,0.06); transform: translateY(-2px); }
        
        /* Study Room Button Styling */
        .btn-study-room {
            background: linear-gradient(45deg, #0075ff, #00d4ff);
            border: none; color: white; font-weight: 700; transition: 0.3s;
            border: 1px solid rgba(255, 255, 255, 0.2);
            animation: pulse-blue 2s infinite;
        }
        .btn-study-room:hover {
            color: white; transform: scale(1.05); box-shadow: 0 0 20px rgba(0, 117, 255, 0.6);
        }

        @keyframes pulse-blue {
            0% { box-shadow: 0 0 0 0 rgba(0, 117, 255, 0.7); }
            70% { box-shadow: 0 0 0 10px rgba(0, 117, 255, 0); }
            100% { box-shadow: 0 0 0 0 rgba(0, 117, 255, 0); }
        }

        .unread-border { border-left: 4px solid var(--accent) !important; }
        .new-dot { width: 10px; height: 10px; background: #ff3b3b; border-radius: 50%; display: inline-block; border: 2px solid var(--bg); }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="text-center mb-5">
        <h2 class="fw-800"><i class="fa fa-earth-asia text-primary me-2"></i>GLOBAL NETWORK</h2>
        <p class="text-white-50">Find your tribe and study together in real-time.</p>
        
        <div class="d-flex flex-wrap justify-content-center gap-3 align-items-center mt-4">
            <div class="badge bg-dark border border-secondary px-3 py-2 rounded-pill">MY ID: #<?= $me['global_id'] ?></div>
            <a href="study_lobby.php" class="btn btn-study-room px-4 py-2 rounded-pill">
                <i class="fa fa-graduation-cap me-2"></i>GO TO STUDY ROOM
            </a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-5">
            <div class="glass-card p-4 h-100 shadow-lg">
                <h5 class="fw-bold mb-4">Discovery</h5>
                <form method="POST" class="search-bar d-flex align-items-center mb-4">
                    <input type="text" name="search_query" placeholder="NX-XXXXX" required>
                    <button type="submit" name="search_user" class="btn btn-primary rounded-circle"><i class="fa fa-search"></i></button>
                </form>

                <?php if ($search_result): ?>
                    <div class="friend-list-item d-flex align-items-center justify-content-between border-primary">
                        <div class="d-flex align-items-center gap-3">
                            <img src="<?= $search_result['profile_image'] ?: 'default.png' ?>" width="45" height="45" class="rounded-circle">
                            <div>
                                <div class="fw-bold"><?= $search_result['name'] ?></div>
                                <div class="small text-white-50">#<?= $search_result['global_id'] ?></div>
                            </div>
                        </div>
                        <form method="POST">
                            <input type="hidden" name="target_id" value="<?= $search_result['id'] ?>">
                            <button type="submit" name="send_req" class="btn btn-sm btn-primary rounded-pill">Connect</button>
                        </form>
                    </div>
                <?php endif; ?>

                <hr class="my-4 border-secondary opacity-25">
                <h6 class="fw-bold text-white-50 mb-3 small">REQUESTS</h6>
                <?php
                $requests = mysqli_query($conn, "SELECT fr.*, u.name, u.global_id FROM friend_requests fr JOIN users u ON fr.sender_id = u.id WHERE fr.receiver_id = '$user_id' AND fr.status = 'pending'");
                while($r = mysqli_fetch_assoc($requests)):
                ?>
                    <div class="friend-list-item d-flex justify-content-between align-items-center">
                        <div class="fw-bold small"><?= $r['name'] ?></div>
                        <form method="POST" class="d-flex gap-2">
                            <input type="hidden" name="req_id" value="<?= $r['id'] ?>">
                            <button name="manage_req" value="accept" class="btn btn-sm btn-success rounded-circle"><i class="fa fa-check"></i></button>
                            <button name="manage_req" value="decline" class="btn btn-sm btn-danger rounded-circle"><i class="fa fa-times"></i></button>
                        </form>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="glass-card p-4 h-100 shadow-lg">
                <h5 class="fw-bold mb-4">Study Circle</h5>
                <div class="row g-3">
                    <?php
                    $friends = mysqli_query($conn, "SELECT u.id, u.name, u.global_id, u.profile_image FROM friend_requests fr 
                                JOIN users u ON (fr.sender_id = u.id OR fr.receiver_id = u.id) 
                                WHERE (fr.sender_id = '$user_id' OR fr.receiver_id = '$user_id') 
                                AND fr.status = 'accepted' AND u.id != '$user_id'");
                    
                    while($f = mysqli_fetch_assoc($friends)):
                        $f_id = $f['id'];
                        $unread_q = mysqli_query($conn, "SELECT id FROM messages WHERE sender_id = '$f_id' AND receiver_id = '$user_id' AND is_read = 0");
                        $has_unread = mysqli_num_rows($unread_q) > 0;
                    ?>
                    <div class="col-md-6">
                        <div class="friend-list-item d-flex align-items-center gap-3 <?= $has_unread ? 'unread-border' : '' ?>">
                            <div class="position-relative">
                                <img src="<?= $f['profile_image'] ?: 'default.png' ?>" width="40" height="40" class="rounded-circle border border-primary">
                                <?php if($has_unread): ?>
                                    <span class="position-absolute top-0 start-100 translate-middle new-dot"></span>
                                <?php endif; ?>
                            </div>
                            <div class="flex-grow-1 overflow-hidden">
                                <div class="fw-bold small text-truncate"><?= $f['name'] ?></div>
                                <div class="text-white-50" style="font-size: 10px;">#<?= $f['global_id'] ?></div>
                            </div>
                            <a href="messages.php?id=<?= $f['id'] ?>" class="text-white-50"><i class="fa <?= $has_unread ? 'fa-comment-dots text-primary' : 'fa-comment' ?>"></i></a>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Unread messages තියෙනවාද කියලා බලන්න විනාඩියකට සැරයක් Refresh වෙනවා
    setInterval(() => location.reload(), 60000);
</script>

</body>
</html>