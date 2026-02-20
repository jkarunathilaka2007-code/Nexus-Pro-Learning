<?php
session_start();
include 'db.php';
date_default_timezone_set('Asia/Colombo');

// --- AUTH CHECK ---
if (!isset($_SESSION['user_id'])) { 
    header("Location: login.php"); 
    exit(); 
}
$user_id = $_SESSION['user_id'];

// --- 1. PROFILE PICTURE UPDATE LOGIC ---
if(isset($_FILES['new_profile_pic'])) {
    $target_dir = "uploads/profiles/";
    if (!file_exists($target_dir)) { mkdir($target_dir, 0777, true); }
    $file_name = time() . '_' . basename($_FILES['new_profile_pic']['name']);
    $target_file = $target_dir . $file_name;
    if(move_uploaded_file($_FILES['new_profile_pic']['tmp_name'], $target_file)) {
        mysqli_query($conn, "UPDATE users SET profile_image = '$target_file' WHERE id = '$user_id'");
        header("Location: dashboard.php");
        exit();
    }
}

// --- 2. DATA FETCHING ---
$query = mysqli_query($conn, "SELECT * FROM users WHERE id = '$user_id'");
$user = mysqli_fetch_assoc($query);

// --- [ADDED] GLOBAL ID AUTO-GENERATION LOGIC ---
if (empty($user['global_id'])) {
    $found = false;
    $newID = "";
    while (!$found) {
        $newID = "NX-" . rand(10000, 99999);
        $check = mysqli_query($conn, "SELECT id FROM users WHERE global_id = '$newID'");
        if (mysqli_num_rows($check) == 0) $found = true;
    }
    mysqli_query($conn, "UPDATE users SET global_id = '$newID' WHERE id = '$user_id'");
    $user['global_id'] = $newID; 
}

$user_subjects = isset($user['subjects']) ? explode(", ", $user['subjects']) : [];

// Exam Countdown
$exam_date = new DateTime($user['exam_date'] ?? date('Y-m-d'));
$today_dt = new DateTime();
$diff = $today_dt->diff($exam_date);
$days_left = (int)$diff->format('%r%a');

// Hard Items Data (Incorrect Questions)
$hard_q_query = mysqli_query($conn, "SELECT subject_name, question_id FROM question_results WHERE user_id = '$user_id' AND is_correct = 0 LIMIT 20");
$hard_questions = [];
if($hard_q_query) {
    while($hq = mysqli_fetch_assoc($hard_q_query)) { 
        $hard_questions[$hq['subject_name']][] = $hq; 
    }
}
$hard_count = count($hard_questions);

// --- 3. WEEKLY STATS ---
$weekly_stats = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $day_name = date('D', strtotime($date));
    $res = mysqli_query($conn, "SELECT COUNT(id) as count FROM question_results WHERE user_id = '$user_id' AND DATE(attempted_at) = '$date'");
    $weekly_stats[$day_name] = ($res) ? mysqli_fetch_assoc($res)['count'] : 0;
}

$total_qs_res = mysqli_query($conn, "SELECT COUNT(id) as q_count FROM question_results WHERE user_id = '$user_id'");
$total_qs = ($total_qs_res) ? mysqli_fetch_assoc($total_qs_res)['q_count'] : 0;

