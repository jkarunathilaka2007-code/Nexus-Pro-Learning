<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['subject'])) {
    header("Location: add_subject_preparation.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$subject = mysqli_real_escape_string($conn, $_GET['subject']);

// 1. දැනටමත් Database එකේ තියෙන දත්ත ලබා ගැනීම
$query = mysqli_query($conn, "SELECT * FROM trackers WHERE user_id = '$user_id' AND subject = '$subject'");

$existing_configs = [];
$start_year = "";
$end_year = "";

while ($row = mysqli_fetch_assoc($query)) {
    $existing_configs[$row['paper_type']] = [
        'q_count' => $row['question_count'],
        'time' => $row['allocated_time']
    ];
    $start_year = $row['start_year'];
    $end_year = $row['end_year'];
}

// දත්ත කිසිවක් නැත්නම් ආපසු යැවීම
if (empty($existing_configs)) {
    header("Location: add_subject_preparation.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Preparation | <?= htmlspecialchars($subject) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary: #6366f1; --dark: #0f172a; }
        body { 
            background: var(--dark); 
            color: #f8fafc; 
            font-family: 'Plus Jakarta Sans', sans-serif; 
        }
        .edit-card { 
            background: rgba(255, 255, 255, 0.03); 
            border: 1px solid rgba(255, 255, 255, 0.1); 
            border-radius: 32px; 
            backdrop-filter: blur(15px);
            padding: 40px;
            margin-top: 50px;
        }
        .form-control { 
            background: rgba(0,0,0,0.3); 
            border: 1px solid rgba(255,255,255,0.1); 
            color: white; 
            border-radius: 12px; 
        }
        .form-control:focus { 
            background: rgba(0,0,0,0.4); 
            border-color: var(--primary); 
            color: white; 
            box-shadow: none; 
        }
        .type-card {
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 20px;
            padding: 20px;
            transition: 0.3s;
        }
        .type-card.active {
            border-color: var(--primary);
            background: rgba(99, 102, 241, 0.05);
        }
        .btn-update {
            background: var(--primary);
            border: none;
            padding: 12px 40px;
            border-radius: 50px;
            font-weight: 700;
            transition: 0.3s;
        }
        .btn-update:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(99, 102, 241, 0.3);
        }
    </style>
</head>
<body>

<div class="container pb-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="edit-card">
                <div class="d-flex align-items-center mb-4">
                    <a href="add_subject_preparation.php" class="btn btn-outline-light btn-sm rounded-circle me-3" style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;">
                        <i class="fa fa-arrow-left"></i>
                    </a>
                    <h2 class="fw-800 mb-0">Edit <span class="text-primary"><?= htmlspecialchars($subject) ?></span></h2>
                </div>

                <form action="save_preperation.php" method="POST">
                    <input type="hidden" name="subject" value="<?= htmlspecialchars($subject) ?>">

                    <div class="mb-4 p-4 rounded-4 bg-black bg-opacity-20 border border-white border-opacity-10">
                        <h6 class="fw-bold mb-3 text-info"><i class="fa fa-calendar-alt me-2"></i>Target Year Range</h6>
                        <div class="row g-3">
                            <div class="col-6">
                                <label class="small opacity-75 mb-1">From Year</label>
                                <input type="number" name="start_year" class="form-control" value="<?= $start_year ?>" required>
                            </div>
                            <div class="col-6">
                                <label class="small opacity-75 mb-1">To Year</label>
                                <input type="number" name="end_year" class="form-control" value="<?= $end_year ?>" required>
                            </div>
                        </div>
                    </div>

                    <h5 class="fw-bold mb-3">Paper Components</h5>
                    <div class="row g-3">
                        <?php 
                        $all_types = [
                            'MCQ' => 'fa-list-ol', 
                            'Structured' => 'fa-layer-group', 
                            'Essay' => 'fa-pen-nib'
                        ];

                        foreach ($all_types as $type_name => $icon): 
                            $is_checked = isset($existing_configs[$type_name]);
                            $q_val = $is_checked ? $existing_configs[$type_name]['q_count'] : "";
                            $t_val = $is_checked ? $existing_configs[$type_name]['time'] : "";
                        ?>
                        <div class="col-12">
                            <div class="type-card <?= $is_checked ? 'active' : '' ?>">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <div class="d-flex align-items-center">
                                        <div class="form-check me-2">
                                            <input class="form-check-input" type="checkbox" name="types[]" value="<?= $type_name ?>" id="check-<?= $type_name ?>" <?= $is_checked ? 'checked' : '' ?> onchange="toggleInputs('<?= $type_name ?>')">
                                        </div>
                                        <i class="fa <?= $icon ?> text-primary me-2"></i>
                                        <label class="fw-bold mb-0" for="check-<?= $type_name ?>"><?= $type_name ?></label>
                                    </div>
                                    <?php if($is_checked): ?>
                                        <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill">Active</span>
                                    <?php endif; ?>
                                </div>

                                <div class="row g-3" id="inputs-<?= $type_name ?>" style="<?= $is_checked ? '' : 'opacity: 0.5; pointer-events: none;' ?>">
                                    <div class="col-6">
                                        <label class="small opacity-75 mb-1">No. of Questions</label>
                                        <input type="number" name="config[<?= $type_name ?>][q_count]" class="form-control" value="<?= $q_val ?>" placeholder="e.g. 50">
                                    </div>
                                    <div class="col-6">
                                        <label class="small opacity-75 mb-1">Time (Minutes)</label>
                                        <input type="number" name="config[<?= $type_name ?>][time]" class="form-control" value="<?= $t_val ?>" placeholder="e.g. 120">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="text-end mt-5">
                        <button type="submit" class="btn btn-update text-white">
                            Save Changes <i class="fa fa-save ms-2"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Checkbox එක අයින් කළොත් Inputs ටික disable කරන function එක
    function toggleInputs(type) {
        const checkbox = document.getElementById('check-' + type);
        const container = document.getElementById('inputs-' + type);
        const card = checkbox.closest('.type-card');

        if (checkbox.checked) {
            container.style.opacity = "1";
            container.style.pointerEvents = "auto";
            card.classList.add('active');
        } else {
            container.style.opacity = "0.5";
            container.style.pointerEvents = "none";
            card.classList.remove('active');
        }
    }
</script>

</body>
</html>