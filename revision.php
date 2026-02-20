<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// 1. Users table එකෙන් විෂයන් ලබා ගැනීම
$user_query = mysqli_query($conn, "SELECT subjects FROM users WHERE id = '$user_id'");
$user_row = mysqli_fetch_assoc($user_query);
$my_subjects = !empty($user_row['subjects']) ? array_map('trim', explode(',', $user_row['subjects'])) : [];

// 2. එක් එක් විෂයට අදාළ ප්‍රශ්න ගණන ලබා ගැනීම
$counts_query = mysqli_query($conn, "SELECT subject_name, COUNT(*) as total FROM user_revisions WHERE user_id = '$user_id' GROUP BY subject_name");
$subject_counts = [];
foreach($my_subjects as $s) { $subject_counts[$s] = 0; } // Default 0
while ($row = mysqli_fetch_assoc($counts_query)) {
    $subject_counts[$row['subject_name']] = $row['total'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nexus | Revision Hub</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">

    <style>
        :root { --bg: #020617; --glass: rgba(15, 23, 42, 0.7); --border: rgba(255, 255, 255, 0.1); --accent: #6366f1; }
        
        body { 
            background: radial-gradient(circle at top right, #1e1b4b, #020617); 
            color: #f8fafc; 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            min-height: 100vh; 
            padding-bottom: 50px; 
        }
        
        .header-section { padding: 40px 0 20px; text-align: center; }
        .main-title { font-weight: 800; font-size: clamp(2rem, 5vw, 2.8rem); background: linear-gradient(to bottom, #fff, #94a3b8); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        
        .btn-manage {
            background: rgba(255, 255, 255, 0.05); border: 1px solid var(--border);
            color: #94a3b8; padding: 10px 20px; border-radius: 50px; text-decoration: none;
            font-size: 0.85rem; transition: 0.3s; display: inline-flex; align-items: center; gap: 8px;
        }
        .btn-manage:hover { background: var(--accent); color: white; border-color: var(--accent); }

        /* Card Styling */
        .subject-card { 
            background: var(--glass); 
            backdrop-filter: blur(12px); 
            border: 1px solid var(--border); 
            border-radius: 24px; 
            padding: 25px; 
            transition: all 0.3s ease; 
            height: 100%; 
            display: flex; 
            flex-direction: column;
        }
        .subject-card:hover { transform: translateY(-5px); border-color: rgba(99, 102, 241, 0.4); }

        .qty-display { 
            font-size: 3rem; font-weight: 800; 
            background: linear-gradient(135deg, #fff 30%, var(--accent)); 
            -webkit-background-clip: text; -webkit-text-fill-color: transparent; 
        }

        /* Form Inputs */
        .input-group-custom { background: rgba(0, 0, 0, 0.3); border-radius: 12px; padding: 2px; border: 1px solid var(--border); margin-bottom: 15px; }
        .qty-input { background: transparent !important; border: none !important; color: white !important; text-align: center; font-weight: 700; }
        
        .btn-start { background: var(--accent); border: none; border-radius: 14px; padding: 12px; font-weight: 700; color: white; transition: 0.3s; display: flex; align-items: center; justify-content: center; gap: 10px; }
        .btn-start:hover:not(:disabled) { background: #4f46e5; box-shadow: 0 5px 15px rgba(99, 102, 241, 0.3); }

        /* Analytics Text Fix */
        .stat-label { color: #94a3b8; font-weight: 600; font-size: 0.95rem; }
        .stat-value { color: #ffffff; font-weight: 800; font-size: 1rem; }
        .stat-row { display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid rgba(255,255,255,0.05); }

        /* Floating Button */
        .btn-add-floating { 
            position: fixed; bottom: 25px; right: 25px; width: 55px; height: 55px; 
            background: #fff; color: #020617; border-radius: 18px; 
            display: flex; align-items: center; justify-content: center; 
            font-size: 1.4rem; box-shadow: 0 10px 20px rgba(0,0,0,0.4); 
            transition: 0.3s; text-decoration: none; z-index: 1000; 
        }
        .btn-add-floating:hover { background: var(--accent); color: white; transform: rotate(90deg); }

        @media (max-width: 576px) {
            .header-section { padding: 30px 0 15px; }
            .subject-card { padding: 20px; }
        }
    </style>
</head>
<body>

<div class="container py-4">
    <header class="header-section">
        <h1 class="main-title">Revision Inventory</h1>
        <p class="text-muted small mb-4">Track progress and start your next session</p>
        
        <a href="revision_info.php" class="btn-manage shadow-sm">
            <i class="fa-solid fa-database"></i> Manage Database
        </a>
    </header>

    <div class="row g-3 g-md-4 mb-5">
        <?php foreach ($my_subjects as $sub): 
            $max_qty = isset($subject_counts[$sub]) ? $subject_counts[$sub] : 0;
        ?>
            <div class="col-xl-3 col-lg-4 col-sm-6">
                <div class="subject-card shadow-sm">
                    <div class="text-uppercase text-muted fw-bold x-small" style="font-size: 0.7rem; letter-spacing: 1px;"><?= htmlspecialchars($sub) ?></div>
                    <div class="qty-display"><?= sprintf("%02d", $max_qty) ?></div>
                    <p class="small text-muted mb-4">Questions Bank</p>

                    <form action="study_session.php" method="GET" class="mt-auto">
                        <input type="hidden" name="subject" value="<?= htmlspecialchars($sub) ?>">
                        <div class="text-start mb-2">
                            <label class="small text-white-50 ms-2 mb-1">Session Limit</label>
                            <div class="input-group-custom">
                                <input type="number" name="limit" class="form-control qty-input" 
                                       min="1" max="<?= $max_qty ?>" value="<?= ($max_qty > 0) ? min(10, $max_qty) : 0 ?>" 
                                       <?= ($max_qty == 0) ? 'disabled' : '' ?>>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-start w-100" <?= ($max_qty == 0) ? 'disabled' : '' ?>>
                            <span>Start</span> <i class="fa-solid fa-play small"></i>
                        </button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="subject-card border-secondary">
                <h5 class="text-white fw-bold mb-4">
                    <i class="fa-solid fa-chart-bar me-2" style="color: var(--accent);"></i>
                    Distribution
                </h5>
                <div style="height: 300px; position: relative;">
                    <canvas id="revisionChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="subject-card border-secondary">
                <h5 class="text-white fw-bold mb-4">
                    <i class="fa-solid fa-bolt me-2 text-warning"></i>
                    Analytics
                </h5>
                
                <div class="stat-row">
                    <span class="stat-label">Total Questions:</span>
                    <span class="stat-value"><?= array_sum($subject_counts) ?></span>
                </div>
                
                <div class="stat-row">
                    <span class="stat-label">Active Subjects:</span>
                    <span class="stat-value"><?= count($my_subjects) ?></span>
                </div>
                
                <div class="stat-row">
                    <span class="stat-label">Top Subject:</span>
                    <span class="stat-value" style="color: #818cf8;">
                        <?php 
                            if(!empty($subject_counts) && array_sum($subject_counts) > 0) {
                                echo htmlspecialchars(array_search(max($subject_counts), $subject_counts));
                            } else { echo "N/A"; }
                        ?>
                    </span>
                </div>

                <div class="mt-4 p-3 rounded-4" style="background: rgba(99, 102, 241, 0.1); border: 1px solid rgba(99, 102, 241, 0.2);">
                    <small style="color: #818cf8; font-weight: 800; text-transform: uppercase; font-size: 0.7rem; display: block; margin-bottom: 5px;">Nexus Insight</small>
                    <p style="color: #cbd5e1; font-size: 0.8rem; margin-bottom: 0; line-height: 1.5;">
                        Focus on subjects with the lowest question counts to maintain a balanced study profile.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<a href="add_revision.php" class="btn-add-floating shadow-lg"><i class="fa-solid fa-plus"></i></a>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const ctx = document.getElementById('revisionChart').getContext('2d');
    const labels = <?= json_encode(array_keys($subject_counts)) ?>;
    const dataValues = <?= json_encode(array_values($subject_counts)) ?>;

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Questions',
                data: dataValues,
                backgroundColor: 'rgba(99, 102, 241, 0.4)',
                borderColor: '#6366f1',
                borderWidth: 2,
                borderRadius: 8,
                hoverBackgroundColor: '#6366f1'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(255, 255, 255, 0.05)' },
                    ticks: { color: '#94a3b8', font: { family: 'Plus Jakarta Sans', size: 10 } }
                },
                x: {
                    grid: { display: false },
                    ticks: { color: '#94a3b8', font: { family: 'Plus Jakarta Sans', size: 10 } }
                }
            },
            plugins: {
                legend: { display: false }
            }
        }
    });
</script>

</body>
</html>