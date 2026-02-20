<?php
session_start();
include 'db.php';
date_default_timezone_set("Asia/Colombo");

if (!isset($_SESSION['user_id']) || !isset($_GET['subject'])) {
    header("Location: dashboard.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$subject = mysqli_real_escape_string($conn, $_GET['subject']);

// 1. Data Fetching
$q = mysqli_query($conn, "SELECT * FROM trackers WHERE user_id = '$user_id' AND subject = '$subject'");
$trackers = []; $years_list = [];
while ($row = mysqli_fetch_assoc($q)) {
    $trackers[] = $row;
    for($y = $row['start_year']; $y <= $row['end_year']; $y++) { $years_list[] = $y; }
}
$years_list = array_unique($years_list); sort($years_list);
$latest_year = !empty($years_list) ? end($years_list) : date('Y');

$status_list = [];
$check_done = mysqli_query($conn, "SELECT paper_year, paper_type, question_no, is_hard FROM completed_questions WHERE user_id = '$user_id' AND subject = '$subject'");
while($d = mysqli_fetch_assoc($check_done)) {
    $status_list[$d['paper_year']][$d['paper_type']][$d['question_no']] = ($d['is_hard'] == 1) ? 'hard' : 'done';
}

$pdf_res = mysqli_query($conn, "SELECT * FROM user_pdfs WHERE user_id = '$user_id' AND subject_name = '$subject'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= strtoupper($subject) ?> | Master Prep</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        <?php if(isset($_GET['mode']) && $_GET['mode'] == 'split'): ?>
         body { overflow: hidden; }
         .main-wrapper { display: flex; width: 100vw; height: 100vh; }
         .pdf-side { width: 50%; border-right: 2px solid var(--accent); background: #0f172a; height: 100vh; position: relative; display: flex; flex-direction: column; }
         .content-side { width: 50%; height: 100vh; overflow-y: auto; }
         .pdf-toggle-btn { display: none; }
        <?php else: ?>
         .pdf-side { display: none; }
         .content-side { width: 100%; }
        <?php endif; ?>

      #splitPdfViewer { width: 100%; height: 100%; border: none; flex-grow: 1; }
      .pdf-nav-mini { background: #1e293b; padding: 10px; display: flex; gap: 8px; overflow-x: auto; flex-shrink: 0; }
       .mini-tab { background: #334155; padding: 4px 12px; border-radius: 6px; font-size: 11px; cursor: pointer; white-space: nowrap; color: white; transition: 0.2s; }
       .mini-tab:hover { background: var(--accent); }
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;800&display=swap');

        :root { 
            --bg: #060910; 
            --card-bg: rgba(17, 24, 39, 0.8);
            --accent: #6366f1; 
            --neon: #22d3ee; 
            --success: #10b981; 
            --danger: #f43f5e; 
        }

        body { 
            background: var(--bg); 
            color: #f8fafc; 
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
        }

        ::-webkit-scrollbar { width: 0px; height: 5px; }
        ::-webkit-scrollbar-thumb { background: var(--accent); border-radius: 10px; }

        .header-nav {
            background: rgba(6, 9, 16, 0.9);
            backdrop-filter: blur(12px);
            padding: 15px 20px;
            position: sticky; top: 0; z-index: 1000;
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }

        .session-panel {
            background: var(--card-bg);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 32px;
            padding: 30px;
            margin: 20px 0;
            text-align: center;
            box-shadow: 0 20px 40px rgba(0,0,0,0.4);
        }

        #main-timer { 
            font-size: 3.5rem; 
            font-weight: 800; 
            letter-spacing: -2px; 
            margin-bottom: 20px;
            background: linear-gradient(to bottom, #fff, #94a3b8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .progress-container {
            position: relative;
            height: 30px;
            background: #0f172a;
            border-radius: 50px;
            overflow: hidden;
            border: 1px solid rgba(255,255,255,0.05);
            margin-bottom: 10px;
        }
        .progress-bar-fill {
            height: 100%;
            width: 0%;
            background: linear-gradient(90deg, var(--accent), var(--neon));
            box-shadow: 0 0 20px rgba(99, 102, 241, 0.4);
            transition: 1s cubic-bezier(0.2, 0.8, 0.2, 1);
        }
        .progress-label {
            position: absolute; width: 100%; top: 0; left: 0;
            line-height: 30px; font-weight: 800; font-size: 0.75rem;
            color: white; text-transform: uppercase; letter-spacing: 1px;
        }

        .year-scroller { display: flex; gap: 12px; overflow-x: auto; padding: 10px 0; }
        .year-pill {
            padding: 12px 24px;
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 20px;
            color: #94a3b8;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
        }
        .year-pill.active {
            background: var(--accent);
            color: white;
            border-color: var(--accent);
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(99, 102, 241, 0.3);
        }
        .hidden-filter { display: none !important; }

        .grid-panel { opacity: 0.15; pointer-events: none; transition: 0.5s ease; margin-top: 30px; }
        .grid-panel.active { opacity: 1; pointer-events: all; }

        .type-header { 
            display: flex; align-items: center; gap: 10px; margin-bottom: 15px; 
            font-size: 0.75rem; font-weight: 800; color: var(--neon); letter-spacing: 1.5px;
        }

        .q-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(48px, 1fr)); gap: 12px; margin-bottom: 35px; }
        
        .q-btn {
            aspect-ratio: 1; border-radius: 16px;
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.08);
            color: #cbd5e1; font-weight: 700; font-size: 1rem;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
        }
        .q-btn:active { transform: scale(0.9); }
        .q-btn.done { background: var(--success) !important; color: white; border: none; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3); }
        .q-btn.hard { background: var(--danger) !important; color: white; border: none; box-shadow: 0 4px 12px rgba(244, 63, 94, 0.3); }
        
        .q-note-trigger {
            position: absolute; top: -10px; right: -10px;
            width: 24px; height: 24px; background: var(--accent);
            border-radius: 50%; color: white; font-size: 11px;
            display: none; align-items: center; justify-content: center;
            border: 2px solid var(--bg); z-index: 10; cursor: pointer;
            box-shadow: 0 4px 10px rgba(99, 102, 241, 0.4);
        }
        .q-btn.done .q-note-trigger, .q-btn.hard .q-note-trigger { display: flex; }

        .q-btn .note-dot {
            position: absolute; bottom: 4px; left: 50%; transform: translateX(-50%);
            width: 6px; height: 6px; background: #fff;
            border-radius: 50%; display: none; box-shadow: 0 0 5px #fff;
        }

        #focusLayer {
            position: fixed; inset: 0; background: #000; z-index: 2000;
            display: none; flex-direction: column; align-items: center; justify-content: center;
        }
        #focusClock { font-size: 8rem; font-weight: 800; color: #fff; margin-bottom: 2rem; }

        .btn-circle {
            width: 48px; height: 48px; border-radius: 16px;
            background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);
            color: white; display: flex; align-items: center; justify-content: center;
            transition: 0.3s;
        }
        .btn-circle:hover { background: var(--accent); border-color: var(--accent); }

        /* SweetAlert Textarea Styling */
        .swal-format-btn {
            border: 1px solid rgba(255,255,255,0.2);
            padding: 5px 12px;
            border-radius: 6px;
            background: rgba(255,255,255,0.05);
            color: #fff;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.2s;
        }
        .swal-format-btn:hover { background: var(--accent); border-color: var(--accent); }
    </style>
