<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }
$user_id = $_SESSION['user_id'];

// --- 1. SESSION DELETE LOGIC (කලින් තිබුණු විදිහමයි) ---
if (isset($_GET['delete'])) {
    $session_id = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM study_sessions WHERE id = '$session_id' AND user_id = '$user_id'");
    header("Location: history.php?msg=deleted");
    exit();
}

// --- 2. ANALYTICS QUERIES (Charts සඳහා) ---

// A. Subject Distribution
$sub_time_query = mysqli_query($conn, "SELECT subject, SUM(TIME_TO_SEC(duration))/3600 as total_hours FROM study_sessions WHERE user_id = '$user_id' GROUP BY subject");
$sub_labels = []; $sub_data = [];
while($r = @mysqli_fetch_assoc($sub_time_query)) {
    $sub_labels[] = $r['subject'];
    $sub_data[] = round($r['total_hours'], 2);
}

// B. Paper Type Distribution (අලුත් Smart Sorting Logic එක අනුව)
$paper_stats_query = mysqli_query($conn, "SELECT paper_type, COUNT(*) as count FROM user_revisions WHERE user_id = '$user_id' AND attempt_count > 0 GROUP BY paper_type");
$paper_labels = []; $paper_data = [];
while($r = @mysqli_fetch_assoc($paper_stats_query)) {
    $paper_labels[] = $r['paper_type'];
    $paper_data[] = $r['count'];
}

// C. Weekly Effort
$weekly_query = mysqli_query($conn, "SELECT DAYNAME(start_time) as day, SUM(TIME_TO_SEC(duration))/3600 as hours FROM study_sessions WHERE user_id = '$user_id' AND start_time >= DATE_SUB(NOW(), INTERVAL 7 DAY) GROUP BY DAYNAME(start_time)");
$week_labels = []; $week_data = [];
while($r = @mysqli_fetch_assoc($weekly_query)) {
    $week_labels[] = $r['day'];
    $week_data[] = round($r['hours'], 2);
}

// NEW: Average Accuracy per Subject
$acc_query = mysqli_query($conn, "SELECT subject_name, AVG((correct_answers/total_questions)*100) as avg_acc FROM exam_results WHERE user_id = '$user_id' GROUP BY subject_name");
$acc_labels = []; $acc_data = [];
if($acc_query) {
    while($r = mysqli_fetch_assoc($acc_query)) {
        $acc_labels[] = $r['subject_name'];
        $acc_data[] = round($r['avg_acc'], 1);
    }
}

