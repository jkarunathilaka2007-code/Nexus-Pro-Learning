<?php
session_start();
include 'db.php';
if (!isset($_SESSION['user_id'])) { die("Please login to continue."); }
$user_id = $_SESSION['user_id'];

// --- Filtering Logic ---
$subject_filter = isset($_GET['subject']) ? mysqli_real_escape_string($conn, $_GET['subject']) : '';
$lesson_filter = isset($_GET['lesson']) ? mysqli_real_escape_string($conn, $_GET['lesson']) : '';

$where_clause = "WHERE user_id = '$user_id'";
if ($subject_filter) { $where_clause .= " AND subject_name = '$subject_filter'"; }
if ($lesson_filter) { $where_clause .= " AND lesson_name = '$lesson_filter'"; }

$query = "SELECT * FROM user_formulas $where_clause ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);

// Filter Dropdowns සඳහා දත්ත ලබා ගැනීම
$subjects_res = mysqli_query($conn, "SELECT DISTINCT subject_name FROM user_formulas WHERE user_id = '$user_id'");
$lessons_res = mysqli_query($conn, "SELECT DISTINCT lesson_name FROM user_formulas WHERE user_id = '$user_id'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maths Bank | Library</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <script>window.MathJax = { tex: { inlineMath: [['$', '$']], displayMath: [['$$', '$$']] } };</script>
    <script id="MathJax-script" async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap');

        body { 
            background: #0b0f19; 
            color: #f8fafc; 
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-image: radial-gradient(circle at 50% 0%, #1e293b 0%, #0b0f19 100%);
            min-height: 100vh;
        }

        .filter-section {
            background: rgba(30, 41, 59, 0.4);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 20px;
            margin-bottom: 30px;
        }

        .form-select-custom {
            background: #111827 !important;
            border: 1px solid #334155 !important;
            color: white !important;
            border-radius: 12px;
        }

        /* Formula Card Styling */
        .formula-card {
            background: rgba(30, 41, 59, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 20px;
            padding: 20px;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            height: 100%;
            position: relative;
            display: flex;
            flex-direction: column;
            overflow: hidden; /* Card එකෙන් එළියට යන දේවල් පාලනයට */
        }

        .formula-card:hover {
            transform: translateY(-10px);
            background: rgba(30, 41, 59, 0.9);
            border-color: #3b82f6;
            box-shadow: 0 20px 40px rgba(0,0,0,0.4);
        }

        .tag-subject {
            font-size: 10px;
            font-weight: 800;
            background: rgba(59, 130, 246, 0.2);
            color: #60a5fa;
            padding: 4px 12px;
            border-radius: 50px;
            text-transform: uppercase;
            display: inline-block;
            margin-bottom: 10px;
        }

        .lesson-name { font-weight: 700; color: #f1f5f9; font-size: 0.95rem; display: block; }
        .topic-name { font-size: 0.8rem; color: #94a3b8; margin-bottom: 15px; display: block; }

        /* සූත්‍රය දිග වැඩි නම් scroll කරන්න පුළුවන් කොටස */
        .formula-display {
            font-size: 1.2rem;
            color: #60a5fa;
            margin: auto 0;
            padding: 20px 0;
            text-align: center;
            overflow-x: auto; /* දිග වැඩි නම් scroll bar එක එනවා */
            overflow-y: hidden;
            max-width: 100%;
            scrollbar-width: thin; /* Firefox සඳහා */
            scrollbar-color: #3b82f6 rgba(0,0,0,0);
        }

        /* Scrollbar එක ලස්සන කරන්න (Chrome/Safari) */
        .formula-display::-webkit-scrollbar {
            height: 4px;
        }
        .formula-display::-webkit-scrollbar-thumb {
            background: #3b82f6;
            border-radius: 10px;
        }

        /* Hover Action Buttons */
        .card-actions {
            position: absolute;
            top: 15px;
            right: 15px;
            display: flex;
            gap: 5px;
            opacity: 0;
            transition: 0.3s;
            z-index: 5; /* සූත්‍රයට යට නොවෙන්න */
        }

        .formula-card:hover .card-actions { opacity: 1; }

        .btn-action {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            border: none;
            transition: 0.2s;
        }

        .btn-edit { background: #1e293b; color: #fbbf24; border: 1px solid #334155; }
        .btn-edit:hover { background: #fbbf24; color: #000; }

        .btn-delete { background: #1e293b; color: #ef4444; border: 1px solid #334155; }
        .btn-delete:hover { background: #ef4444; color: #fff; }

        /* Floating Button */
        .fab {
            position: fixed; bottom: 30px; right: 30px;
            width: 60px; height: 60px; border-radius: 20px;
            background: #3b82f6; color: white;
            display: flex; align-items: center; justify-content: center;
            font-size: 28px; box-shadow: 0 10px 25px rgba(59, 130, 246, 0.4);
            transition: 0.3s; z-index: 1000;
        }
        .fab:hover { transform: scale(1.1) rotate(90deg); }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h2 class="fw-bold m-0"><i class="bi bi-journal-code text-primary"></i> Formula Bank</h2>
            <p class="text-muted small">Your personalized scientific equation library</p>
        </div>
        <a href="formula_book.php" class="btn btn-warning rounded-pill px-4 fw-bold shadow">
            <i class="bi bi-book-half me-2"></i> 3D VIEW
        </a>
    </div>

    <div class="filter-section">
        <form method="GET" class="row g-3">
            <div class="col-md-4">
                <label class="small text-secondary fw-bold mb-2">SUBJECT</label>
                <select name="subject" class="form-select form-select-custom" onchange="this.form.submit()">
                    <option value="">All Subjects</option>
                    <?php while($s = mysqli_fetch_assoc($subjects_res)): ?>
                        <option value="<?= $s['subject_name'] ?>" <?= ($subject_filter == $s['subject_name']) ? 'selected' : '' ?>>
                            <?= $s['subject_name'] ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="small text-secondary fw-bold mb-2">LESSON</label>
                <select name="lesson" class="form-select form-select-custom" onchange="this.form.submit()">
                    <option value="">All Lessons</option>
                    <?php 
                    while($l = mysqli_fetch_assoc($lessons_res)): ?>
                        <option value="<?= $l['lesson_name'] ?>" <?= ($lesson_filter == $l['lesson_name']) ? 'selected' : '' ?>>
                            <?= $l['lesson_name'] ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <a href="maths_bank.php" class="btn btn-outline-secondary w-100 rounded-pill">
                    <i class="bi bi-arrow-clockwise"></i> Reset Filters
                </a>
            </div>
        </form>
    </div>

    <div class="row g-4">
        <?php if(mysqli_num_rows($result) > 0): ?>
            <?php while($row = mysqli_fetch_assoc($result)): ?>
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="formula-card">
                    <div class="card-actions">
                        <a href="edit_formula.php?id=<?= $row['id'] ?>" class="btn-action btn-edit" title="Edit">
                            <i class="bi bi-pencil-fill"></i>
                        </a>
                        <button onclick="deleteFormula(<?= $row['id'] ?>)" class="btn-action btn-delete" title="Delete">
                            <i class="bi bi-trash3-fill"></i>
                        </button>
                    </div>

                    <div class="tag-subject"><?= htmlspecialchars($row['subject_name']) ?></div>
                    <span class="lesson-name text-truncate"><?= htmlspecialchars($row['lesson_name']) ?></span>
                    <span class="topic-name text-truncate"><?= htmlspecialchars($row['topic'] ?: 'General') ?></span>
                    
                    <div class="formula-display">
                        $$ <?= $row['formula_text'] ?> $$
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12 text-center py-5">
                <div class="opacity-25 mb-3"><i class="bi bi-search" style="font-size: 4rem;"></i></div>
                <h4 class="text-secondary">No formulas found.</h4>
                <p class="text-muted">Try changing your filters or add a new formula.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<a href="add_formula.php" class="fab text-white text-decoration-none shadow-lg">
    <i class="bi bi-plus-lg"></i>
</a>

<script>
    // Delete Function
    function deleteFormula(id) {
        if(confirm("Are you sure you want to delete this formula? This action cannot be undone.")) {
            window.location.href = "delete_formula.php?id=" + id;
        }
    }

    // Auto Refresh MathJax after filtering
    document.addEventListener("DOMContentLoaded", function() {
        MathJax.typesetPromise();
    });
</script>

</body>
</html>