</head>
<body>

<div id="focusLayer">
    <p class="opacity-50 text-uppercase tracking-widest">Stay Focused</p>
    <div id="focusClock">00:00:00</div>
    <button class="btn btn-outline-danger rounded-pill px-5" onclick="exitFocus()">EXIT FOCUS</button>
</div>

<div class="main-wrapper">

    <div class="pdf-side">
        <div class="pdf-nav-mini">
            <?php while($p = mysqli_fetch_assoc($pdf_res)): ?>
                <div class="mini-tab" onclick="document.getElementById('splitPdfViewer').src='<?= $p['file_path'] ?>'">
                    <i class="fa fa-file-pdf me-1"></i> <?= htmlspecialchars($p['pdf_title']) ?>
                </div>
            <?php endwhile; ?>
            <a href="start_preperation.php?subject=<?= $subject ?>" class="mini-tab bg-danger text-white text-decoration-none">Close</a>
        </div>
        <iframe id="splitPdfViewer" src=""></iframe>
    </div>

    <div class="content-side">
        <div class="header-nav d-flex justify-content-between align-items-center">
    <div class="d-flex align-items-center gap-2">
        <a href="dashboard.php" class="btn-circle text-decoration-none"><i class="fa fa-chevron-left"></i></a>
        <a href="study_mode.php?subject=<?= urlencode($subject) ?>" class="btn btn-info rounded-pill px-4 fw-800">
            <i class="fa fa-book-reader me-2"></i> STUDY MODE (PDF)
        </a>
    </div>
    <div class="text-center">
        <div class="small opacity-50 fw-bold text-uppercase" style="font-size: 0.6rem; letter-spacing: 2px;">Targeting</div>
        <div class="fw-800 text-white"><?= $subject ?></div>
    </div>