// --- 3. FULL HISTORY LOGS ---
$history_query = mysqli_query($conn, "SELECT * FROM study_sessions WHERE user_id = '$user_id' ORDER BY start_time DESC");
$exam_history = mysqli_query($conn, "SELECT * FROM exam_results WHERE user_id = '$user_id' ORDER BY completed_at DESC LIMIT 10");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Advanced Analytics | Nexus Pro</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        :root { --primary: #6366f1; --accent: #06b6d4; --dark-bg: #0f172a; --card-bg: rgba(255, 255, 255, 0.03); --sidebar-bg: #0b1120; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: var(--dark-bg); color: #f8fafc; margin: 0; }
        .sidebar { height: 100vh; width: 260px; position: fixed; background: var(--sidebar-bg); border-right: 1px solid rgba(255, 255, 255, 0.05); z-index: 1050; transition: 0.3s; }
        .main-content { margin-left: 260px; padding: 40px; }
        .sidebar-link { padding: 14px 25px; display: block; color: #64748b; text-decoration: none; font-weight: 600; border-radius: 12px; margin: 5px 15px; }
        .sidebar-link.active { color: white; background: rgba(99, 102, 241, 0.1); }
        .glass-card { background: var(--card-bg); backdrop-filter: blur(15px); border-radius: 28px; border: 1px solid rgba(255, 255, 255, 0.08); padding: 25px; height: 100%; }
        .chart-container { position: relative; height: 260px; width: 100%; }
        .table-container { background: var(--card-bg); border-radius: 28px; border: 1px solid rgba(255, 255, 255, 0.08); overflow: hidden; margin-top: 30px; }
        .table { color: #f8fafc; margin-bottom: 0; }
        .table th { padding: 20px; color: #64748b; text-transform: uppercase; font-size: 0.8rem; }
        .table td { padding: 20px; border-bottom: 1px solid rgba(255, 255, 255, 0.03); }
        .subject-tag { background: rgba(99, 102, 241, 0.1); color: #818cf8; padding: 6px 14px; border-radius: 10px; font-weight: 700; font-size: 0.85rem; }
        .badge-accuracy { background: rgba(6, 182, 212, 0.1); color: #06b6d4; font-weight: 700; padding: 8px 16px; border-radius: 12px; }
        
        @media (max-width: 992px) { .sidebar { display: none; } .main-content { margin-left: 0; } }
    </style>
</head>
<body>

<div class="sidebar shadow">
    <div class="p-4 text-center">
        <h4 class="fw-800 mb-0">STUDY<span class="text-primary">NEXUS</span></h4>
    </div>
    <nav class="mt-4">
        <a href="dashboard.php" class="sidebar-link"><i class="fa fa-th-large me-3"></i> Dashboard</a>
        <a href="history.php" class="sidebar-link active"><i class="fa fa-chart-pie me-3"></i> Analytics</a>
        <a href="logout.php" class="sidebar-link text-danger mt-5"><i class="fa fa-sign-out-alt me-3"></i> Logout</a>
    </nav>
</div>

<div class="main-content">
    <h1 class="fw-800">Visual Insights</h1>
    <p class="text-muted">ඔයාගේ සියලුම දත්ත මෙතන සුරක්ෂිතව තියෙනවා.</p>

    <div class="row g-4 mb-5">
        <div class="col-xl-4 col-md-6">
            <div class="glass-card">
                <h6>Time per Subject</h6>
                <div class="chart-container"><canvas id="subPieChart"></canvas></div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6">
            <div class="glass-card">
                <h6>Accuracy per Subject</h6>
                <div class="chart-container"><canvas id="accBarChart"></canvas></div>
            </div>
        </div>
        <div class="col-xl-4 col-md-12">
            <div class="glass-card">
                <h6>Weekly Effort</h6>
                <div class="chart-container"><canvas id="weeklyPolar"></canvas></div>
            </div>
        </div>
    </div>

    <h4 class="fw-800">Recent Exam Results</h4>
    <div class="table-container shadow-lg mb-5">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr><th>Date</th><th>Subject</th><th>Score</th><th>Accuracy</th></tr>
                </thead>
                <tbody>
                    <?php if($exam_history && mysqli_num_rows($exam_history) > 0): ?>
                        <?php while($ex = mysqli_fetch_assoc($exam_history)): 
                            $acc = round(($ex['correct_answers']/$ex['total_questions'])*100); ?>
                            <tr>
                                <td><?= date("M d", strtotime($ex['completed_at'])) ?></td>
                                <td><span class="subject-tag"><?= $ex['subject_name'] ?></span></td>
                                <td><?= $ex['correct_answers'] ?> / <?= $ex['total_questions'] ?></td>
                                <td><span class="badge-accuracy"><?= $acc ?>%</span></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="4" class="text-center py-4 text-muted">No exam data found. Complete a paper to see results here.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <h4 class="fw-800">Study Activity Log</h4>
    <div class="table-container shadow-lg">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr><th>Date & Timing</th><th>Subject</th><th>Duration</th><th class="text-end">Action</th></tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($history_query) > 0): ?>
                        <?php while($row = mysqli_fetch_assoc($history_query)): ?>
                        <tr>
                            <td><b><?= date("M d, Y", strtotime($row['start_time'])) ?></b><br>
                                <small class="text-muted"><?= date("h:i A", strtotime($row['start_time'])) ?></small>
                            </td>
                            <td><span class="subject-tag"><?= $row['subject'] ?></span></td>
                            <td><span class="badge bg-success bg-opacity-10 text-success p-2 rounded"><?= $row['duration'] ?></span></td>
                            <td class="text-end">
                                <a href="history.php?delete=<?= $row['id'] ?>" class="text-danger" onclick="return confirm('Delete this?')"><i class="fa fa-trash"></i></a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="4" class="text-center py-4">No sessions found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    // Charts config
    const ctx1 = document.getElementById('subPieChart');
    new Chart(ctx1, { type: 'pie', data: { labels: <?= json_encode($sub_labels) ?>, datasets: [{ data: <?= json_encode($sub_data) ?>, backgroundColor: ['#6366f1', '#06b6d4', '#f59e0b'] }] } });

    const ctx2 = document.getElementById('accBarChart');
    new Chart(ctx2, { type: 'bar', data: { labels: <?= json_encode($acc_labels) ?>, datasets: [{ label: 'Accuracy %', data: <?= json_encode($acc_data) ?>, backgroundColor: '#06b6d4' }] }, options: { scales: { y: { beginAtZero: true, max: 100 } } } });

    const ctx3 = document.getElementById('weeklyPolar');
    new Chart(ctx3, { type: 'polarArea', data: { labels: <?= json_encode($week_labels) ?>, datasets: [{ data: <?= json_encode($week_data) ?>, backgroundColor: 'rgba(99, 102, 241, 0.5)' }] } });
</script>
</body>
</html>