<?php
session_start();
// Setting the timezone at the very top to ensure accuracy
date_default_timezone_set('Asia/Colombo');

include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$status = getDailyStatus($conn, $user_id);
$today_date = date('Y-m-d');

// --- Advanced Analytics ---

// 1. Total questions added today
$count_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM user_revisions WHERE user_id = '$user_id' AND DATE(created_at) = '$today_date'");
$total_today = mysqli_fetch_assoc($count_query)['total'];

// 2. Most active subject today (The one with most questions added)
$active_sub_query = mysqli_query($conn, "SELECT subject_name FROM user_revisions WHERE user_id = '$user_id' AND DATE(created_at) = '$today_date' GROUP BY subject_name ORDER BY COUNT(*) DESC LIMIT 1");
$active_subject = mysqli_fetch_assoc($active_sub_query)['subject_name'] ?? 'None';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity Insights | Nexus Pro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap');
        
        :root { 
            --bg: #030712; 
            --card: #111827; 
            --accent: #818cf8; /* High Contrast Indigo */
            --success: #10b981; 
            --warning: #fbbf24; 
            --text-main: #f9fafb;
            --text-dim: #9ca3af;
        }
        
        body { 
            background-color: var(--bg); 
            color: var(--text-main); 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            min-height: 100vh;
        }

        /* Glassmorphism Summary Box */
        .analytics-card { 
            background: linear-gradient(145deg, rgba(31, 41, 55, 0.8), rgba(17, 24, 39, 0.8));
            backdrop-filter: blur(12px); 
            border: 1px solid rgba(255, 255, 255, 0.1); 
            border-radius: 28px; 
            padding: 25px; 
            margin-bottom: 30px; 
            box-shadow: 0 20px 40px rgba(0,0,0,0.4);
        }

        .stat-label { color: var(--text-dim); font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1.5px; }
        .stat-value { font-size: 1.6rem; font-weight: 800; margin-top: 5px; color: #fff; }

        /* Notification Styling */
        .notif-item {
            display: flex;
            align-items: center;
            padding: 20px;
            border-radius: 22px;
            background: #111827; 
            border: 1px solid rgba(255, 255, 255, 0.06);
            margin-bottom: 15px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .notif-item:hover { 
            background: #1f2937;
            transform: translateX(10px);
            border-color: var(--accent);
        }

        .notif-success { border-left: 6px solid var(--success); }
        .notif-info { border-left: 6px solid var(--accent); }
        .notif-warning { border-left: 6px solid var(--warning); }

        .icon-box {
            min-width: 52px; height: 52px;
            border-radius: 16px;
            display: flex; align-items: center; justify-content: center;
            margin-right: 20px;
            font-size: 1.4rem;
        }

        .notif-title { color: #ffffff; font-weight: 700; font-size: 1.1rem; margin-bottom: 2px; }
        .notif-desc { color: var(--text-dim); font-size: 0.9rem; line-height: 1.4; }
        .notif-time { color: var(--accent); font-size: 0.8rem; font-weight: 700; background: rgba(129, 140, 248, 0.1); padding: 4px 10px; border-radius: 8px; }

        .empty-state { padding: 80px 20px; text-align: center; color: var(--text-dim); }
        .btn-back { background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: #fff; padding: 10px 20px; border-radius: 15px; text-decoration: none; font-weight: 600; transition: 0.3s; }
        .btn-back:hover { background: rgba(255,255,255,0.1); color: var(--accent); }
    </style>
</head>
<body>

<div class="container py-5" style="max-width: 800px;">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-800 m-0" style="letter-spacing: -1px;">Activity Insights</h2>
            <p style="color: var(--text-dim); margin: 0;">Synchronized at <?= date('h:i A') ?></p>
        </div>
        <a href="revision.php" class="btn-back">
            <i class="fa fa-chevron-left me-2"></i> Dashboard
        </a>
    </div>

    <div class="analytics-card">
        <div class="row text-center">
            <div class="col-4 border-end border-secondary border-opacity-25">
                <div class="stat-label">Added Today</div>
                <div class="stat-value text-accent"><?= sprintf("%02d", $total_today) ?></div>
            </div>
            <div class="col-4 border-end border-secondary border-opacity-25">
                <div class="stat-label">Top Focus</div>
                <div class="stat-value text-truncate px-2" style="font-size: 1.1rem; padding-top: 8px;">
                    <?= htmlspecialchars($active_subject) ?>
                </div>
            </div>
            <div class="col-4">
                <div class="stat-label">Daily Goal</div>
                <div class="stat-value <?= $status['is_complete'] ? 'text-success' : 'text-warning' ?>">
                    <?= $status['percentage'] ?>%
                </div>
            </div>
        </div>
    </div>

    <div class="notif-list">
        
        <?php if ($status['is_complete']): ?>
            <div class="notif-item notif-success">
                <div class="icon-box" style="background: rgba(16, 185, 129, 0.15); color: var(--success);">
                    <i class="fa-solid fa-trophy"></i>
                </div>
                <div>
                    <div class="notif-title">Daily Milestone Reached!</div>
                    <div class="notif-desc">System unlocked. You have fulfilled the requirements for all subjects today.</div>
                </div>
            </div>
        <?php else: ?>
            <div class="notif-item notif-warning">
                <div class="icon-box" style="background: rgba(251, 191, 36, 0.15); color: var(--warning);">
                    <i class="fa-solid fa-fire"></i>
                </div>
                <div>
                    <div class="notif-title">Action Required</div>
                    <div class="notif-desc">You are currently at <?= $status['percentage'] ?>%. Update <?= count($status['pending_subjects']) ?> more subjects to reach your goal.</div>
                </div>
            </div>
        <?php endif; ?>

        <?php 
        // Fetching recent activity for the feed
        $activities = mysqli_query($conn, "SELECT subject_name, MAX(created_at) as last_time FROM user_revisions WHERE user_id = '$user_id' AND DATE(created_at) = '$today_date' GROUP BY subject_name ORDER BY last_time DESC");
        
        if(mysqli_num_rows($activities) > 0):
            while($act = mysqli_fetch_assoc($activities)):
        ?>
            <div class="notif-item notif-info">
                <div class="icon-box" style="background: rgba(129, 140, 248, 0.15); color: var(--accent);">
                    <i class="fa-solid fa-layer-group"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between align-items-start mb-1">
                        <span class="notif-title"><?= htmlspecialchars($act['subject_name']) ?> Updated</span>
                        <span class="notif-time">
                            <i class="fa-regular fa-clock me-1"></i>
                            <?= date('h:i A', strtotime($act['last_time'])) ?>
                        </span>
                    </div>
                    <div class="notif-desc">New revision data has been successfully processed and stored in your hub.</div>
                </div>
            </div>
        <?php endwhile; else: ?>
             <div class="empty-state">
                <i class="fa-solid fa- inbox fa-3x mb-3 opacity-25"></i>
                <h5 class="fw-bold">No Activity Yet</h5>
                <p class="small">Start adding questions to see your daily insights here.</p>
            </div>
        <?php endif; ?>

    </div>

    <div class="mt-5 p-4 rounded-4 border border-secondary border-opacity-10" style="background: rgba(255,255,255,0.02);">
        <div class="d-flex gap-3">
            <i class="fa-solid fa-wand-magic-sparkles text-accent fs-4"></i>
            <div>
                <div class="fw-bold small text-uppercase mb-1" style="letter-spacing: 1px;">Pro Tip</div>
                <p class="m-0 small text-dim">Using the "No Questions Today" feature helps maintain your daily streak even on busy days. Keep the momentum going!</p>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>