<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include 'db.php';

if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }
$current_user_id = $_SESSION['user_id'];

$rank_query = "
    SELECT u.id, u.name, u.profile_image, u.exam_year, 
           COUNT(cq.id) as solved_count
    FROM users u
    LEFT JOIN completed_questions cq ON u.id = cq.user_id
    GROUP BY u.id
    ORDER BY solved_count DESC
";
$result = mysqli_query($conn, $rank_query);

$rank_list = [];
$my_rank = 0;
$counter = 1;
if($result) {
    while($row = mysqli_fetch_assoc($result)) {
        if ($row['id'] == $current_user_id) { $my_rank = $counter; }
        $rank_list[] = $row;
        $counter++;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Global Leaderboard | Nexus Pro</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        :root { 
            --vision-bg: #030518; --accent-blue: #0075ff; 
            --gold: #ffbc00; --silver: #b4b4b4; --bronze: #cd7f32;
            --glass: linear-gradient(127.09deg, rgba(6, 11, 40, 0.94) 19.41%, rgba(10, 14, 35, 0.49) 76.65%);
        }

        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background: var(--vision-bg); color: white; margin: 0; 
            background-image: url('https://raw.githubusercontent.com/creativetimofficial/public-assets/master/vision-ui-dashboard-chakra/surface.png');
            background-size: cover; background-attachment: fixed; min-height: 100vh; overflow-x: hidden;
        }

        /* Ambient Glow Effects */
        .glow { position: fixed; width: 300px; height: 300px; border-radius: 50%; filter: blur(80px); z-index: -1; opacity: 0.2; }
        .glow-1 { top: -100px; left: -100px; background: var(--accent-blue); }
        .glow-2 { bottom: -100px; right: -100px; background: #6366f1; }

        .glass { background: var(--glass); backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 24px; }
        
        .top-nav { padding: 30px 40px; display: flex; align-items: center; justify-content: space-between; }
        .back-btn { color: white; text-decoration: none; font-weight: 700; font-size: 0.8rem; background: rgba(255,255,255,0.05); padding: 10px 20px; border-radius: 50px; border: 1px solid rgba(255,255,255,0.1); transition: 0.3s; }
        .back-btn:hover { background: var(--accent-blue); transform: translateX(-5px); color: white; }

        .container { max-width: 950px; position: relative; }

        /* Podium Design */
        .podium-container { display: flex; align-items: flex-end; justify-content: center; gap: 20px; margin: 60px 0; }
        .podium-card { 
            background: var(--glass); border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 30px; padding: 30px 20px; text-align: center; 
            position: relative; transition: 0.5s cubic-bezier(0.2, 0.8, 0.2, 1); width: 100%;
        }
        .podium-card:hover { transform: translateY(-15px); border-color: rgba(255,255,255,0.3); }
        
        .podium-1 { border-top: 5px solid var(--gold); order: 2; transform: scale(1.15); z-index: 5; background: linear-gradient(127.09deg, rgba(255, 188, 0, 0.1) 19.41%, rgba(10, 14, 35, 0.49) 76.65%); }
        .podium-2 { border-top: 5px solid var(--silver); order: 1; }
        .podium-3 { border-top: 5px solid var(--bronze); order: 3; }

        .avatar-container { position: relative; display: inline-block; margin-bottom: 15px; }
        .rank-img { width: 80px; height: 80px; object-fit: cover; border-radius: 50%; border: 3px solid rgba(255, 255, 255, 0.2); }
        .podium-1 .rank-img { width: 100px; height: 100px; border-color: var(--gold); }
        
        .crown-icon { position: absolute; top: -25px; left: 50%; transform: translateX(-50%) rotate(-10deg); font-size: 2.5rem; filter: drop-shadow(0 0 10px var(--gold)); }

        /* Standing List */
        .rank-item { 
            background: var(--glass); border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 20px; padding: 15px 25px; margin-bottom: 12px; 
            display: flex; align-items: center; transition: 0.3s;
        }
        .rank-item:hover { background: rgba(255,255,255,0.08); transform: scale(1.01); }
        .rank-item.my-row { border: 2px solid var(--accent-blue); background: rgba(0, 117, 255, 0.1); box-shadow: 0 0 20px rgba(0, 117, 255, 0.1); }

        .rank-num { width: 50px; font-weight: 900; font-size: 1.2rem; background: linear-gradient(to bottom, #fff, #666); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .user-img-sm { width: 45px; height: 45px; object-fit: cover; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1); }
        
        .score-pill { 
            background: rgba(0, 117, 255, 0.1); color: var(--accent-blue); 
            font-weight: 800; padding: 8px 20px; border-radius: 15px; 
            border: 1px solid rgba(0, 117, 255, 0.2);
        }

        .status-tag { font-size: 9px; text-transform: uppercase; letter-spacing: 1px; font-weight: 800; padding: 2px 8px; border-radius: 4px; margin-top: 5px; display: inline-block; }
        .tag-expert { background: rgba(255, 188, 0, 0.2); color: var(--gold); }
        .tag-pro { background: rgba(0, 117, 255, 0.2); color: var(--accent-blue); }

        @media (max-width: 768px) {
            .podium-container { flex-direction: column; align-items: center; gap: 40px; margin-top: 80px; }
            .podium-1 { transform: scale(1.05); }
        }
    </style>
</head>
<body>

<div class="glow glow-1"></div>
<div class="glow glow-2"></div>

<div class="top-nav">
    <a href="dashboard.php" class="back-btn"><i class="fa fa-arrow-left me-2"></i> Dashboard</a>
    <div class="glass px-3 py-2 text-center" style="border-radius: 15px;">
        <small class="text-muted d-block fw-bold" style="font-size: 9px;">GLOBAL RANK</small>
        <span class="fw-800 text-white">#<?= $my_rank ?></span>
    </div>
</div>

<div class="container mt-4">
    <div class="text-center mb-5">
        <h1 class="fw-800" style="letter-spacing: -1px; font-size: 2.5rem;">The Hall of <span style="color:var(--accent-blue)">Champions</span></h1>
        <p class="text-muted">Push your limits and dominate the leaderboard!</p>
    </div>

    <div class="podium-container">
        <?php if(isset($rank_list[1])): ?>
        <div class="podium-card podium-2">
            <div class="avatar-container">
                <i class="fa fa-medal medal-icon crown-icon" style="color: var(--silver); font-size: 1.5rem; top: -15px;"></i>
                <img src="<?= !empty($rank_list[1]['profile_image']) ? $rank_list[1]['profile_image'] : 'default.png' ?>" class="rank-img">
            </div>
            <h6 class="fw-800 mb-1"><?= htmlspecialchars($rank_list[1]['name']) ?></h6>
            <span class="status-tag tag-pro">Pro Learner</span><br>
            <div class="score-pill d-inline-block mt-3"><?= $rank_list[1]['solved_count'] ?> Qs</div>
        </div>
        <?php endif; ?>

        <?php if(isset($rank_list[0])): ?>
        <div class="podium-card podium-1">
            <div class="avatar-container">
                <i class="fa fa-crown crown-icon" style="color: var(--gold);"></i>
                <img src="<?= !empty($rank_list[0]['profile_image']) ? $rank_list[0]['profile_image'] : 'default.png' ?>" class="rank-img shadow-lg">
            </div>
            <h5 class="fw-900 mb-1"><?= htmlspecialchars($rank_list[0]['name']) ?></h5>
            <span class="status-tag tag-expert">Elite Master</span><br>
            <div class="score-pill d-inline-block mt-3" style="background: var(--gold); color: #000; border: none;"><?= $rank_list[0]['solved_count'] ?> SOLVED</div>
        </div>
        <?php endif; ?>

        <?php if(isset($rank_list[2])): ?>
        <div class="podium-card podium-3">
            <div class="avatar-container">
                <i class="fa fa-medal medal-icon crown-icon" style="color: var(--bronze); font-size: 1.5rem; top: -15px;"></i>
                <img src="<?= !empty($rank_list[2]['profile_image']) ? $rank_list[2]['profile_image'] : 'default.png' ?>" class="rank-img">
            </div>
            <h6 class="fw-800 mb-1"><?= htmlspecialchars($rank_list[2]['name']) ?></h6>
            <span class="status-tag tag-pro">Rising Star</span><br>
            <div class="score-pill d-inline-block mt-3"><?= $rank_list[2]['solved_count'] ?> Qs</div>
        </div>
        <?php endif; ?>
    </div>

    <div class="mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4 px-2">
            <h5 class="fw-800 mb-0"><i class="fa fa-list-ol me-2 text-primary"></i> Global Standings</h5>
            <small class="text-muted fw-bold"><?= count($rank_list) ?> Active Users</small>
        </div>
        
        <?php foreach($rank_list as $index => $learner): ?>
            <div class="rank-item <?= ($learner['id'] == $current_user_id) ? 'my-row' : '' ?>">
                <div class="rank-num">
                    <?= ($index + 1 < 10) ? '0'.($index+1) : ($index+1) ?>
                </div>
                
                <div class="user-info flex-grow-1 d-flex align-items-center gap-3">
                    <img src="<?= !empty($learner['profile_image']) ? $learner['profile_image'] : 'default.png' ?>" class="user-img-sm">
                    <div>
                        <h6 class="fw-bold mb-0 small"><?= htmlspecialchars($learner['name']) ?></h6>
                        <small class="text-muted" style="font-size: 10px;"><?= htmlspecialchars($learner['exam_year']) ?> Batch</small>
                    </div>
                </div>
                
                <div class="score-pill py-1 px-3" style="font-size: 0.8rem;">
                    <?= $learner['solved_count'] ?> <small class="opacity-50">Pts</small>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>