<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// 1. පරිශීලකයාගේ profile එකේ තියෙන විෂයන් ලබා ගැනීම
$u_query = mysqli_query($conn, "SELECT subjects FROM users WHERE id = '$user_id'");
$u_data = mysqli_fetch_assoc($u_query);
$subjects_list = array_filter(explode(", ", $u_data['subjects']));

// 2. දැනටමත් tracker හදලා තියෙන විෂයන් ලැයිස්තුව ලබා ගැනීම
$existing_q = mysqli_query($conn, "SELECT DISTINCT subject FROM trackers WHERE user_id = '$user_id'");
$existing_subjects = [];
while($row = mysqli_fetch_assoc($existing_q)) { 
    $existing_subjects[] = $row['subject']; 
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subject Preparation | Nexus Pro</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root { --primary: #6366f1; --dark: #0f172a; --success: #22c55e; }
        body { 
            background: var(--dark); 
            color: #f8fafc; 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            min-height: 100vh;
        }
        
        .wizard-card { 
            background: rgba(255, 255, 255, 0.03); 
            border: 1px solid rgba(255, 255, 255, 0.1); 
            border-radius: 32px; 
            backdrop-filter: blur(15px); 
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }

        .step-container { display: none; }
        .step-container.active { display: block; animation: fadeIn 0.4s ease; }

        /* Option Boxes */
        .option-box { 
            border: 1px solid rgba(255, 255, 255, 0.1); 
            border-radius: 20px; 
            padding: 20px; 
            cursor: pointer; 
            transition: 0.3s; 
            background: rgba(255,255,255,0.02);
            position: relative;
            height: 100%;
        }

        .option-box:hover { border-color: var(--primary); background: rgba(255,255,255,0.05); }

        /* Status Colors */
        .subject-active { border-color: rgba(34, 197, 94, 0.3); background: rgba(34, 197, 94, 0.05); }

        /* Action Buttons for Active Subjects */
        .action-btns {
            position: absolute;
            top: 12px;
            right: 12px;
            display: flex;
            gap: 8px;
            z-index: 10;
        }

        .btn-icon {
            width: 32px;
            height: 32px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: none;
            color: white;
            transition: 0.2s;
            text-decoration: none;
        }
        .btn-edit { background: var(--primary); }
        .btn-delete { background: #ef4444; }
        .btn-icon:hover { transform: scale(1.1); color: white; }

        /* Form Controls */
        input[type="radio"]:checked + .option-box,
        input[type="checkbox"]:checked + .option-box { 
            border-color: var(--primary); 
            background: rgba(99, 102, 241, 0.15);
            box-shadow: 0 0 20px rgba(99, 102, 241, 0.1);
        }

        .form-control { 
            background: rgba(0,0,0,0.3); 
            border: 1px solid rgba(255,255,255,0.1); 
            color: white; 
            border-radius: 12px; 
            padding: 12px;
        }
        .form-control:focus { background: rgba(0,0,0,0.4); color: white; border-color: var(--primary); box-shadow: none; }

        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="wizard-card p-4 p-md-5">
                <form action="save_preperation.php" method="POST" id="prepForm">
                    
                    <div class="step-container active" id="step-1">
                        <div class="text-center mb-5">
                            <h2 class="fw-800">Subject Preparation</h2>
                            <p class="text-muted">Select a subject to start or manage existing trackers.</p>
                        </div>
                        <div class="row g-3">
                            <?php foreach($subjects_list as $index => $sub): 
                                $is_active = in_array($sub, $existing_subjects);
                            ?>
                            <div class="col-md-6">
                                <?php if(!$is_active): ?>
                                    <input type="radio" name="subject" id="s-<?= $index ?>" value="<?= htmlspecialchars($sub) ?>" class="btn-check" required>
                                    <label class="option-box d-block w-100" for="s-<?= $index ?>">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary bg-opacity-10 p-3 rounded-circle me-3 text-primary">
                                                <i class="fa fa-plus"></i>
                                            </div>
                                            <span class="fw-bold fs-5"><?= htmlspecialchars($sub) ?></span>
                                        </div>
                                    </label>
                                <?php else: ?>
                                    <div class="option-box subject-active">
                                        <div class="action-btns">
                                            <a href="edit_preperation.php?subject=<?= urlencode($sub) ?>" class="btn-icon btn-edit" title="Edit Configuration"><i class="fa fa-pen"></i></a>
                                            <button type="button" onclick="confirmDelete('<?= htmlspecialchars($sub) ?>')" class="btn-icon btn-delete" title="Delete Tracker"><i class="fa fa-trash"></i></button>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-success bg-opacity-10 p-3 rounded-circle me-3 text-success">
                                                <i class="fa fa-check"></i>
                                            </div>
                                            <div>
                                                <span class="fw-bold d-block fs-5"><?= htmlspecialchars($sub) ?></span>
                                                <small class="text-success fw-bold text-uppercase" style="letter-spacing: 1px; font-size: 0.7rem;">Active Tracker</small>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="text-center mt-5">
                            <button type="button" class="btn btn-primary px-5 py-3 rounded-pill fw-800" onclick="nextStep(2)">Configure Subject <i class="fa fa-arrow-right ms-2"></i></button>
                        </div>
                    </div>

                    <div class="step-container" id="step-2">
                        <div class="text-center mb-5">
                            <h3 class="fw-bold">Paper Components</h3>
                            <p class="text-muted">Select all paper parts you want to include in this tracker.</p>
                        </div>
                        <div class="row g-3">
                            <?php 
                            $pts = ['MCQ' => 'fa-list-check', 'Structured' => 'fa-align-left', 'Essay' => 'fa-pen-fancy']; 
                            foreach($pts as $name => $icon): ?>
                            <div class="col-4">
                                <input type="checkbox" name="types[]" id="t-<?= $name ?>" value="<?= $name ?>" class="btn-check">
                                <label class="option-box text-center d-block w-100" for="t-<?= $name ?>">
                                    <i class="fa <?= $icon ?> fa-2x mb-3 d-block opacity-50"></i>
                                    <span class="fw-bold"><?= $name ?></span>
                                </label>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="d-flex justify-content-between mt-5">
                            <button type="button" class="btn btn-outline-light px-4 py-3 rounded-pill" onclick="nextStep(1)">Back</button>
                            <button type="button" class="btn btn-primary px-5 py-3 rounded-pill fw-bold" onclick="nextStep(3)">Next Step <i class="fa fa-arrow-right ms-2"></i></button>
                        </div>
                    </div>

                    <div class="step-container" id="step-3">
                        <div class="text-center mb-5">
                            <h3 class="fw-bold">Tracker Details</h3>
                            <p class="text-muted">Set your question targets and time limits.</p>
                        </div>
                        <div id="dynamic-fields"></div>
                        <div class="d-flex justify-content-between mt-5">
                            <button type="button" class="btn btn-outline-light px-4 py-3 rounded-pill" onclick="nextStep(2)">Back</button>
                            <button type="submit" class="btn btn-success px-5 py-3 rounded-pill fw-bold" style="background: var(--success); border:none;">Create Preparation <i class="fa fa-rocket ms-2"></i></button>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function nextStep(s) {
        // Validation for Step 1
        if(s === 2 && !document.querySelector('input[name="subject"]:checked')) {
            Swal.fire({ icon: 'info', title: 'Subject Required', text: 'Please select a new subject to configure.', background: '#1e293b', color: '#fff' });
            return;
        }
        // Validation for Step 2
        if(s === 3 && document.querySelectorAll('input[name="types[]"]:checked').length === 0) {
            Swal.fire({ icon: 'warning', title: 'No Types Selected', text: 'Select at least one paper component (MCQ, etc.)', background: '#1e293b', color: '#fff' });
            return;
        }
        
        document.querySelectorAll('.step-container').forEach(c => c.classList.remove('active'));
        document.getElementById('step-' + s).classList.add('active');
        if(s === 3) generateFields();
    }

    function generateFields() {
        const container = document.getElementById('dynamic-fields');
        const selected = document.querySelectorAll('input[name="types[]"]:checked');
        let html = `
            <div class="mb-4 p-4 rounded-4 bg-black bg-opacity-20 border border-white border-opacity-10 shadow-inner">
                <h6 class="fw-bold mb-3 text-info"><i class="fa fa-calendar-check me-2"></i>Past Paper Year Range</h6>
                <div class="row g-3">
                    <div class="col-6">
                        <label class="small fw-bold opacity-75 mb-1">From Year</label>
                        <input type="number" name="start_year" class="form-control" placeholder="2010" required min="1980" max="2026">
                    </div>
                    <div class="col-6">
                        <label class="small fw-bold opacity-75 mb-1">To Year</label>
                        <input type="number" name="end_year" class="form-control" placeholder="2024" required min="1990" max="2026">
                    </div>
                </div>
            </div>`;

        selected.forEach(t => {
            html += `
                <div class="card bg-white bg-opacity-5 border border-white border-opacity-10 rounded-4 p-4 mb-3">
                    <h6 class="fw-bold text-primary mb-3"><i class="fa fa-sliders-h me-2"></i>${t.value} Settings</h6>
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="small fw-bold opacity-75 mb-1">Target Questions</label>
                            <input type="number" name="config[${t.value}][q_count]" class="form-control" placeholder="e.g. 50" required>
                        </div>
                        <div class="col-6">
                            <label class="small fw-bold opacity-75 mb-1">Time Allowed (Mins)</label>
                            <input type="number" name="config[${t.value}][time]" class="form-control" placeholder="e.g. 120" required>
                        </div>
                    </div>
                </div>`;
        });
        container.innerHTML = html;
    }

    function confirmDelete(sub) {
        Swal.fire({
            title: 'Remove Tracker?',
            text: "This will delete all saved progress for " + sub + ". This action cannot be undone!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Yes, Delete it!',
            background: '#1e293b',
            color: '#fff'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'delete_preperation.php?subject=' + encodeURIComponent(sub);
            }
        });
    }
</script>
</body>
</html>