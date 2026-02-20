<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) { 
    header("Location: login.php"); 
    exit(); 
}
$user_id = $_SESSION['user_id'];

$res = mysqli_query($conn, "SELECT * FROM timetables WHERE user_id = '$user_id' LIMIT 1");
$tt_data = mysqli_fetch_assoc($res);
$has_tt = $tt_data ? true : false;

$timetable = $has_tt ? json_decode($tt_data['content'], true) : [];
$days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
?>

<!DOCTYPE html>
<html lang="si">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Schedule | Nexus Pro</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    
    <style>
        :root {
            --primary: #6366f1;
            --secondary: #a855f7;
            --dark-bg: #0b1120;
            --card-bg: rgba(30, 41, 59, 0.7);
            --danger: #ef4444;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--dark-bg);
            color: #f8fafc;
            min-height: 100vh;
            overflow-x: hidden;
        }

        .premium-card {
            background: var(--card-bg);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 24px;
            padding: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
        }

        .action-btn {
            width: 45px; height: 45px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            transition: 0.3s all; border: none; text-decoration: none; color: white;
        }
        .btn-img { background: #3b82f6; }
        .btn-edit { background: #10b981; }
        .btn-del { background: var(--danger); }
        .btn-create { background: var(--primary); padding: 10px 25px; width: auto; border-radius: 50px; }

        /* Responsive Table */
        .table-responsive { border-radius: 15px; }
        .schedule-table { border-collapse: separate; border-spacing: 0 10px; min-width: 800px; }
        .schedule-table thead th { color: var(--primary); font-size: 0.75rem; letter-spacing: 1px; border: none; }
        .schedule-table tbody tr { background: rgba(255, 255, 255, 0.03); border-radius: 12px; transition: 0.3s; }
        .schedule-table td { padding: 15px; border: none; vertical-align: middle; }
        .schedule-table td:first-child { border-radius: 12px 0 0 12px; color: #94a3b8; font-weight: bold; width: 140px; }
        .schedule-table td:last-child { border-radius: 0 12px 12px 0; }

        .subject-badge {
            background: rgba(99, 102, 241, 0.15);
            color: #818cf8;
            padding: 6px 10px; border-radius: 8px; font-size: 0.8rem; font-weight: 600;
            display: block; border: 1px solid rgba(99, 102, 241, 0.2);
        }

        @media (max-width: 768px) {
            .header-flex { flex-direction: column; align-items: flex-start !important; gap: 15px; }
            .premium-card { padding: 10px; border-radius: 15px; }
        }
    </style>
</head>
<body>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 header-flex">
        <div>
            <a href="dashboard.php" class="text-white-50 text-decoration-none small"><i class="fa fa-arrow-left"></i> Dashboard</a>
            <h2 class="fw-800 mt-1 mb-0">Study <span class="text-primary">Schedule</span></h2>
        </div>

        <div class="d-flex gap-2">
            <?php if($has_tt): ?>
                <button onclick="downloadImage()" class="action-btn btn-img" title="Download as Image">
                    <i class="fa fa-image"></i>
                </button>
                <a href="edit_timetable.php" class="action-btn btn-edit" title="Edit Schedule">
                    <i class="fa fa-pen"></i>
                </a>
                <button onclick="confirmDelete()" class="action-btn btn-del" title="Delete Timetable">
                    <i class="fa fa-trash"></i>
                </button>
            <?php else: ?>
                <a href="create_timetable.php" class="action-btn btn-create fw-bold text-decoration-none">
                    <i class="fa fa-plus me-2"></i> Create New
                </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="premium-card" id="timetable-area">
        <?php if($has_tt): ?>
            <div class="table-responsive">
                <table class="table text-center schedule-table mb-0">
                    <thead>
                        <tr>
                            <th>Time Slot</th>
                            <?php foreach($days as $day) echo "<th>".substr($day,0,3)."</th>"; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($timetable['slots'] as $index => $slot): $row_id = $index + 1; ?>
                        <tr>
                            <td><i class="far fa-clock me-1 opacity-50"></i> <?= $slot['time'] ?></td>
                            <?php foreach($days as $day): 
                                $sub = $timetable['data'][$row_id][$day] ?? '';
                            ?>
                                <td>
                                    <?php if($sub): ?>
                                        <span class="subject-badge"><?= htmlspecialchars($sub) ?></span>
                                    <?php else: ?>
                                        <span class="opacity-25">-</span>
                                    <?php endif; ?>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center py-5">
                <i class="fa fa-calendar-alt fa-3x opacity-20 mb-3"></i>
                <h5>No Timetable Found</h5>
                <p class="text-muted small">Create your master plan to stay organized.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function downloadImage() {
    const area = document.getElementById('timetable-area');
    html2canvas(area, {
        backgroundColor: "#0b1120", // Body background ekama demma
        scale: 2 // Quality eka wedi karanna
    }).then(canvas => {
        const link = document.createElement('a');
        link.download = 'My-Study-Schedule.png';
        link.href = canvas.toDataURL();
        link.click();
    });
}

function confirmDelete() {
    if(confirm("Are you sure you want to delete your entire timetable? This cannot be undone.")) {
        window.location.href = "delete_timetable.php";
    }
}
</script>
</body>
</html>