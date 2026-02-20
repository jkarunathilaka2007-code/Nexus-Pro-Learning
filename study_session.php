<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['subject'])) {
    header("Location: revision.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$subject = mysqli_real_escape_string($conn, $_GET['subject']);
$selected_lesson = isset($_GET['lesson']) ? mysqli_real_escape_string($conn, $_GET['lesson']) : '';
$selected_type = isset($_GET['type']) ? mysqli_real_escape_string($conn, $_GET['type']) : '';
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 0; 

// --- Filters ---
$lesson_sql = "SELECT DISTINCT lesson_name FROM user_revisions WHERE user_id = '$user_id' AND subject_name = '$subject'";
$lesson_result = mysqli_query($conn, $lesson_sql);
$lessons = []; while($l_row = mysqli_fetch_assoc($lesson_result)) { $lessons[] = $l_row['lesson_name']; }

$type_sql = "SELECT DISTINCT paper_type FROM user_revisions WHERE user_id = '$user_id' AND subject_name = '$subject'";
$type_result = mysqli_query($conn, $type_sql);
$available_types = []; while($t_row = mysqli_fetch_assoc($type_result)) { $available_types[] = $t_row['paper_type']; }

// --- Main Query ---
$query = "SELECT * FROM user_revisions WHERE user_id = '$user_id' AND subject_name = '$subject'";
if ($selected_lesson !== '') { $query .= " AND lesson_name = '$selected_lesson'"; }
if ($selected_type !== '') { $query .= " AND paper_type = '$selected_type'"; }
$query .= " ORDER BY attempt_count ASC, RAND()";
if ($limit > 0) { $query .= " LIMIT $limit"; }

$result = mysqli_query($conn, $query);
$questions = [];
while ($row = mysqli_fetch_assoc($result)) { $questions[] = $row; }

// --- RECURSIVE FUNCTION ---
function render_nested_parts($nodes, $level = 0, $qIndex) {
    if (!$nodes) return;
    $counter = 1;

    foreach ($nodes as $node) {
        // Numbering Logic
        $numbering = "";
        if ($level == 0) $numbering = "(" . chr(96 + $counter) . ")"; // (a)
        elseif ($level == 1) $numbering = "(" . toRoman($counter) . ")"; // (i)
        else $numbering = "-";

        echo '<div class="sub-question-block" style="margin-left:'.($level * 20).'px; margin-top: 10px;">';
        
        echo '<div class="d-flex align-items-start gap-2">';
        echo '<div class="q-number">'.$numbering.'</div>';
        echo '<div class="w-100">';
        
        if(!empty($node['q'])) echo '<div class="q-text">'.nl2br(htmlspecialchars($node['q'])).'</div>';
        
        if (!empty($node['img'])) echo '<img src="'.$node['img'].'" class="paper-img">';

        if (!empty($node['tbl'])) {
            echo '<table class="exam-table">';
            foreach ($node['tbl'] as $row) {
                echo '<tr>';
                foreach ($row as $cell) echo '<td>'.htmlspecialchars($cell).'</td>';
                echo '</tr>';
            }
            echo '</table>';
        }

        // Auto-expanding textarea for better UX
        echo '<textarea class="paper-input no-print" rows="1" placeholder=".................................................................................................................................." oninput="autoResize(this); markAnswered('.$qIndex.')"></textarea>';

        if(!empty($node['a'])) {
            echo '<div class="model-ans"><b>Answer:</b> '.nl2br(htmlspecialchars($node['a'])).'</div>';
        }

        if (!empty($node['subs'])) render_nested_parts($node['subs'], $level + 1, $qIndex);
        
        echo '</div>'; 
        echo '</div>'; 
        echo '</div>';

        $counter++;
    }
}

function toRoman($number) {
    $map = array('M' => 1000, 'CM' => 900, 'D' => 500, 'CD' => 400, 'C' => 100, 'XC' => 90, 'L' => 50, 'XL' => 40, 'X' => 10, 'IX' => 9, 'V' => 5, 'IV' => 4, 'I' => 1);
    $returnValue = '';
    while ($number > 0) {
        foreach ($map as $roman => $int) {
            if ($number >= $int) {
                $number -= $int;
                $returnValue .= $roman;
                break;
            }
        }
    }
    return strtolower($returnValue);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exam Paper View</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    
    <style>
        body { background: #525659; padding: 20px 0; font-family: 'Cambria', 'Times New Roman', serif; }
        
        #printable-area { 
            background: #fff; width: 210mm; min-height: 297mm; margin: auto; padding: 15mm 15mm; 
            box-shadow: 0 0 15px rgba(0,0,0,0.5); position: relative; 
        }

        /* Typography */
        h2 { font-family: 'Arial', sans-serif; text-transform: uppercase; font-weight: 800; }
        .q-text { font-size: 15px; line-height: 1.5; color: #000; text-align: justify; margin-bottom: 5px; }
        .q-number { font-weight: bold; font-size: 15px; min-width: 30px; }

        /* Question Block - Prevents cutting in PDF */
        .q-block { break-inside: avoid; page-break-inside: avoid; margin-bottom: 25px; border-bottom: 1px dashed #ccc; padding-bottom: 20px; }
        
        /* Images & Tables */
        .paper-img { max-width: 100%; max-height: 250px; margin: 10px 0; display: block; border: 1px solid #eee; }
        .exam-table { border-collapse: collapse; width: auto; margin: 10px 0; font-size: 14px; }
        .exam-table td, .exam-table th { border: 1px solid #000; padding: 5px 15px; text-align: center; }

        /* Inputs */
        .paper-input { 
            width: 100%; border: none; border-bottom: 1px dotted #999; 
            resize: none; outline: none; overflow: hidden; background: transparent; 
            font-family: 'Courier New', monospace; font-size: 14px; color: #000080; margin-top: 5px; min-height: 30px;
        }
        .paper-input:focus { border-bottom: 1px solid #000; background: #f8f9fa; }

        /* Model Answers */
        .model-ans { display: none; margin-top: 10px; font-size: 14px; color: #d63384; background: #fff0f6; padding: 8px; border-left: 3px solid #d63384; }

        /* MCQ */
        .mcq-opt { padding: 6px 0; font-size: 15px; cursor: pointer; }
        .mcq-opt:hover { background: #f1f5f9; }
        .mcq-opt.selected { font-weight: bold; }
        .mcq-opt.selected::before { content: '● '; }
        .mcq-opt::before { content: '○ '; color: #777; }
        .ans-tick { color: green; font-weight: bold; display: none; margin-left: 10px; }
        .ans-cross { color: red; font-weight: bold; display: none; margin-left: 10px; }

        /* Result Layer (The Pop-up) */
        #resultLayer { display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.95); z-index: 2000; align-items: center; justify-content: center; backdrop-filter: blur(5px); }
        .res-card { background: #fff; width: 90%; max-width: 500px; border-radius: 15px; overflow: hidden; animation: slideUp 0.3s ease-out; }
        .res-header { background: #1e293b; color: #fff; padding: 25px; text-align: center; }
        .grade-circle { width: 100px; height: 100px; background: #2563eb; color: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 3rem; font-weight: bold; margin: -50px auto 15px; border: 6px solid #fff; }
        @keyframes slideUp { from { transform: translateY(20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }

        /* Controls */
        .controls { position: fixed; bottom: 20px; right: 20px; z-index: 999; }
        .filter-bar { background: #fff; padding: 10px; width: 210mm; margin: 0 auto 20px; display: flex; gap: 10px; border-radius: 4px; }

        @media print {
            body { background: #fff; padding: 0; }
            #printable-area { box-shadow: none; margin: 0; width: 100%; padding: 0; }
            .no-print, .controls, .filter-bar { display: none !important; }
            .q-block { page-break-inside: avoid; }
        }
    </style>
</head>
<body>

<div id="resultLayer">
    <div class="res-card">
        <div class="res-header"><h2>EXAM COMPLETED</h2></div>
        <div class="p-4 text-center">
            <div class="grade-circle" id="resGrade">-</div>
            <h5 id="resStatus" class="fw-bold text-muted">Thinking...</h5>
            
            <div class="row g-2 mt-4">
                <div class="col-6"><div class="p-2 border rounded bg-light">Accuracy<br><b id="resAccuracy" class="fs-4">0%</b></div></div>
                <div class="col-6"><div class="p-2 border rounded bg-light">Correct<br><b id="resCorrect" class="fs-4">0/0</b></div></div>
            </div>

            <div class="d-grid gap-2 mt-4">
                <button class="btn btn-primary fw-bold" onclick="dlPDF()">Download Result PDF</button>
                <button class="btn btn-outline-dark" onclick="closeLayer()">Review Answers</button>
            </div>
        </div>
    </div>
</div>

<div class="no-print filter-bar">
    <span class="fw-bold align-self-center me-2">FILTERS:</span>
    <select id="lFilter" class="form-select form-select-sm" onchange="applyF()">
        <option value="">All Lessons</option>
        <?php foreach($lessons as $l): ?><option value="<?=$l?>" <?=$selected_lesson==$l?'selected':''?>><?=$l?></option><?php endforeach; ?>
    </select>
    <select id="tFilter" class="form-select form-select-sm" onchange="applyF()">
        <option value="">All Types</option>
        <?php foreach($available_types as $t): ?><option value="<?=$t?>" <?=$selected_type==$t?'selected':''?>><?=$t?></option><?php endforeach; ?>
    </select>
    <div class="ms-auto fw-bold text-danger bg-white px-3 py-1 border rounded" id="timer">00:00</div>
    <button class="btn btn-sm btn-danger" onclick="location.href='revision.php'">EXIT</button>
</div>

<div id="printable-area">
    <div class="text-center border-bottom border-2 border-dark pb-3 mb-4">
        <h2 class="m-0">REVISION PAPER</h2>
        <div class="d-flex justify-content-between mt-2 small fw-bold text-uppercase">
            <span>Subject: <?=htmlspecialchars($subject)?></span>
            <span>Date: <?=date('Y-m-d')?></span>
        </div>
    </div>

    <div id="q-container">
        <?php foreach($questions as $index => $q): 
            $ctx = json_decode($q['question'], true);
            if(json_last_error() !== JSON_ERROR_NONE) $ctx = ['header_text' => $q['question'], 'image' => '', 'table' => []];
        ?>
            <div class="q-block" data-id="<?=$q['id']?>" data-type="<?=$q['paper_type']?>" data-correct="<?=$q['correct_idx']?>">
                
                <div class="d-flex align-items-start gap-2">
                    <div class="q-number fs-5"><?=($index + 1)?>.</div>
                    <div class="w-100">
                        <div class="q-text fw-bold fs-6"><?=nl2br(htmlspecialchars($ctx['header_text']))?></div>
                        <?php if(!empty($ctx['image'])): ?><img src="<?=$ctx['image']?>" class="paper-img"><?php endif; ?>
                        <?php if(!empty($ctx['table'])): ?>
                            <table class="exam-table">
                                <?php foreach($ctx['table'] as $r): ?><tr><?php foreach($r as $c): ?><td><?=htmlspecialchars($c)?></td><?php endforeach; ?></tr><?php endforeach; ?>
                            </table>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="ps-4 mt-2">
                    <?php if($q['paper_type'] == 'MCQ'): ?>
                        <div class="ms-2">
                            <?php $opts = json_decode($q['options_json']); if($opts): foreach($opts as $i => $opt): ?>
                                <div class="mcq-opt" onclick="selMCQ(<?=$index?>, <?=$i?>)" id="q<?=$index?>o<?=$i?>">
                                    (<?=($i+1)?>) <?=htmlspecialchars($opt)?>
                                    <span class="ans-tick">✓</span><span class="ans-cross">✗</span>
                                </div>
                            <?php endforeach; endif; ?>
                        </div>
                    <?php else: ?>
                        <?php 
                            $nested = json_decode($q['answer_text'], true);
                            if(is_array($nested)) render_nested_parts($nested, 0, $index);
                        ?>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<div class="controls no-print">
    <button class="btn btn-dark shadow px-4 py-2 fw-bold" id="subBtn" onclick="submitP()">SUBMIT PAPER</button>
</div>

<script>
    // 1. Timer & Auto-Resize
    let sec = 0; setInterval(() => { sec++; document.getElementById('timer').innerText = new Date(sec*1000).toISOString().substr(14,5); }, 1000);
    
    function autoResize(el) {
        el.style.height = 'auto';
        el.style.height = el.scrollHeight + 'px';
    }

    // 2. Filters & Selection
    function applyF() {
        location.href = `?subject=<?=urlencode($subject)?>&lesson=${document.getElementById('lFilter').value}&type=${document.getElementById('tFilter').value}&limit=<?=$limit?>`;
    }

    let uAns = {}, ansSet = new Set();
    function selMCQ(qI, oI) {
        document.querySelectorAll(`[id^="q${qI}o"]`).forEach(e => e.classList.remove('selected'));
        document.getElementById(`q${qI}o${oI}`).classList.add('selected');
        uAns[qI] = oI; ansSet.add(qI);
    }
    function markAnswered(qI) { ansSet.add(qI); }

    // 3. Submit & Show Results
    function submitP() {
        if(!ansSet.size) return alert("Please answer at least one question.");
        
        let score = 0, mcqC = 0, qIds=[], uC=[], cI=[];

        document.querySelectorAll('.q-block').forEach((b, i) => {
            if(ansSet.has(i)) {
                let id = b.dataset.id, type = b.dataset.type, corr = b.dataset.correct;
                qIds.push(id); cI.push(corr);

                if(type === 'MCQ') {
                    mcqC++;
                    let userVal = uAns[i];
                    uC.push(userVal);
                    document.getElementById(`q${i}o${corr}`).querySelector('.ans-tick').style.display = 'inline';
                    if(userVal == corr) score++;
                    else document.getElementById(`q${i}o${userVal}`).querySelector('.ans-cross').style.display = 'inline';
                } else {
                    uC.push('Text');
                    b.querySelectorAll('.model-ans').forEach(m => m.style.display = 'block');
                }
            } else {
                b.style.display = 'none'; 
            }
        });

        // Show Result Layer
        let acc = mcqC > 0 ? Math.round((score/mcqC)*100) : 100;
        document.getElementById('resGrade').innerText = acc >= 75 ? 'A' : (acc >= 50 ? 'B' : (acc >= 35 ? 'S' : 'F'));
        document.getElementById('resStatus').innerText = acc >= 75 ? 'Excellent!' : 'Keep Practicing';
        document.getElementById('resAccuracy').innerText = acc + "%";
        document.getElementById('resCorrect').innerText = score + "/" + mcqC;
        document.getElementById('resultLayer').style.display = 'flex';

        // AJAX Save
        let fd = new FormData();
        fd.append('subject', '<?=$subject?>'); fd.append('total', ansSet.size); fd.append('correct', score);
        fd.append('time', sec); fd.append('q_ids', JSON.stringify(qIds));
        fd.append('user_choices', JSON.stringify(uC)); fd.append('correct_indices', JSON.stringify(cI));
        fetch('save_session_final.php', { method:'POST', body:fd });

        document.getElementById('subBtn').style.display = 'none';
    }

    function closeLayer() { document.getElementById('resultLayer').style.display = 'none'; }

    // 4. Advanced PDF Generation (Fixing the Cut-off Issue)
    function dlPDF() {
        closeLayer();
        const element = document.getElementById('printable-area');

        // PRE-PROCESSING: Expand all textareas
        const textareas = element.querySelectorAll('textarea');
        textareas.forEach(ta => {
            ta.style.height = (ta.scrollHeight + 10) + 'px'; // Expand height to fit text
            ta.style.borderBottom = '1px solid #000'; // Make line visible
        });

        // PDF Generation
        html2pdf().set({
            margin: [10, 10, 10, 10], // Top, Left, Bottom, Right
            filename: 'My_Revision_Paper.pdf',
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2, useCORS: true, scrollY: 0 },
            pagebreak: { mode: ['avoid-all', 'css', 'legacy'] } // Crucial for not cutting questions
        }).from(element).save().then(() => {
            // Optional: Reset heights if user stays on page (or just leave them expanded)
        });
    }
</script>

</body>
</html>