</div>

<div class="container">
    <div class="session-panel">
        <div id="main-timer">00:00:00</div>
        
        <div class="d-flex justify-content-center gap-3 mb-4">
            <button id="startBtn" class="btn btn-primary rounded-pill px-5 py-2 fw-800" onclick="startSession()">START STUDY</button>
            <button id="stopBtn" class="btn btn-outline-danger rounded-pill px-5 py-2 fw-800 d-none" onclick="endSession()">FINISH SESSION</button>
        </div>

        <div class="progress-container">
            <div id="progressFill" class="progress-bar-fill"></div>
            <div id="percLabel" class="progress-label">0% COMPLETED</div>
        </div>
        <small class="text-muted"><i class="fa fa-info-circle me-1"></i> Use Note Icon or Long-press to add notes</small>
    </div>

    <div class="year-scroller" id="pillScroll">
        <?php foreach(array_reverse($years_list) as $y): ?>
            <div class="year-pill" id="pill-<?= $y ?>" onclick="changeYear(<?= $y ?>)"><?= $y ?></div>
        <?php endforeach; ?>
    </div>

    <div id="gridPanel" class="grid-panel">
        <?php foreach($years_list as $y): ?>
            <div class="year-content-box" id="content-<?= $y ?>" style="display:none;">
                <?php foreach($trackers as $t): 
                    $type = $t['paper_type']; $count = $t['question_count']; ?>
                    <div class="type-group mb-4" data-ptype="<?= $type ?>">
                        <div class="type-header">
                            <i class="fa fa-circle small"></i> <?= strtoupper($type) ?> PAPER
                        </div>
                        <div class="q-grid">
                            <?php for($i = 1; $i <= $count; $i++): $st = $status_list[$y][$type][$i] ?? ''; ?>
                                <button class="q-btn <?= $st ?>" id="q-<?= $y ?>-<?= $type ?>-<?= $i ?>"
                                     onclick="mark(<?= $y ?>, '<?= $type ?>', <?= $i ?>, 0)"
                                     ondblclick="mark(<?= $y ?>, '<?= $type ?>', <?= $i ?>, 1)"
                                     oncontextmenu="openNote(event, <?= $y ?>, '<?= $type ?>', <?= $i ?>)">
                                    <?= $i ?>
                                    <span class="q-note-trigger" onclick="openNote(event, <?= $y ?>, '<?= $type ?>', <?= $i ?>)">
                                        <i class="fa fa-edit"></i>
                                    </span>
                                    <span class="note-dot" id="dot-<?= $y ?>-<?= $type ?>-<?= $i ?>"></span>
                                </button>
                            <?php endfor; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<audio id="alarm" src="https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3"></audio>

<script>
let mSec = 0, mInt = null, active = false, fTime = 0, fInt = null;
let currentYear = <?= $latest_year ?>;

function startSession() {
    if(active) return;
    active = true;
    mInt = setInterval(() => {
        mSec++;
        let h = Math.floor(mSec/3600).toString().padStart(2,'0');
        let m = Math.floor((mSec%3600)/60).toString().padStart(2,'0');
        let s = (mSec%60).toString().padStart(2,'0');
        document.getElementById('main-timer').innerText = `${h}:${m}:${s}`;
    }, 1000);
    document.getElementById('startBtn').classList.add('d-none');
    document.getElementById('stopBtn').classList.remove('d-none');
    document.getElementById('gridPanel').classList.add('active');
}

