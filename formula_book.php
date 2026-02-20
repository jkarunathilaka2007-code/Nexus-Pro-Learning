<?php
session_start();
include 'db.php';
if (!isset($_SESSION['user_id'])) { die("Please login to continue."); }
$user_id = $_SESSION['user_id'];

// --- Get unique subjects and topics for filter dropdowns ---
$subjects_res = mysqli_query($conn, "SELECT DISTINCT subject_name FROM user_formulas WHERE user_id = '$user_id' ORDER BY subject_name");
$topics_res = mysqli_query($conn, "SELECT DISTINCT topic FROM user_formulas WHERE user_id = '$user_id' ORDER BY topic");

// --- Filtering Logic ---
$subject_filter = isset($_GET['subject']) ? mysqli_real_escape_string($conn, $_GET['subject']) : '';
$topic_filter = isset($_GET['topic']) ? mysqli_real_escape_string($conn, $_GET['topic']) : '';

$where_clause = "WHERE user_id = '$user_id'";
if ($subject_filter) { $where_clause .= " AND subject_name = '$subject_filter'"; }
if ($topic_filter) { $where_clause .= " AND topic = '$topic_filter'"; }

$query = "SELECT * FROM user_formulas $where_clause ORDER BY lesson_name ASC, topic ASC";
$result = mysqli_query($conn, $query);

$raw_formulas = [];
while ($row = mysqli_fetch_assoc($result)) {
    $raw_formulas[] = $row;
}

$formulas_per_page = 3; 
$pages = array_chunk($raw_formulas, $formulas_per_page);

