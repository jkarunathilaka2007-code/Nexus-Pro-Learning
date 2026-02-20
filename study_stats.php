<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// --- 1. DATA CALCULATIONS ---
$done_res = mysqli_query($conn, "SELECT COUNT(*) as total_done FROM completed_questions WHERE user_id = '$user_id'");
$done_count = mysqli_fetch_assoc($done_res)['total_done'] ?? 0;

$total_pool = 0;
$tracker_res = mysqli_query($conn, "SELECT start_year, end_year, question_count FROM trackers WHERE user_id = '$user_id'");
while ($t_row = mysqli_fetch_assoc($tracker_res)) {
    $years = ((int)$t_row['end_year'] - (int)$t_row['start_year']) + 1;
    $total_pool += ($years * (int)$t_row['question_count']);
}
$progress_pct = ($total_pool > 0) ? round(($done_count / $total_pool) * 100, 1) : 0;
$remaining_pool = ($total_pool - $done_count) > 0 ? ($total_pool - $done_count) : 0;

// Goals
$goals_res = mysqli_query($conn, "SELECT COUNT(*) as total_goals, SUM(CASE WHEN done = 1 THEN 1 ELSE 0 END) as completed_goals FROM goals WHERE user_id = '$user_id'");
$g_data = mysqli_fetch_assoc($goals_res);
$total_goals = $g_data['total_goals'] ?? 0;
$completed_goals = $g_data['completed_goals'] ?? 0;
$pending_goals = $total_goals - $completed_goals;

// Subjects
$subjects_labels = []; $subjects_done = [];
$sub_res = mysqli_query($conn, "SELECT subject, COUNT(*) as done FROM completed_questions WHERE user_id = '$user_id' GROUP BY subject");
while ($s_row = mysqli_fetch_assoc($sub_res)) {
    $subjects_labels[] = ucfirst($s_row['subject']);
    $subjects_done[] = $s_row['done'];
}

