<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$error = "";

// 1. CREATE ROOM LOGIC
if (isset($_POST['create_room'])) {
    $r_name = mysqli_real_escape_string($conn, $_POST['room_name']);
    // අකුරු සහ ඉලක්කම් මිශ්‍ර අංක 6ක අහඹු කේතයක් සෑදීම
    $r_code = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 6)); 
    
    $insert_room = mysqli_query($conn, "INSERT INTO study_rooms (room_name, room_code, creator_id) VALUES ('$r_name', '$r_code', '$user_id')");
    
    if ($insert_room) {
        $room_id = mysqli_insert_id($conn);
        // Creator ව සාමාජිකයෙක් ලෙස ඇතුළත් කිරීම
        mysqli_query($conn, "INSERT INTO room_members (room_id, user_id) VALUES ('$room_id', '$user_id')");
        header("Location: study_room.php?id=" . $room_id);
        exit();
    }
}

// 2. JOIN ROOM LOGIC
if (isset($_POST['join_room'])) {
    $code = mysqli_real_escape_string($conn, $_POST['room_code']);
    $q = mysqli_query($conn, "SELECT id FROM study_rooms WHERE room_code = '$code'");
    
    if (mysqli_num_rows($q) > 0) {
        $room = mysqli_fetch_assoc($q);
        $room_id = $room['id'];
        // දැනටමත් සාමාජිකයෙක්දැයි බැලීම (IGNORE පාවිච්චි කර ඇත)
        mysqli_query($conn, "INSERT IGNORE INTO room_members (room_id, user_id) VALUES ('$room_id', '$user_id')");
        header("Location: study_room.php?id=" . $room_id);
        exit();
    } else {
        $error = "Invalid Room Code! Please check and try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nexus | Study Lobby</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --bg: #020410; --accent: #0075ff; --glass: rgba(17, 25, 40, 0.85); }
        body { 
            background: var(--bg); color: #fff; font-family: 'Plus Jakarta Sans', sans-serif;
            background-image: radial-gradient(circle at 50% 50%, rgba(0, 117, 255, 0.1) 0%, transparent 80%);
            height: 100vh; display: flex; align-items: center;
        }
        .lobby-card { background: var(--glass); backdrop-filter: blur(15px); border: 1px solid rgba(255,255,255,0.1); border-radius: 25px; padding: 40px; box-shadow: 0 20px 50px rgba(0,0,0,0.5); }
        .form-control { background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.1); color: white; border-radius: 12px; padding: 12px; }
        .form-control:focus { background: rgba(0,0,0,0.4); color: white; border-color: var(--accent); box-shadow: none; }
        .btn-create { background: var(--accent); border: none; font-weight: 700; border-radius: 12px; transition: 0.3s; }
        .btn-join { background: #00d4ff; border: none; font-weight: 700; border-radius: 12px; transition: 0.3s; color: #000; }
        .btn:hover { transform: translateY(-3px); filter: brightness(1.1); }
        .or-divider { position: relative; text-align: center; margin: 30px 0; }
        .or-divider::before { content: ""; position: absolute; top: 50%; left: 0; width: 45%; height: 1px; background: rgba(255,255,255,0.1); }
        .or-divider::after { content: ""; position: absolute; top: 50%; right: 0; width: 45%; height: 1px; background: rgba(255,255,255,0.1); }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            <div class="lobby-card">
                <div class="text-center mb-5">
                    <h1 class="fw-800 mb-2">STUDY LOBBY</h1>
                    <p class="text-white-50">Create a private space or join your partners</p>
                </div>

                <?php if($error): ?>
                    <div class="alert alert-danger bg-danger bg-opacity-10 border-danger text-danger border-0 rounded-4 mb-4 text-center small">
                        <i class="fa fa-circle-exclamation me-2"></i><?= $error ?>
                    </div>
                <?php endif; ?>

                <div class="row g-5">
                    <div class="col-md-6 border-end border-white border-opacity-10">
                        <div class="text-center mb-4">
                            <i class="fa fa-rocket fa-3x text-primary mb-3"></i>
                            <h5 class="fw-bold">Start New Session</h5>
                        </div>
                        <form method="POST">
                            <div class="mb-3">
                                <label class="small text-white-50 mb-2">Room Name</label>
                                <input type="text" name="room_name" class="form-control" placeholder="e.g. Final Exam Prep" required>
                            </div>
                            <button type="submit" name="create_room" class="btn btn-create w-100 py-3">
                                Create Room <i class="fa fa-arrow-right ms-2"></i>
                            </button>
                        </form>
                    </div>

                    <div class="col-md-6">
                        <div class="text-center mb-4">
                            <i class="fa fa-key fa-3x text-info mb-3"></i>
                            <h5 class="fw-bold">Join Partner's Room</h5>
                        </div>
                        <form method="POST">
                            <div class="mb-3">
                                <label class="small text-white-50 mb-2">Room Code</label>
                                <input type="text" name="room_code" class="form-control text-center fw-bold" placeholder="6-DIGIT CODE" maxlength="6" required>
                            </div>
                            <button type="submit" name="join_room" class="btn btn-join w-100 py-3">
                                Join Session <i class="fa fa-door-open ms-2"></i>
                            </button>
                        </form>
                    </div>
                </div>

                <div class="text-center mt-5">
                    <a href="global.php" class="text-white-50 text-decoration-none small">
                        <i class="fa fa-arrow-left me-2"></i> Back to Network
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>