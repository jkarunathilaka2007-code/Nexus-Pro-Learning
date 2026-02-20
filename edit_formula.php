<?php
session_start();
include 'db.php';
if (!isset($_SESSION['user_id'])) { die("Please login to continue."); }
$user_id = $_SESSION['user_id'];

if (!isset($_GET['id'])) { header("Location: maths_bank.php"); exit(); }
$id = mysqli_real_escape_string($conn, $_GET['id']);

// සූත්‍රයේ පරණ දත්ත ලබා ගැනීම
$get_sql = "SELECT * FROM user_formulas WHERE id = '$id' AND user_id = '$user_id'";
$res = mysqli_query($conn, $get_sql);
$f_data = mysqli_fetch_assoc($res);

if (!$f_data) { die("Formula not found or access denied."); }

// User subjects
$user_query = "SELECT subjects FROM users WHERE id = '$user_id'";
$user_res = mysqli_query($conn, $user_query);
$u_info = mysqli_fetch_assoc($user_res);
$my_subjects = explode(',', $u_info['subjects']);

// Datalist Suggestions
$suggest_res = mysqli_query($conn, "SELECT DISTINCT lesson_name, topic, sub_topic FROM user_formulas WHERE user_id = '$user_id'");
$ex_lessons = []; $ex_topics = []; $ex_subtopics = [];
while ($s_row = mysqli_fetch_assoc($suggest_res)) {
    if($s_row['lesson_name']) $ex_lessons[] = $s_row['lesson_name'];
    if($s_row['topic']) $ex_topics[] = $s_row['topic'];
    if($s_row['sub_topic']) $ex_subtopics[] = $s_row['sub_topic'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Formula | FormulaDB</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <script>window.MathJax = { tex: { inlineMath: [['$', '$']], displayMath: [['$$', '$$']] } };</script>
    <script id="MathJax-script" async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
    
    <style>
        :root { --glass: rgba(255, 255, 255, 0.05); --border: rgba(255, 255, 255, 0.1); }
        body { background: #0b0f19; color: #e2e8f0; font-family: 'Inter', sans-serif; min-height: 100vh; }
        .main-wrapper { max-width: 1200px; margin: auto; padding: 30px 15px; }
        .editor-card { background: #111827; border: 1px solid #1f2937; border-radius: 20px; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5); }
        .kb-group { background: var(--glass); border-radius: 12px; padding: 12px; margin-bottom: 12px; border: 1px solid var(--border); }
        .kb-title { font-size: 10px; font-weight: 800; color: #60a5fa; text-transform: uppercase; margin-bottom: 8px; display: block; letter-spacing: 1px; }
        .kb-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(45px, 1fr)); gap: 6px; }
        .kb-btn { background: #1f2937; border: 1px solid #374151; color: white; padding: 8px 2px; font-size: 14px; border-radius: 6px; cursor: pointer; transition: 0.2s; min-height: 40px; }
        .kb-btn:hover { background: #3b82f6; border-color: #60a5fa; transform: translateY(-2px); }
        .preview-box { min-height: 200px; background: rgba(0,0,0,0.15); border: 2px dashed #374151; border-radius: 15px; display: flex; align-items: center; justify-content: center; font-size: 2.2rem; color: #60a5fa; margin-top: 10px; padding: 10px; overflow-x: auto;}
        .form-control, .form-select { background: #1f2937 !important; border: 1px solid #374151 !important; color: white !important; }
        .math-kb-wrapper { max-height: 500px; overflow-y: auto; padding-right: 8px; }
        .math-kb-wrapper::-webkit-scrollbar { width: 5px; }
        .math-kb-wrapper::-webkit-scrollbar-thumb { background: #374151; border-radius: 10px; }
    </style>
</head>
<body onload="updatePreview()">

<div class="main-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-pencil-square text-warning"></i> Edit Formula</h2>
        <a href="maths_bank.php" class="btn btn-outline-light rounded-pill px-4 shadow-sm">Back to Library</a>
    </div>

    <div class="editor-card p-4">
        <div class="row g-4">
            <div class="col-lg-7">
                <div class="row g-2 mb-3">
                    <div class="col-md-6">
                        <label class="small fw-bold text-secondary mb-1">Subject</label>
                        <select id="subject" class="form-select">
                            <?php foreach($my_subjects as $sub): ?>
                                <option value="<?=trim($sub)?>" <?= ($f_data['subject_name'] == trim($sub)) ? 'selected' : '' ?>><?=trim($sub)?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="small fw-bold text-danger mb-1">Lesson Name *</label>
                        <input type="text" list="lList" id="lesson" class="form-control" value="<?= htmlspecialchars($f_data['lesson_name']) ?>" autocomplete="off" required>
                        <datalist id="lList">
                            <?php foreach(array_unique($ex_lessons) as $l): ?><option value="<?=$l?>"><?php endforeach; ?>
                        </datalist>
                    </div>
                    <div class="col-md-6">
                        <label class="small fw-bold text-secondary mb-1">Topic</label>
                        <input type="text" list="tList" id="topic" class="form-control" value="<?= htmlspecialchars($f_data['topic']) ?>" autocomplete="off">
                        <datalist id="tList">
                            <?php foreach(array_unique($ex_topics) as $t): ?><option value="<?=$t?>"><?php endforeach; ?>
                        </datalist>
                    </div>
                    <div class="col-md-6">
                        <label class="small fw-bold text-secondary mb-1">Sub-topic</label>
                        <input type="text" list="stList" id="subtopic" class="form-control" value="<?= htmlspecialchars($f_data['sub_topic']) ?>" autocomplete="off">
                        <datalist id="stList">
                            <?php foreach(array_unique($ex_subtopics) as $st): ?><option value="<?=$st?>"><?php endforeach; ?>
                        </datalist>
                    </div>
                </div>

                <div class="math-kb-wrapper">
                    <div class="kb-group">
                        <span class="kb-title">Arithmetic & Structure</span>
                        <div class="kb-grid">
                            <button class="kb-btn" onclick="insert('+')">+</button>
                            <button class="kb-btn" onclick="insert('-')">-</button>
                            <button class="kb-btn" onclick="insert('\\times')">$\times$</button>
                            <button class="kb-btn" onclick="insert('\\div')">$\div$</button>
                            <button class="kb-btn" onclick="insert('\\frac{ }{ }')">$\frac{a}{b}$</button>
                            <button class="kb-btn" onclick="insert('^{ }')">$x^n$</button>
                            <button class="kb-btn" onclick="insert('_{ }')">$x_n$</button>
                            <button class="kb-btn" onclick="insert('\\sqrt{ }')">$\sqrt{x}$</button>
                            <button class="kb-btn" onclick="insert('\\sqrt[ ]{ }')">$\sqrt[n]{x}$</button>
                            <button class="kb-btn" onclick="insert('\\pm')">$\pm$</button>
                        </div>
                    </div>

                    <div class="kb-group">
                        <span class="kb-title">Trigonometry</span>
                        <div class="kb-grid">
                            <button class="kb-btn" onclick="insert('\\sin')">sin</button>
                            <button class="kb-btn" onclick="insert('\\cos')">cos</button>
                            <button class="kb-btn" onclick="insert('\\tan')">tan</button>
                            <button class="kb-btn" onclick="insert('\\sec')">sec</button>
                            <button class="kb-btn" onclick="insert('\\csc')">csc</button>
                            <button class="kb-btn" onclick="insert('\\cot')">cot</button>
                            <button class="kb-btn" onclick="insert('\\sin^{-1}')">$sin^{-1}$</button>
                            <button class="kb-btn" onclick="insert('\\cos^{-1}')">$cos^{-1}$</button>
                            <button class="kb-btn" onclick="insert('\\tan^{-1}')">$tan^{-1}$</button>
                            <button class="kb-btn" onclick="insert('^{\\circ}')">$^{\circ}$</button>
                        </div>
                    </div>

                    <div class="kb-group">
                        <span class="kb-title">Greek Letters</span>
                        <div class="kb-grid">
                            <button class="kb-btn" onclick="insert('\\pi')">$\pi$</button>
                            <button class="kb-btn" onclick="insert('\\theta')">$\theta$</button>
                            <button class="kb-btn" onclick="insert('\\alpha')">$\alpha$</button>
                            <button class="kb-btn" onclick="insert('\\beta')">$\beta$</button>
                            <button class="kb-btn" onclick="insert('\\gamma')">$\gamma$</button>
                            <button class="kb-btn" onclick="insert('\\lambda')">$\lambda$</button>
                            <button class="kb-btn" onclick="insert('\\omega')">$\omega$</button>
                            <button class="kb-btn" onclick="insert('\\phi')">$\phi$</button>
                            <button class="kb-btn" onclick="insert('\\mu')">$\mu$</button>
                            <button class="kb-btn" onclick="insert('\\Delta')">$\Delta$</button>
                        </div>
                    </div>

                    <div class="kb-group">
                        <span class="kb-title">Calculus & Advanced</span>
                        <div class="kb-grid">
                            <button class="kb-btn" onclick="insert('\\int_{ }^{ }')">$\int$</button>
                            <button class="kb-btn" onclick="insert('\\frac{d}{dx}')">$\frac{d}{dx}$</button>
                            <button class="kb-btn" onclick="insert('\\lim_{x \\to \\infty}')">$\lim$</button>
                            <button class="kb-btn" onclick="insert('\\sum_{ }^{ }')">$\sum$</button>
                            <button class="kb-btn" onclick="insert('\\infty')">$\infty$</button>
                            <button class="kb-btn" onclick="insert('\\log_{ }')">$\log$</button>
                            <button class="kb-btn" onclick="insert('\\ln')">$\ln$</button>
                            <button class="kb-btn" onclick="insert('\\partial')">$\partial$</button>
                            <button class="kb-btn" onclick="insert('\\nabla')">$\nabla$</button>
                            <button class="kb-btn" onclick="insert('\\to')">$\to$</button>
                        </div>
                    </div>

                    <div class="kb-group">
                        <span class="kb-title">Sets & Logic</span>
                        <div class="kb-grid">
                            <button class="kb-btn" onclick="insert('\\in')">$\in$</button>
                            <button class="kb-btn" onclick="insert('\\notin')">$\notin$</button>
                            <button class="kb-btn" onclick="insert('\\subset')">$\subset$</button>
                            <button class="kb-btn" onclick="insert('\\cup')">$\cup$</button>
                            <button class="kb-btn" onclick="insert('\\cap')">$\cap$</button>
                            <button class="kb-btn" onclick="insert('\\forall')">$\forall$</button>
                            <button class="kb-btn" onclick="insert('\\exists')">$\exists$</button>
                            <button class="kb-btn" onclick="insert('\\implies')">$\implies$</button>
                            <button class="kb-btn" onclick="insert('\\iff')">$\iff$</button>
                            <button class="kb-btn" onclick="insert('\\therefore')">$\therefore$</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <label class="small fw-bold text-secondary mb-1">LaTeX Editor</label>
                <textarea id="formulaText" class="form-control mb-3" rows="6"><?= htmlspecialchars($f_data['formula_text']) ?></textarea>
                
                <label class="small fw-bold text-success mb-1">Visual Preview</label>
                <div class="preview-box" id="previewArea">...</div>
                
                <button class="btn btn-warning btn-lg w-100 mt-4 py-3 rounded-4 fw-bold shadow" onclick="updateFormula()">
                    <i class="bi bi-check2-circle"></i> UPDATE FORMULA
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    const input = document.getElementById('formulaText');
    const preview = document.getElementById('previewArea');

    function insert(val) {
        let start = input.selectionStart;
        input.value = input.value.substring(0, start) + val + input.value.substring(input.selectionEnd);
        input.focus();
        if(val.includes('{')) {
            let pos = start + val.indexOf('{') + 1;
            input.setSelectionRange(pos, pos);
        }
        updatePreview();
    }

    input.addEventListener('input', updatePreview);
    function updatePreview() {
        preview.innerHTML = input.value ? '$$' + input.value + '$$' : '...';
        MathJax.typesetPromise([preview]);
    }

    function updateFormula() {
        if(!document.getElementById('lesson').value || !input.value) return alert("Please fill lesson name and formula!");
        
        let fd = new FormData();
        fd.append('id', '<?= $id ?>');
        fd.append('subject', document.getElementById('subject').value);
        fd.append('lesson', document.getElementById('lesson').value);
        fd.append('topic', document.getElementById('topic').value);
        fd.append('subtopic', document.getElementById('subtopic').value);
        fd.append('formula', input.value);

        fetch('update_formula_process.php', { method: 'POST', body: fd })
        .then(res => res.text())
        .then(data => {
            alert("Updated Successfully!");
            window.location.href = 'maths_bank.php';
        })
        .catch(err => alert("Error: " + err));
    }
</script>
</body>
</html>