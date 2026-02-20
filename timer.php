<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) { 
    header("Location: login.php"); 
    exit(); 
}

$user_id = $_SESSION['user_id'];
$today = date('Y-m-d');

// 1. Active Goal (done = 0) ලබාගැනීම සහ දැනට කර ඇති කාලය ගණනය කිරීම
$active_query = "SELECT g.*, 
                 IFNULL((SELECT SUM(duration) FROM timer_sessions WHERE goal_id = g.goal_id), 0) as total_done
                 FROM goals g 
                 WHERE g.user_id = '$user_id' AND DATE(g.created_at) = '$today' AND g.done = 0 
                 ORDER BY g.goal_id DESC LIMIT 1";
$active_res = mysqli_query($conn, $active_query);
$active_goal = mysqli_fetch_assoc($active_res);

// 2. Dashboard Header එක සඳහා අද දවසේ මුළු කාලය
$total_time_query = "SELECT SUM(duration) as total_sec FROM timer_sessions WHERE user_id = '$user_id' AND DATE(created_at) = '$today'";
$total_time_res = mysqli_fetch_assoc(mysqli_query($conn, $total_time_query));
$total_today_sec = $total_time_res['total_sec'] ? (int)$total_time_res['total_sec'] : 0;
$total_today_hours = floor($total_today_sec / 3600);
$total_today_mins = floor(($total_today_sec % 3600) / 60);

// 3. Goal History එක ලබාගැනීම
$history_query = "SELECT g.*, 
                 IFNULL(SUM(s.duration), 0) as total_duration_done
                 FROM goals g 
                 LEFT JOIN timer_sessions s ON g.goal_id = s.goal_id
                 WHERE g.user_id = '$user_id' AND DATE(g.created_at) = '$today'
                 GROUP BY g.goal_id ORDER BY g.goal_id DESC";
