<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// --- DELETE LOGIC ---
if (isset($_GET['delete_id'])) {
    $del_id = mysqli_real_escape_string($conn, $_GET['delete_id']);
    mysqli_query($conn, "DELETE FROM user_revisions WHERE id = '$del_id' AND user_id = '$user_id'");
    header("Location: revision_info.php");
    exit();
}

// --- FILTER & SEARCH LOGIC ---
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$f_subject = isset($_GET['f_subject']) ? mysqli_real_escape_string($conn, $_GET['f_subject']) : '';
$f_type = isset($_GET['f_type']) ? mysqli_real_escape_string($conn, $_GET['f_type']) : '';

$query = "SELECT * FROM user_revisions WHERE user_id = '$user_id'";

if ($search) {
    $query .= " AND (question LIKE '%$search%' OR lesson_name LIKE '%$search%')";
}
if ($f_subject) {
    $query .= " AND subject_name = '$f_subject'";
}
if ($f_type) {
    $query .= " AND paper_type = '$f_type'";
}

$query .= " ORDER BY id DESC";
$result = mysqli_query($conn, $query);

$subs_res = mysqli_query($conn, "SELECT DISTINCT subject_name FROM user_revisions WHERE user_id = '$user_id'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nexus | Database Explorer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap');
        
        :root { --bg: #020617; --card: #0f172a; --accent: #6366f1; --border: rgba(255,255,255,0.1); }
        
        body { 
            background-color: var(--bg) !important; 
            color: #cbd5e1 !important; 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            padding: 20px 0; 
        }
        
        /* Filter Panel */
        .filter-panel { 
            background: rgba(15, 23, 42, 0.8); 
            border: 1px solid var(--border); 
            border-radius: 20px; 
            padding: 20px; 
            margin-bottom: 25px;
        }

        .glass-card { 
            background: var(--card) !important; 
            border: 1px solid var(--border) !important; 
            border-radius: 24px; 
            overflow: hidden; 
        }

        .form-control, .form-select { 
            background: #020617 !important; 
            border: 1px solid var(--border) !important; 
            color: #ffffff !important; 
        }

        /* --- table text fix --- */
        .table { 
            background-color: transparent !important; 
            color: #cbd5e1 !important; /* light grey text */
            margin-bottom: 0;
        }

        .table thead th { 
            background-color: #1e293b !important; 
            color: #6366f1 !important; /* purple accent for headers */
            border: none !important;
            font-weight: 800;
            padding: 15px;
        }

        .table td { 
            background-color: #0f172a !important; 
            color: #cbd5e1 !important; 
            border-bottom: 1px solid var(--border) !important;
            padding: 15px;
        }

        .text-white-custom { color: #ffffff !important; font-weight: 600; }
        .text-muted-custom { color: #94a3b8 !important; font-size: 0.85rem; }

        .btn-action { 
            width: 35px; height: 35px; border-radius: 10px; 
            display: inline-flex; align-items: center; justify-content: center; 
            transition: 0.3s; text-decoration: none;
        }
        .btn-edit { background: rgba(99, 102, 241, 0.2); color: #818cf8; }
        .btn-edit:hover { background: #6366f1; color: white; }
        .btn-del { background: rgba(239, 68, 68, 0.2); color: #f87171; }
        .btn-del:hover { background: #ef4444; color: white; }

        @media (max-width: 768px) {
            .table thead { display: none; }
            .table tr { display: block; margin-bottom: 15px; border: 1px solid var(--border); border-radius: 15px; background: #0f172a; }
            .table td { display: block; text-align: left; border: none !important; }
            .table td:last-child { text-align: right; border-top: 1px solid var(--border) !important; }
        }
    </style>
</head>
<body>

<div class="container px-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="text-white fw-bold mb-0">Question Bank</h2>
            <p class="text-muted small">Manage your revision database</p>
        </div>
        <a href="revision.php" class="btn btn-sm btn-outline-light rounded-pill px-3">Back</a>
    </div>

    <form action="" method="GET" class="filter-panel shadow-sm">
        <div class="row g-3">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Search questions..." value="<?= htmlspecialchars($search) ?>">
            </div>
            <div class="col-md-3">
                <select name="f_subject" class="form-select">
                    <option value="">All Subjects</option>
                    <?php while($s = mysqli_fetch_assoc($subs_res)): ?>
                        <option value="<?= $s['subject_name'] ?>" <?= ($f_subject == $s['subject_name']) ? 'selected' : '' ?>><?= $s['subject_name'] ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-3">
                <select name="f_type" class="form-select">
                    <option value="">Any Type</option>
                    <option value="MCQ" <?= ($f_type == 'MCQ') ? 'selected' : '' ?>>MCQ</option>
                    <option value="Structured" <?= ($f_type == 'Structured') ? 'selected' : '' ?>>Structured</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100 fw-bold" style="background: #6366f1; border:none;">Filter</button>
            </div>
        </div>
    </form>

    <div class="glass-card">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Subject Info</th>
                        <th>Question & Answer</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(mysqli_num_rows($result) > 0): ?>
                        <?php while($row = mysqli_fetch_assoc($result)): 
                            $isMCQ = ($row['paper_type'] == 'MCQ');
                            $opts = $isMCQ ? json_decode($row['options_json']) : [];
                        ?>
                        <tr>
                            <td>
                                <div class="text-white-custom"><?= htmlspecialchars($row['subject_name']) ?></div>
                                <div class="text-muted-custom small mt-1"><?= $row['paper_type'] ?> | <?= htmlspecialchars($row['lesson_name']) ?></div>
                            </td>
                            <td>
                                <div class="text-white-custom mb-1"><?= htmlspecialchars($row['question']) ?></div>
                                <div style="color: #6366f1; font-size: 0.85rem;">
                                    <?php if($isMCQ): ?>
                                        <i class="fa fa-check-circle me-1"></i> <?= htmlspecialchars($opts[$row['correct_idx']] ?? 'N/A') ?>
                                    <?php else: ?>
                                        <i class="fa fa-lightbulb me-1"></i> <?= mb_strimwidth(htmlspecialchars($row['answer_text']), 0, 100, "...") ?>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="text-end">
                                <a href="edit_question.php?id=<?= $row['id'] ?>" class="btn-action btn-edit">
                                    <i class="fa fa-pen"></i>
                                </a>
                                <a href="?delete_id=<?= $row['id'] ?>" class="btn-action btn-del ms-1" onclick="return confirm('Delete?')">
                                    <i class="fa fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="text-center py-5 text-muted">No questions found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>