$hard_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM completed_questions WHERE user_id = '$user_id' AND is_hard = 1"))['c'] ?? 0;
$formula_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM user_formulas WHERE user_id = '$user_id'"))['c'] ?? 0;
$note_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM question_notes WHERE user_id = '$user_id'"))['c'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nexus Analytics | Dark Edition</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap');
        
        :root {
            --bg-dark: #0f172a;
            --card-dark: #1e293b;
            --text-main: #f1f5f9;
            --text-muted: #94a3b8;
            --primary: #6366f1;
            --accent: #22d3ee;
        }

        body { 
            background: var(--bg-dark); 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            color: var(--text-main);
            overflow-x: hidden;
        }

        /* Glass Cards for Dark Mode */
        .glass-card { 
            background: var(--card-dark); 
            border-radius: 28px; 
            border: 1px solid rgba(255,255,255,0.05);
            box-shadow: 0 10px 30px rgba(0,0,0,0.2); 
            transition: 0.4s ease; 
            padding: 25px; 
        }
        
        .glass-card:hover { transform: translateY(-5px); border-color: rgba(99, 102, 241, 0.3); }

        .hero-banner { 
            background: linear-gradient(135deg, #4338ca 0%, #7e22ce 100%); 
            color: white; 
            border-radius: 35px; 
            padding: 45px; 
            margin-bottom: 30px; 
            box-shadow: 0 20px 50px rgba(0,0,0,0.3);
        }

        .stat-val { font-size: 3.5rem; font-weight: 800; line-height: 1; }
        .text-neon { color: var(--accent); text-shadow: 0 0 10px rgba(34, 211, 238, 0.5); }
        .stat-icon { width: 50px; height: 50px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; }
        
        /* Progress Ring Center Fix */
        .chart-box { position: relative; height: 260px; width: 100%; }
        
        .nav-link-custom {
            background: rgba(255,255,255,0.1);
            color: white;
            border-radius: 50px;
            padding: 8px 20px;
            text-decoration: none;
            font-weight: 600;
            transition: 0.3s;
        }
        .nav-link-custom:hover { background: white; color: var(--bg-dark); }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-5 animate__animated animate__fadeIn">
        <div>
            <h2 class="fw-800 mb-0">Nexus <span class="text-primary">Intelligence</span></h2>
            <p class="text-muted small fw-bold">SYSTEM STATS v3.0</p>
        </div>
        <a href="index.php" class="nav-link-custom"><i class="fa fa-home me-2"></i>Exit Hub</a>
    </div>

    <div class="hero-banner animate__animated animate__fadeInDown">
        <div class="row align-items-center">
            <div class="col-md-8">
                <span class="badge bg-dark bg-opacity-50 text-white mb-3 px-3 py-2 rounded-pill fw-bold">OVERALL MASTERY</span>
                <div class="stat-val mb-2"><?= $progress_pct ?>%</div>
                <p class="fs-5 text-white-50 fw-600">Syllabus Completion Tracked</p>
                
                <div class="row g-3 mt-4">
                    <div class="col-auto">
                        <div class="bg-black bg-opacity-30 p-3 rounded-4 border border-white border-opacity-10">
                            <small class="text-white-50 d-block mb-1">Completed</small>
                            <span class="fs-4 fw-800 text-white"><?= $done_count ?></span>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="bg-black bg-opacity-30 p-3 rounded-4 border border-white border-opacity-10">
                            <small class="text-white-50 d-block mb-1">Total Pool</small>
                            <span class="fs-4 fw-800 text-neon"><?= $total_pool ?></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 text-center d-none d-md-block">
                <i class="fa fa-brain fa-10x opacity-10"></i>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-lg-4 animate__animated animate__fadeInUp" style="animation-delay: 0.1s;">
            <div class="glass-card text-center">
                <h6 class="text-muted fw-bold mb-4">SYLLABUS RATIO</h6>
                <div class="chart-box">
                    <canvas id="syllabusChart"></canvas>
                </div>
                <p class="mt-4 mb-0 small text-white-50">Mastery vs Remaining</p>
            </div>
        </div>

        <div class="col-lg-4 animate__animated animate__fadeInUp" style="animation-delay: 0.2s;">
            <div class="glass-card text-center">
                <h6 class="text-muted fw-bold mb-4">DAILY GOALS</h6>
                <div class="chart-box">
                    <canvas id="goalChart"></canvas>
                </div>
                <p class="mt-4 mb-0 small text-white-50">Completed: <?= $completed_goals ?> / Total: <?= $total_goals ?></p>
            </div>
        </div>

        <div class="col-lg-4 animate__animated animate__fadeInUp" style="animation-delay: 0.3s;">
            <div class="glass-card text-center">
                <h6 class="text-muted fw-bold mb-4">SUBJECT LOAD</h6>
                <div class="chart-box">
                    <canvas id="subjectPie"></canvas>
                </div>
                <p class="mt-4 mb-0 small text-white-50"><?= count($subjects_labels) ?> Subjects Tracked</p>
            </div>
        </div>
    </div>

    <div class="row g-4 animate__animated animate__fadeInUp">
        <div class="col-6 col-md-3">
            <div class="glass-card p-3 d-flex align-items-center">
                <div class="stat-icon bg-danger text-white me-3"><i class="fa fa-skull"></i></div>
                <div><h4 class="fw-800 mb-0"><?= $hard_count ?></h4><small class="text-muted">Hard Qs</small></div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="glass-card p-3 d-flex align-items-center">
                <div class="stat-icon bg-info text-white me-3"><i class="fa fa-flask"></i></div>
                <div><h4 class="fw-800 mb-0"><?= $formula_count ?></h4><small class="text-muted">Formulas</small></div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="glass-card p-3 d-flex align-items-center">
                <div class="stat-icon bg-warning text-white me-3"><i class="fa fa-sticky-note"></i></div>
                <div><h4 class="fw-800 mb-0"><?= $note_count ?></h4><small class="text-muted">Notes</small></div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="glass-card p-3 d-flex align-items-center">
                <div class="stat-icon bg-primary text-white me-3"><i class="fa fa-fire"></i></div>
                <div><h4 class="fw-800 mb-0"><?= $progress_pct ?>%</h4><small class="text-muted">Complete</small></div>
            </div>
        </div>
    </div>
</div>

<script>
    // Global Chart Defaults for Dark Mode
    Chart.defaults.color = '#94a3b8';
    Chart.defaults.font.family = 'Plus Jakarta Sans';

    const chartOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { 
            legend: { 
                position: 'bottom',
                labels: { color: '#f1f5f9', padding: 20, font: { size: 11, weight: 'bold' } }
            } 
        },
        cutout: '75%',
        animation: { duration: 2000, easing: 'easeOutQuart' }
    };

    // 1. Syllabus Doughnut
    new Chart(document.getElementById('syllabusChart'), {
        type: 'doughnut',
        data: {
            labels: ['Done', 'Left'],
            datasets: [{
                data: [<?= $done_count ?>, <?= $remaining_pool ?>],
                backgroundColor: ['#6366f1', 'rgba(255,255,255,0.05)'],
                borderWidth: 0,
                hoverOffset: 10
            }]
        },
        options: chartOptions
    });

    // 2. Goal Pie
    new Chart(document.getElementById('goalChart'), {
        type: 'pie',
        data: {
            labels: ['Done', 'Pending'],
            datasets: [{
                data: [<?= $completed_goals ?>, <?= $pending_goals ?>],
                backgroundColor: ['#22d3ee', 'rgba(255,255,255,0.05)'],
                borderWidth: 0
            }]
        },
        options: { ...chartOptions, cutout: '0%' }
    });

    // 3. Subject Breakdown
    new Chart(document.getElementById('subjectPie'), {
        type: 'doughnut',
        data: {
            labels: <?= json_encode($subjects_labels) ?>,
            datasets: [{
                data: <?= json_encode($subjects_done) ?>,
                backgroundColor: ['#818cf8', '#f472b6', '#fbbf24', '#2dd4bf', '#a78bfa'],
                borderWidth: 2,
                borderColor: '#1e293b'
            }]
        },
        options: chartOptions
    });
</script>

</body>
</html>