$toc = [];
foreach ($pages as $p_idx => $p_formulas) {
    if (!empty($p_formulas)) { // Make sure page has content
        $lesson_on_this_page = $p_formulas[0]['lesson_name'];
        if (!isset($toc[$lesson_on_this_page])) {
            $toc[$lesson_on_this_page] = $p_idx + 1; 
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Realistic 3D Formula Book on a Table</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <script id="MathJax-script" async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Caveat:wght@700&family=Permanent+Marker&family=Kalam:wght@400;700&display=swap');

        body {
            background-color: #3b2a20; /* Darker base for wood */
            background-image: 
                linear-gradient(rgba(0,0,0,0.1) 1px, transparent 1px),
                linear-gradient(90deg, rgba(0,0,0,0.1) 1px, transparent 1px);
            background-size: 20px 20px; /* Grain effect */
            background-repeat: repeat;
            display: flex; justify-content: center; align-items: center;
            height: 100vh; margin: 0; overflow: hidden;
            perspective: 3000px; /* Global perspective for 3D */
        }

        /* --- Filter Panel Styling --- */
        .filter-toggle-btn {
            position: fixed; left: 20px; top: 50%; transform: translateY(-50%);
            z-index: 5001; background: rgba(0,0,0,0.5); color: #fff;
            padding: 12px 15px; border-radius: 0 10px 10px 0;
            cursor: pointer; transition: left 0.3s ease-in-out;
            box-shadow: 2px 2px 10px rgba(0,0,0,0.3);
            border: none;
        }

        .filter-panel {
            position: fixed; left: -300px; /* Hidden by default */
            top: 0; height: 100%; width: 280px;
            background: linear-gradient(to top, rgba(17, 24, 39, 0.95), rgba(0,0,0,0.95));
            backdrop-filter: blur(10px);
            z-index: 5000; padding: 25px;
            box-shadow: 5px 0 20px rgba(0,0,0,0.5);
            transition: left 0.3s ease-in-out;
            color: #e5e7eb;
            overflow-y: auto;
        }
        .filter-panel.active { left: 0; }
        .filter-panel label { font-size: 14px; margin-bottom: 5px; color: #cbd5e1; }
        .filter-panel select, .filter-panel button {
            border-radius: 5px; font-size: 14px; margin-bottom: 15px;
        }
        .filter-panel .form-control { background-color: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: #fff; }
        .filter-panel .form-control option { background-color: #1f2937; color: #fff; }
        .filter-panel .btn-primary { background-color: #2563eb; border-color: #2563eb; }
        .filter-panel .btn-outline-light { color: #e5e7eb; border-color: #e5e7eb; }

        /* --- Book Styling --- */
        .book {
            width: 450px; height: 600px;
            position: relative; transform-style: preserve-3d;
            transform: rotateX(10deg) rotateY(-5deg); /* Slightly tilted on table */
            box-shadow: 20px 20px 50px rgba(0,0,0,0.7); /* Stronger shadow for realism */
            transition: transform 0.5s ease-in-out;
        }
        .book-wrapper {
            /* This div helps with the table shadow and central positioning */
            position: relative;
            transform: translateY(-20px) rotateX(20deg); /* Adjust book's position on table */
        }

        .page {
            width: 100%; height: 100%; position: absolute; top: 0; left: 0;
            transform-origin: left; transform-style: preserve-3d;
            transition: transform 1.2s cubic-bezier(0.4, 0, 0.2, 1); cursor: pointer;
        }

        .front, .back {
            position: absolute; width: 100%; height: 100%;
            backface-visibility: hidden; padding: 30px 35px 60px 35px;
            background-color: #fffef2; border-radius: 5px 15px 15px 5px;
            border: 1px solid #c1c1c1; display: flex; flex-direction: column;
            overflow: hidden; /* Ensure content doesn't spill */
        }

        .back { transform: rotateY(180deg); background: #e5e7eb; box-shadow: inset 20px 0 30px rgba(0,0,0,0.1); }
        .page.flipped { transform: rotateY(-180deg); }

        .front { background-image: linear-gradient(#e5e7eb 1px, transparent 1px); background-size: 100% 28px; }
        .front::before { content: ''; position: absolute; top: 0; left: 0; width: 30px; height: 100%; background: linear-gradient(to right, rgba(0,0,0,0.15), transparent); }

        .lesson-header { font-family: 'Permanent Marker', cursive; font-size: 22px; color: #1e3a8a; text-align: center; border-bottom: 2px solid #1e3a8a; margin-bottom: 10px; }
        .topic-title { font-family: 'Kalam', cursive; font-size: 16px; color: #b91c1c; margin-top: 10px; border-left: 4px solid #ef4444; padding-left: 10px; }
        .formula-box { padding: 15px 0; text-align: center; border-bottom: 1px dashed #cbd5e1; display: flex; align-items: center; justify-content: center; }
        .math-content { font-size: 1.5rem; color: #000; }
        .page-footer { position: absolute; bottom: 15px; left: 0; width: 100%; text-align: center; font-size: 12px; color: #64748b; font-family: monospace; }

        .cover-front { background: linear-gradient(135deg, #1e3a8a 0%, #000 100%) !important; color: #fbbf24; border-left: 8px solid #000; display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center; }
        .exit-btn { position: fixed; bottom: 20px; left: 20px; z-index: 5000; }
    </style>
</head>
<body>

<button class="filter-toggle-btn" id="filterToggleBtn">
    <i class="bi bi-funnel-fill fs-5"></i>
</button>

<div class="filter-panel" id="filterPanel">
    <h5 class="text-light mb-4">Filter Formulas</h5>
    <form method="GET">
        <div class="mb-3">
            <label for="subjectFilter" class="form-label">Subject:</label>
            <select name="subject" id="subjectFilter" class="form-select">
                <option value="">All Subjects</option>
                <?php while($s = mysqli_fetch_assoc($subjects_res)): ?>
                    <option value="<?= $s['subject_name'] ?>" <?= $subject_filter == $s['subject_name'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($s['subject_name']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="topicFilter" class="form-label">Topic:</label>
            <select name="topic" id="topicFilter" class="form-select">
                <option value="">All Topics</option>
                <?php while($t = mysqli_fetch_assoc($topics_res)): ?>
                    <option value="<?= $t['topic'] ?>" <?= $topic_filter == $t['topic'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($t['topic']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        
        <button type="submit" class="btn btn-primary w-100 mb-2"><i class="bi bi-check-circle"></i> Apply Filter</button>
        <a href="formula_book.php" class="btn btn-outline-light w-100"><i class="bi bi-arrow-clockwise"></i> Clear Filters</a>
    </form>
</div>

<a href="maths_bank.php" class="btn btn-sm btn-danger exit-btn rounded-pill px-3 shadow">
    <i class="bi bi-arrow-left"></i> Exit
</a>

<div class="book" id="book">
    <div class="page" onclick="flipPage(this)" style="z-index: 1000;">
        <div class="front cover-front">
            <i class="bi bi-journal-text" style="font-size: 60px;"></i>
            <h2 class="mt-3 fw-bold">FORMULA BOOK</h2>
            <?php if($subject_filter || $topic_filter): ?>
                <div class="badge bg-warning text-dark mt-2">
                    <?php if($subject_filter) echo htmlspecialchars($subject_filter) . " "; ?>
                    <?php if($topic_filter) echo htmlspecialchars($topic_filter) . " "; ?>
                    FILTERED
                </div>
            <?php endif; ?>
            <div class="mt-4 small border border-warning p-2 rounded">OPEN BOOK</div>
        </div>
        <div class="back"></div>
    </div>

    <div class="page" onclick="flipPage(this)" style="z-index: 999;">
        <div class="front">
            <h4 class="text-center fw-bold" style="font-family:'Permanent Marker'">INDEX</h4>
            <div class="mt-4">
                <?php if(empty($toc)): ?>
                    <p class="text-center text-muted mt-5">No formulas found.</p>
                <?php else: ?>
                    <?php foreach ($toc as $lesson => $page_num): ?>
                        <div class="d-flex justify-content-between border-bottom py-2" style="font-family:'Kalam'">
                            <span><?= htmlspecialchars($lesson) ?></span>
                            <span class="text-primary">Pg. <?= $page_num ?></span>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <div class="page-footer">CONTENTS</div>
        </div>
        <div class="back"></div>
    </div>

    <?php 
    $zIndex = 998;
    foreach ($pages as $p_index => $page_formulas): 
        $current_lesson = ""; $current_topic = "";
    ?>
    <div class="page" onclick="flipPage(this)" style="z-index: <?= $zIndex-- ?>;">
        <div class="front">
            <?php foreach ($page_formulas as $f): ?>
                <?php if ($f['lesson_name'] !== $current_lesson): $current_lesson = $f['lesson_name']; ?>
                    <div class="lesson-header"><?= htmlspecialchars($current_lesson) ?></div>
                <?php endif; ?>
                <?php if ($f['topic'] !== $current_topic): $current_topic = $f['topic']; ?>
                    <div class="topic-title"># <?= htmlspecialchars($current_topic) ?></div>
                <?php endif; ?>
                <div class="formula-box">
                    <div class="math-content">$$ <?= $f['formula_text'] ?> $$</div>
                </div>
            <?php endforeach; ?>
            <div class="page-footer">Page <?= str_pad($p_index + 1, 2, '0', STR_PAD_LEFT) ?></div>
        </div>
        <div class="back"></div>
    </div>
    <?php endforeach; ?>

    <div class="page" style="z-index: 0;">
        <div class="front d-flex align-items-center justify-content-center">
            <h5 class="text-muted">THE END</h5>
        </div>
        <div class="back"></div>
    </div>
</div>

<script>
    function flipPage(element) {
        if (element.classList.contains('flipped')) {
            element.classList.remove('flipped');
            setTimeout(() => { element.style.zIndex = element.getAttribute('data-original-z'); }, 600);
        } else {
            element.classList.add('flipped');
            element.style.zIndex = 1000;
        }
        setTimeout(() => { MathJax.typesetPromise(); }, 300);
    }
    document.querySelectorAll('.page').forEach(page => {
        page.setAttribute('data-original-z', page.style.zIndex);
    });

    // JavaScript for Filter Panel Toggle
    const filterToggleBtn = document.getElementById('filterToggleBtn');
    const filterPanel = document.getElementById('filterPanel');

    filterToggleBtn.addEventListener('click', () => {
        filterPanel.classList.toggle('active');
        // Optionally move the toggle button too
        if (filterPanel.classList.contains('active')) {
            filterToggleBtn.style.left = '280px'; 
            filterToggleBtn.style.borderRadius = '10px 0 0 10px';
        } else {
            filterToggleBtn.style.left = '20px';
            filterToggleBtn.style.borderRadius = '0 10px 10px 0';
        }
    });

    // Close panel if clicked outside
    document.addEventListener('click', (event) => {
        if (!filterPanel.contains(event.target) && !filterToggleBtn.contains(event.target) && filterPanel.classList.contains('active')) {
            filterPanel.classList.remove('active');
            filterToggleBtn.style.left = '20px';
            filterToggleBtn.style.borderRadius = '0 10px 10px 0';
        }
    });
</script>

</body>
</html>