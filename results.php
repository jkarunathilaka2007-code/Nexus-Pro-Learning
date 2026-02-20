<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include 'db.php';

if (!isset($_SESSION['user_id'])) { 
    header("Location: login.php"); 
    exit(); 
}

$user_id = $_SESSION['user_id'];
$room_id = isset($_SESSION['last_room_id']) ? $_SESSION['last_room_id'] : 0;

if ($room_id == 0) {
    die("<div style='text-align:center; color:white; padding:100px;'><h2>No active session found</h2><a href='dashboard.php' class='btn btn-primary'>Go Home</a></div>");
}

// 1. මගේ ලකුණු ලබා ගැනීම
$my_res = mysqli_query($conn, "SELECT * FROM room_results WHERE room_id = '$room_id' AND user_id = '$user_id' ORDER BY id DESC LIMIT 1");
$my_data = mysqli_fetch_assoc($my_res);

// 2. Leaderboard එක ලබා ගැනීම
$all_res = mysqli_query($conn, "SELECT rr.*, u.name, u.profile_image 
                                FROM room_results rr 
                                JOIN users u ON rr.user_id = u.id 
                                WHERE rr.room_id = '$room_id' 
                                ORDER BY rr.score DESC, rr.id ASC");

if (!$my_data) {
    die("<div style='text-align:center; color:white; padding:100px;'><h2>Result Not Found</h2><a href='dashboard.php'>Home</a></div>");
}

// ලකුණු ප්‍රතිශතය ගණනය කිරීම
$percentage = ($my_data['total_questions'] > 0) ? round(($my_data['score'] / $my_data['total_questions']) * 100) : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Global Ranking | Nexus Battle Arena</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body { background: #0b0e14; color: #e2e8f0; font-family: 'Plus Jakarta Sans', sans-serif; overflow-x: hidden; }
        
        .stat-card {
            background: linear-gradient(145deg, #1a1f2e, #111522);
            border-radius: 24px;
            padding: 40px 30px;
            border: 1px solid rgba(255,255,255,0.08);
            text-align: center;
            box-shadow: 0 20px 40px rgba(0,0,0,0.4);
            position: relative;
            overflow: hidden;
        }

        /* සහතික පත්‍රය සඳහා විශේෂ Style එක */
        .cert-banner {
            background: linear-gradient(90deg, #b49410, #e6c646);
            color: #000;
            padding: 15px;
            border-radius: 15px;
            margin-top: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.02); }
            100% { transform: scale(1); }
        }

        .leaderboard-box {
            background: #111522;
            border-radius: 24px;
            overflow: hidden;
            border: 1px solid rgba(255,255,255,0.05);
            margin-top: 30px;
        }

        .table { color: #e2e8f0; margin-bottom: 0; vertical-align: middle; }
        .table td { border-top: 1px solid rgba(255,255,255,0.05); padding: 15px 20px; }

        .rank-badge {
            width: 32px; height: 32px;
            display: flex; align-items: center; justify-content: center;
            border-radius: 8px; font-weight: 800; font-size: 0.85rem;
        }
        .rank-1 { background: #ffd700; color: #000; }
        .rank-2 { background: #cbd5e1; color: #000; }
        .rank-3 { background: #cd7f32; color: #000; }

        .my-score-row { background: rgba(59, 130, 246, 0.12) !important; border-left: 4px solid #3b82f6; }
        .player-img { width: 35px; height: 35px; border-radius: 10px; object-fit: cover; }
        .score-pill { background: rgba(59, 130, 246, 0.1); padding: 6px 15px; border-radius: 50px; font-weight: 800; color: #60a5fa; border: 1px solid rgba(96, 165, 250, 0.2); }
        .btn-action { padding: 12px 30px; border-radius: 15px; font-weight: 700; transition: 0.3s; }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            
            <div class="stat-card">
                <span class="badge bg-success mb-3 px-3 py-2 rounded-pill">BATTLE FINISHED</span>
                <h4 class="text-muted small fw-bold text-uppercase">Your Final Score</h4>
                <h1 class="display-3 fw-800 mb-0"><?= $my_data['score'] ?> <span class="fs-4 text-muted">/ <?= $my_data['total_questions'] ?></span></h1>
                <p class="text-info fw-bold mt-2"><?= $percentage ?>% Accuracy</p>
                
                <?php if($percentage >= 80): ?>
                <div class="cert-banner">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-medal fa-2x me-3"></i>
                        <div class="text-start">
                            <h6 class="mb-0 fw-bold">Achievement Unlocked!</h6>
                            <small>You earned an Official E-Certificate</small>
                        </div>
                    </div>
                    <a href="certificate.php?res_id=<?= $my_data['id'] ?>" target="_blank" class="btn btn-dark btn-sm rounded-pill px-4 fw-bold">VIEW CERTIFICATE</a>
                </div>
                <?php endif; ?>

                <div class="d-flex justify-content-center gap-3 mt-4">
                    <a href="review.php?res_id=<?= $my_data['id'] ?>" class="btn btn-primary btn-action"><i class="fas fa-search me-2"></i>Review Paper</a>
                    <a href="dashboard.php" class="btn btn-outline-light btn-action">Exit Arena</a>
                </div>
            </div>

            <div class="leaderboard-box">
                <div class="p-4 border-bottom border-secondary border-opacity-10">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-trophy text-warning me-2"></i>Live Rankings</h5>
                </div>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr class="text-muted small text-uppercase">
                                <th class="ps-4">Place</th>
                                <th>Participant</th>
                                <th class="text-center">Accuracy</th>
                                <th class="text-end pe-4">Score</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $count = 0; $display_rank = 0; $prev_score = -1;
                            while($row = mysqli_fetch_assoc($all_res)): 
                                $count++;
                                $current_score = $row['score'];
                                if ($current_score !== $prev_score) { $display_rank = $count; }
                                $rank_class = ($display_rank <= 3) ? 'rank-'.$display_rank : 'bg-secondary bg-opacity-25';
                                $prev_score = $current_score;
                                $row_acc = ($row['total_questions'] > 0) ? round(($row['score'] / $row['total_questions']) * 100) : 0;
                            ?>
                            <tr class="<?= ($row['user_id'] == $user_id) ? 'my-score-row' : '' ?>">
                                <td class="ps-4">
                                    <div class="rank-badge <?= $rank_class ?>"><?= $display_rank ?></div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="<?= !empty($row['profile_image']) ? $row['profile_image'] : 'https://cdn-icons-png.flaticon.com/512/149/149071.png' ?>" class="player-img me-3">
                                        <div class="fw-bold"><?= htmlspecialchars($row['name']) ?></div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="small fw-bold"><?= $row_acc ?>%</div>
                                    <div class="progress mt-1" style="height: 4px; width: 80px; margin: auto; background: rgba(255,255,255,0.05);">
                                        <div class="progress-bar bg-info" style="width: <?= $row_acc ?>%"></div>
                                    </div>
                                </td>
                                <td class="text-end pe-4">
                                    <span class="score-pill"><?= $current_score ?></span>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

</body>
</html>