$history_res = mysqli_query($conn, $history_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nexus Focus | Advanced Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --bg: #050810; --accent: #3b82f6; --success: #10b981; --glass: rgba(15, 23, 42, 0.85); }
        body { 
            background: var(--bg); background-image: radial-gradient(circle at top right, #1e293b, #050810);
            color: white; font-family: 'Plus Jakarta Sans', sans-serif; min-height: 100vh;
        }
        .glass-card { background: var(--glass); backdrop-filter: blur(15px); border: 1px solid rgba(255,255,255,0.1); border-radius: 24px; padding: 2rem; }
        .goal-input { background: rgba(0,0,0,0.4) !important; border: 1px solid #334155 !important; color: #60a5fa !important; font-weight: 700; text-align: center; border-radius: 12px; }
        .remaining-box { background: rgba(59, 130, 246, 0.1); border: 1px solid rgba(59, 130, 246, 0.4); border-radius: 20px; padding: 25px; }
        .text-bright { color: #ffffff !important; font-weight: 600; }
        .time-label { font-size: 0.75rem; color: #94a3b8; font-weight: 800; letter-spacing: 1.5px; text-transform: uppercase; }
        .progress-bar-custom { height: 8px; background: rgba(255,255,255,0.08); border-radius: 10px; overflow: hidden; }
        .progress-fill { height: 100%; background: var(--success); transition: 0.6s cubic-bezier(0.4, 0, 0.2, 1); }
        .badge-status { font-size: 0.65rem; font-weight: 800; padding: 6px 14px; border-radius: 50px; border: 1px solid currentColor; }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h2 class="fw-800 mb-0">Nexus <span class="text-primary">Focus</span></h2>
            <p class="text-white-50 small mb-0">Track your progress and stay consistent.</p>
        </div>
        <div class="glass-card py-2 px-4 shadow-sm border-0" style="background: rgba(59, 130, 246, 0.15);">
            <div class="time-label text-primary">TOTAL FOCUS TODAY</div>
            <div class="h4 fw-bold mb-0 text-bright"><?= $total_today_hours ?>h <?= $total_today_mins ?>m</div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-5">
            <div class="glass-card shadow-lg h-100 border-0">
                <?php if ($active_goal): 
                    $remaining = $active_goal['goal_duration_seconds'] - $active_goal['total_done'];
                    $rem_h = floor(max(0, $remaining) / 3600);
                    $rem_m = floor((max(0, $remaining) % 3600) / 60);
                ?>
                    <div class="text-center py-4">
                        <div class="mb-4">
                            <i class="fa-solid fa-fire-alt text-primary fa-3x"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Target in Progress</h4>
                        <div class="remaining-box mb-4">
                            <div class="h1 fw-800 text-primary mb-0"><?= $rem_h ?>h <?= $rem_m ?>m</div>
                            <div class="time-label text-bright mt-2">REMAINING TARGET</div>
                        </div>
                        <p class="text-white-50 small mb-4 px-3">ඔබ දැනටමත් අද සඳහා ඉලක්කයක් ලබා දී ඇත. එය අවසන් කරන තුරු අලුත් එකක් ලබා දිය නොහැක.</p>
                        <a href="start_timer.php?goal_id=<?= $active_goal['goal_id'] ?>" class="btn btn-primary w-100 py-3 rounded-4 fw-bold shadow">
                            <i class="fa-solid fa-play me-2"></i> RESUME SESSION
                        </a>
                    </div>
                <?php else: ?>
                    <h4 class="fw-bold mb-4">Set Goal Duration</h4>
                    <form action="create_goal.php" method="POST">
                        <div class="row g-3 mb-4">
                            <div class="col-6">
                                <label class="time-label mb-2 d-block">HOURS</label>
                                <input type="number" name="goal_h" class="form-control goal-input py-3 fs-3" value="1" min="0" max="23">
                            </div>
                            <div class="col-6">
                                <label class="time-label mb-2 d-block">MINUTES</label>
                                <input type="number" name="goal_m" class="form-control goal-input py-3 fs-3" value="0" min="0" max="59">
                            </div>
                        </div>
                        <div class="row g-3 mb-4 text-start">
                            <div class="col-6">
                                <label class="time-label mb-2 d-block">FOCUS (MIN)</label>
                                <input type="number" name="focus" class="form-control goal-input py-2" value="25">
                            </div>
                            <div class="col-6">
                                <label class="time-label mb-2 d-block">BREAK (MIN)</label>
                                <input type="number" name="interval" class="form-control goal-input py-2" value="5">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 py-3 rounded-4 fw-bold shadow">
                            <i class="fa-solid fa-plus-circle me-2"></i> START NEW GOAL
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="glass-card shadow-lg h-100 border-0">
                <h4 class="fw-bold mb-4">Daily Performance</h4>
                <div class="table-responsive">
                    <table class="table table-dark table-hover border-0">
                        <thead>
                            <tr class="text-muted small">
                                <th class="border-0">GOAL</th>
                                <th class="border-0">CREATED</th>
                                <th class="border-0">PROGRESS</th>
                                <th class="border-0 text-end">STATUS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(mysqli_num_rows($history_res) > 0): ?>
                                <?php while($row = mysqli_fetch_assoc($history_res)): 
                                    $target_sec = (int)$row['goal_duration_seconds'];
                                    $done_sec = (int)$row['total_duration_done'];
                                    // Safety check for Division by Zero
                                    $pct = ($target_sec > 0) ? min(round(($done_sec / $target_sec) * 100), 100) : 0;
                                ?>
                                <tr class="align-middle">
                                    <td class="py-3">
                                        <div class="fw-bold text-bright">G-<?= $row['goal_id'] ?></div>
                                        <div class="small text-white-50">
                                            <?= floor($target_sec / 3600) ?>h <?= floor(($target_sec % 3600) / 60) ?>m
                                        </div>
                                    </td>
                                    <td class="py-3 text-bright small">
                                        <?= date('h:i A', strtotime($row['created_at'])) ?>
                                    </td>
                                    <td class="py-3" style="min-width: 120px;">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="progress-bar-custom flex-grow-1">
                                                <div class="progress-fill" style="width: <?= $pct ?>%"></div>
                                            </div>
                                            <span class="small fw-bold text-success"><?= $pct ?>%</span>
                                        </div>
                                    </td>
                                    <td class="text-end py-3">
                                        <?php if($row['done']): ?>
                                            <span class="badge-status text-success bg-success bg-opacity-10">DONE</span>
                                        <?php else: ?>
                                            <span class="badge-status text-primary bg-primary bg-opacity-10">ACTIVE</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="4" class="text-center py-5 text-white-50">No goals set for today yet.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>