function endSession() {
    Swal.fire({
        title: 'Finish & Save?', text: "This will record your study duration.", icon: 'question',
        background: '#111827', color: '#fff', showCancelButton: true, confirmButtonColor: '#6366f1'
    }).then(r => {
        if(r.isConfirmed) {
            clearInterval(mInt);
            let dur = document.getElementById('main-timer').innerText;
            
            let fd = new FormData();
            fd.append('subject', '<?= $subject ?>');
            fd.append('duration', dur);

            fetch('save_session.php', {
                method: 'POST',
                body: fd
            }).then(() => {
                Swal.fire({ icon:'success', title:'Session Saved', background:'#111827', color:'#fff' })
                .then(() => location.reload());
            });
        }
    });
}

// FIXED: General Note Year Selection
function openGeneralNote() {
    // Current active year pill එකෙන් value එක අරගන්න
    let activePill = document.querySelector('.year-pill.active');
    let yearToUse = activePill ? activePill.innerText.trim() : currentYear;
    openNote(null, yearToUse, 'GENERAL', 0);
}

async function openFilter() {
    const { value: f } = await Swal.fire({
        title: 'Filter Content', background: '#111827', color: '#fff',
        html: `
            <select id="fT" class="swal2-input w-100 m-0"><option value="all">All Papers</option><option value="MCQ">MCQ</option><option value="Essay">Essay</option><option value="Structured">Structured</option></select>
            <input id="fS" type="number" class="swal2-input w-100 mt-3" placeholder="Start Year" value="<?= $years_list[0] ?? date('Y') ?>">
            <input id="fE" type="number" class="swal2-input w-100 mt-3" placeholder="End Year" value="<?= end($years_list) ?>">`,
        preConfirm: () => ({ t: document.getElementById('fT').value, s: parseInt(document.getElementById('fS').value), e: parseInt(document.getElementById('fE').value) })
    });
    if(f) {
        document.querySelectorAll('.year-pill').forEach(p => {
            let y = parseInt(p.innerText);
            p.classList.toggle('hidden-filter', !(y >= f.s && y <= f.e));
        });
        document.querySelectorAll('.type-group').forEach(g => {
            g.classList.toggle('hidden-filter', !(f.t === 'all' || g.dataset.ptype === f.t));
        });
        let first = document.querySelector('.year-pill:not(.hidden-filter)');
        if(first) changeYear(first.innerText.trim());
        updateProgress();
    }
}

function mark(y, t, q, h) {
    if(event.target.closest('.q-note-trigger')) return; 

    const btn = document.getElementById(`q-${y}-${t}-${q}`);
    let action = (btn.classList.contains('done') && h === 0) ? 'delete' : 'add';
    fetch('mark_question.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `subject=<?= $subject ?>&year=${y}&type=${t}&q_no=${q}&action=${action}&is_hard=${h}`
    }).then(() => {
        btn.classList.remove('done', 'hard');
        if(action === 'add') btn.classList.add(h ? 'hard' : 'done');
        updateProgress();
    });
}

function updateProgress() {
    let btns = document.querySelectorAll('.type-group:not(.hidden-filter) .q-btn');
    let done = document.querySelectorAll('.type-group:not(.hidden-filter) .q-btn.done, .type-group:not(.hidden-filter) .q-btn.hard').length;
    let p = btns.length > 0 ? Math.round((done/btns.length)*100) : 0;
    document.getElementById('progressFill').style.width = p + '%';
    document.getElementById('percLabel').innerText = `${p}% COMPLETED`;
    checkNotesStatus();
}