// --- 4. QUICK NOTES (LIMITED TO 3) ---
$recent_notes = mysqli_query($conn, "SELECT * FROM question_notes WHERE user_id = '$user_id' ORDER BY id DESC LIMIT 3");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Nexus Pro | Dashboard</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        :root { 
            --bg-dark: #020410; 
            --accent-primary: #0075ff;
            --glass-bg: rgba(17, 25, 40, 0.85); 
            --sidebar-width: 260px;
            --sidebar-mini: 80px;
        }
        
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background: var(--bg-dark); 
            color: #fff; 
            margin: 0; 
            background-image: radial-gradient(circle at 10% 20%, rgba(0, 117, 255, 0.1) 0%, rgba(0, 0, 0, 0) 40%);
            background-attachment: fixed;
            overflow-x: hidden;
        }

        .sidebar { 
            width: var(--sidebar-width); 
            height: 100vh; 
            position: fixed; 
            left: 0; 
            top: 0; 
            background: rgba(6, 11, 38, 0.98); 
            border-right: 1px solid rgba(255,255,255,0.08);
            padding: 20px; 
            z-index: 1050; 
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            overflow-y: auto;
        }

        .sidebar.mini { width: var(--sidebar-mini); }
        .sidebar.mini .nav-text, .sidebar.mini .cat-title, .sidebar.mini .logo-text { display: none; }
        .sidebar.mini .nav-item { justify-content: center; padding: 12px 0; }
        .sidebar.mini .nav-item i { margin-right: 0; }

        @media (max-width: 768px) {
            .sidebar { left: -100%; width: 280px; } 
            .sidebar.mobile-active { left: 0; }
            .sidebar.mini { width: 280px; } 
            .main-content { margin-left: 0 !important; padding: 15px !important; }
            .mobile-overlay {
                display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.6); z-index: 1040;
                backdrop-filter: blur(4px);
            }
            .mobile-overlay.active { display: block; }
        }

        .nav-item { 
            padding: 12px 15px; 
            border-radius: 12px; 
            display: flex; 
            align-items: center; 
            color: #8fa0bc; 
            text-decoration: none; 
            margin-bottom: 5px; 
            font-weight: 600; 
            font-size: 0.9rem; 
            transition: 0.2s; 
        }
        .nav-item:hover, .nav-item.active { color: #fff; background: rgba(0, 117, 255, 0.15); }
        .nav-item.active i { color: var(--accent-primary); }
        .nav-item i { width: 25px; margin-right: 10px; text-align: center; }

        .cat-title { font-size: 0.7rem; font-weight: 800; color: #566a87; text-transform: uppercase; margin: 25px 0 10px 15px; }

        .main-content { margin-left: var(--sidebar-width); padding: 30px; transition: 0.3s ease; min-height: 100vh; }
        .main-content.expanded { margin-left: var(--sidebar-mini); }

        .glass-card { 
            background: var(--glass-bg); 
            backdrop-filter: blur(12px); 
            border: 1px solid rgba(255, 255, 255, 0.1); 
            border-radius: 16px; 
            box-shadow: 0 4px 20px rgba(0,0,0,0.2);
        }

        .hero-section {
            background: linear-gradient(135deg, rgba(0,117,255,0.15), rgba(0,0,0,0));
            border-radius: 20px;
            padding: 25px;
            border: 1px solid rgba(255,255,255,0.1);
        }
        .profile-pic { width: 70px; height: 70px; border-radius: 50%; object-fit: cover; border: 2px solid var(--accent-primary); }

        /* [ADDED] Global ID Badge & Edit Styling */
        .global-id-badge {
            background: rgba(0, 117, 255, 0.1);
            color: var(--accent-primary);
            padding: 2px 10px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 800;
            border: 1px solid rgba(0, 117, 255, 0.2);
            display: inline-flex;
            align-items: center;
        }
        .btn-edit-profile {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            color: #fff;
            transition: 0.3s;
        }
        .btn-edit-profile:hover {
            background: rgba(255,255,255,0.1);
            border-color: #fff;
            color: #fff;
        }

        .toggle-btn { background: none; border: none; color: white; font-size: 1.5rem; display: none; }
        @media (max-width: 768px) { .toggle-btn { display: block; } }
    </style>
</head>
<body>

<div class="mobile-overlay" id="mobileOverlay" onclick="toggleMobileSidebar()"></div>

<div class="sidebar" id="sidebar">
    <div class="d-flex align-items-center justify-content-between mb-4 px-2">
        <h4 class="fw-800 logo-text m-0"><i class="fa fa-bolt text-primary"></i> NEXUS</h4>
        <i class="fa fa-bars text-secondary cursor-pointer d-none d-md-block" onclick="toggleDesktopSidebar()" style="cursor: pointer;"></i>
        <i class="fa fa-times text-secondary cursor-pointer d-md-none" onclick="toggleMobileSidebar()" style="cursor: pointer;"></i>
    </div>
    
    <nav>
        <a href="dashboard.php" class="nav-item active"><i class="fa fa-grid-2"></i> <span class="nav-text">Dashboard</span></a>

        <div class="cat-title">Battle & Rank</div>
        <a href="battle.php" class="nav-item"><i class="fa fa-gamepad"></i> <span class="nav-text">Arena</span></a>
        <a href="rank.php" class="nav-item"><i class="fa fa-trophy"></i> <span class="nav-text">Leaderboard</span></a>
        <a href="spin.php" class="nav-item"><i class="fa fa-globe fa-spin"></i> <span class="nav-text">Spin</span></a>

        <div class="cat-title">Learning</div>
        <a href="add_subject_preparation.php" class="nav-item"><i class="fa fa-layer-group"></i> <span class="nav-text">Preparation</span></a>
        <a href="revision.php" class="nav-item"><i class="fa fa-rotate"></i> <span class="nav-text">Revision</span></a>
        <a href="view_notes.php" class="nav-item"><i class="fa fa-note-sticky"></i> <span class="nav-text">My Notes</span></a>
        <a href="maths_bank.php" class="nav-item"><i class="fa fa-square-root-variable"></i> <span class="nav-text">Formulas</span></a>
        <a href="global.php" class="nav-item"><i class="fa fa-globe"></i> <span class="nav-text">Global</span></a>

        <div class="cat-title">Personal</div>
        <a href="timer.php" class="nav-item"><i class="fa fa-clock"></i> <span class="nav-text">Timer</span></a>
        <a href="study_stats.php" class="nav-item"><i class="fa fa-chart-pie"></i> <span class="nav-text">Analytics</span></a>
        <a href="pdf_vault.php" class="nav-item"><i class="fa fa-file-pdf"></i> <span class="nav-text">Vault</span></a>
        <a href="timetable.php" class="nav-item"><i class="fa fa-calendar-day"></i> <span class="nav-text">Timetable</span></a>
        <a href="edit_profile.php" class="nav-item"><i class="fa fa-gear"></i> <span class="nav-text">Settings</span></a>
        
        <a href="logout.php" class="nav-item text-danger mt-4">
            <i class="fa fa-power-off"></i> <span class="nav-text">Log Out</span>
        </a>
    </nav>
</div>

<div class="main-content" id="content">
    
    <div class="d-flex align-items-center mb-3 d-md-none">
        <button class="toggle-btn me-3" onclick="toggleMobileSidebar()"><i class="fa fa-bars"></i></button>
        <h5 class="fw-800 m-0">Dashboard</h5>
    </div>

    <div class="hero-section mb-4">
        <div class="d-flex align-items-center flex-wrap gap-3">
            <form id="pForm" method="POST" enctype="multipart/form-data">
                <label style="cursor: pointer;">
                    <img src="<?= $user['profile_image'] ?? 'default.png' ?>" class="profile-pic">
                    <input type="file" name="new_profile_pic" hidden onchange="document.getElementById('pForm').submit()">
                </label>
            </form>
            <div class="flex-grow-1">
                <h3 class="fw-800 mb-1">Hello, <?= explode(' ', trim($user['name'] ?? 'User'))[0] ?>!</h3>
                <div class="d-flex align-items-center flex-wrap gap-2">
                    <p class="text-white-50 mb-0 small">
                        <i class="fa fa-fire text-warning"></i> <?= $user['streak_count'] ?? 0 ?> Day Streak 
                        <span class="mx-2">|</span> 
                        <?= $days_left ?> Days to Exam
                        <span class="mx-2">|</span>
                        <i class="fa fa-coins text-warning"></i> <?= number_format($user['total_coins'] ?? 0) ?> Coins
                    </p>
                    <div class="global-id-badge">
                        <i class="fa fa-earth-asia me-1"></i> #<?= $user['global_id'] ?>
                    </div>
                </div>
            </div>
            <div class="d-flex gap-2">
                <a href="edit_profile.php" class="btn btn-edit-profile rounded-pill btn-sm px-3 fw-bold">
                    <i class="fa fa-pen-to-square"></i>
                </a>
                <a href="start_preperation.php" class="btn btn-primary rounded-pill btn-sm px-4 fw-bold">Start Study</a>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="glass-card p-3 text-center h-100">
                <h3 class="fw-800 mb-0"><?= number_format($total_qs) ?></h3>
                <small class="text-white-50 text-uppercase" style="font-size: 10px;">Questions</small>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="glass-card p-3 text-center h-100">
                <h3 class="fw-800 mb-0 text-danger"><?= number_format($hard_count) ?></h3>
                <small class="text-white-50 text-uppercase" style="font-size: 10px;">To Review</small>
            </div>
        </div> 
        <div class="col-6 col-md-3">
            <div class="glass-card p-3 text-center h-100">
                <h3 class="fw-800 mb-0 text-info"><?= $days_left ?></h3>
                <small class="text-white-50 text-uppercase" style="font-size: 10px;">Days Left</small>
            </div>
        </div>
        
        <div class="col-6 col-md-3">
            <div class="glass-card p-3 text-center h-100">
                <h3 class="fw-800 mb-0" style="color: #FFD700;"><?= number_format($user['total_coins'] ?? 0) ?></h3>
                <small class="text-white-50 text-uppercase" style="font-size: 10px;">Total Coins</small>
            </div>
        </div>
        </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold m-0">Your Subjects</h6>
                <a href="add_subject_preparation.php" class="btn btn-sm btn-outline-light rounded-pill" style="font-size: 10px;"><i class="fa fa-plus"></i> Add</a>
            </div>
            
            <div class="row g-3">
                <?php if(empty($user_subjects) || (count($user_subjects)==1 && empty($user_subjects[0]))): ?>
                    <div class="col-12 text-center py-4 text-white-50">No subjects added.</div>
                <?php else: ?>
                    <?php foreach($user_subjects as $sub): if(empty($sub)) continue; 
                        $t_res = mysqli_query($conn, "SELECT id FROM trackers WHERE user_id='$user_id' AND subject='$sub'");
                        $has_t = ($t_res) ? mysqli_num_rows($t_res) > 0 : false;
                    ?>
                    <div class="col-md-6">
                        <div class="glass-card p-3 d-flex justify-content-between align-items-center">
                            <span class="fw-bold small"><?= $sub ?></span>
                            <a href="<?= $has_t ? 'start_preperation.php?subject='.urlencode($sub) : 'add_subject_preparation.php' ?>" 
                               class="btn btn-sm <?= $has_t ? 'btn-primary' : 'btn-outline-secondary' ?> rounded-pill px-3" style="font-size: 10px;">
                               <?= $has_t ? 'Study' : 'Setup' ?>
                            </a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold m-0">Quick Notes</h6>
                <a href="view_notes.php" class="text-white-50 small text-decoration-none">View All</a>
            </div>
            
            <div class="glass-card p-3">
                <?php if(mysqli_num_rows($recent_notes) > 0): ?>
                    <?php while($n = mysqli_fetch_assoc($recent_notes)): ?>
                        <div class="mb-3 pb-2 border-bottom border-white border-opacity-10 last-no-border">
                            <p class="mb-1 small fw-bold text-truncate"><?= strip_tags($n['note']) ?></p>
                            <small class="text-white-50" style="font-size: 9px;"><?= $n['subject'] ?></small>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="text-center text-white-50 small my-3">No notes yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleDesktopSidebar() {
        if(window.innerWidth > 768) {
            document.getElementById('sidebar').classList.toggle('mini');
            document.getElementById('content').classList.toggle('expanded');
        }
    }

    function toggleMobileSidebar() {
        document.getElementById('sidebar').classList.toggle('mobile-active');
        document.getElementById('mobileOverlay').classList.toggle('active');
    }
</script>
</body>
</html>