async function openNote(event, y, t, q) {
    if(event) {
        event.preventDefault();
        event.stopPropagation();
    }
    
    // Fetch existing notes
    const response = await fetch(`get_note.php?subject=<?= $subject ?>&year=${y}&type=${t}&q_no=${q}`);
    let currentFullNote = await response.text();
    if(currentFullNote === "null") currentFullNote = "";

    const { value: result } = await Swal.fire({
        title: q === 0 ? `General Note (${y})` : `Note: ${y} ${t} Q${q}`,
        background: '#111827',
        color: '#fff',
        html: `
            <div class="text-start small text-muted mb-2">New notes will be appended to your collection.</div>
            <div class="text-start mb-2 d-flex gap-2">
                <button type="button" class="swal-format-btn" onclick="insertFormat('# ')"><i class="fa fa-heading"></i></button>
                <button type="button" class="swal-format-btn" onclick="insertFormat('* ')"><i class="fa fa-list"></i></button>
            </div>
            <textarea id="swal-note-text" class="swal2-textarea" style="width: 100%; height: 180px; margin: 0; background: #000; color: #fff; border: 1px solid #333;" placeholder="Write your new note here..."></textarea>
            <div class="mt-3 text-start">
                <label class="small text-muted mb-1">Attach Image (Optional)</label>
                <input type="file" id="swal-note-img" class="form-control bg-dark text-white border-secondary" accept="image/*">
            </div>
        `,
        confirmButtonText: 'Append Note',
        confirmButtonColor: '#6366f1',
        showCancelButton: true,
        preConfirm: () => {
            return {
                newText: document.getElementById('swal-note-text').value,
                image: document.getElementById('swal-note-img').files[0]
            }
        }
    });

    if (result && result.newText.trim() !== "") {
        let timestamp = new Date().toLocaleString();
        let separator = currentFullNote.trim() !== "" ? "\n\n--- Added on " + timestamp + " ---\n" : "";
        let finalNote = currentFullNote + separator + result.newText;

        let formData = new FormData();
        formData.append('subject', '<?= $subject ?>');
        formData.append('year', y);
        formData.append('type', t);
        formData.append('q_no', q);
        formData.append('note', finalNote);
        if (result.image) formData.append('image', result.image);

        fetch('save_note.php', {
            method: 'POST',
            body: formData
        }).then(() => {
            checkNotesStatus();
            Swal.fire({ icon: 'success', title: 'Note Appended', toast: true, position: 'top-end', showConfirmButton: false, timer: 2000, background: '#111827', color: '#fff' });
        });
    }
}

function insertFormat(symbol) {
    const textarea = document.getElementById('swal-note-text');
    const start = textarea.selectionStart;
    const end = textarea.selectionEnd;
    const text = textarea.value;
    const prefix = (text.length > 0 && start > 0 && text[start-1] !== '\n') ? '\n' + symbol : symbol;
    textarea.value = text.substring(0, start) + prefix + text.substring(end);
    textarea.focus();
    textarea.selectionStart = textarea.selectionEnd = start + prefix.length;
}

function checkNotesStatus() {
    fetch(`get_all_notes_status.php?subject=<?= $subject ?>`)
    .then(r => r.json())
    .then(data => {
        document.querySelectorAll('.note-dot').forEach(d => d.style.display = 'none');
        data.forEach(n => {
            const dot = document.getElementById(`dot-${n.paper_year}-${n.paper_type}-${n.question_no}`);
            if(dot) dot.style.display = 'block';
        });
    });
}

async function openTimerModal() {
    const { value: v } = await Swal.fire({
        title: 'Focus Countdown', background: '#111827', color: '#fff',
        html: '<div class="d-flex gap-2"><input id="th" type="number" placeholder="H" class="swal2-input m-0"><input id="tm" type="number" placeholder="M" class="swal2-input m-0"><input id="ts" type="number" placeholder="S" class="swal2-input m-0"></div>',
        preConfirm: () => [document.getElementById('th').value, document.getElementById('tm').value, document.getElementById('ts').value]
    });
    if(v) {
        let sec = (parseInt(v[0]||0)*3600) + (parseInt(v[1]||0)*60) + parseInt(v[2]||0);
        if(sec > 0) {
            if(!active) startSession();
            fTime = sec;
            document.getElementById('focusLayer').style.display = 'flex';
            fInt = setInterval(() => {
                fTime--;
                let hh = Math.floor(fTime/3600).toString().padStart(2,'0');
                let mm = Math.floor((fTime%3600)/60).toString().padStart(2,'0');
                let ss = (fTime%60).toString().padStart(2,'0');
                document.getElementById('focusClock').innerText = `${hh}:${mm}:${ss}`;
                if(fTime <= 0) { clearInterval(fInt); document.getElementById('alarm').play(); exitFocus(); }
            }, 1000);
        }
    }
}
function exitFocus() { clearInterval(fInt); document.getElementById('focusLayer').style.display = 'none'; }

function changeYear(y) {
    currentYear = y;
    document.querySelectorAll('.year-pill').forEach(p => p.classList.remove('active'));
    document.getElementById('pill-'+y).classList.add('active');
    document.querySelectorAll('.year-content-box').forEach(b => b.style.display = 'none');
    document.getElementById('content-'+y).style.display = 'block';
}

window.onload = () => { changeYear(<?= $latest_year ?>); updateProgress(); };
</script>

    </div> </div> </body>
</html>