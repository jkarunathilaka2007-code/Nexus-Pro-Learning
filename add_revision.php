<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = "";

// --- Save Process ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_revision'])) {
    $subject = mysqli_real_escape_string($conn, $_POST['subject_name']);
    $paper_type = mysqli_real_escape_string($conn, $_POST['paper_type']);
    $lesson = mysqli_real_escape_string($conn, $_POST['lesson_name']);
    $date = date('Y-m-d H:i:s');

    // 1. Handle Main Context (JSON)
    $main_data = [
        'header_text' => $_POST['question'],
        'image' => $_POST['main_image_data'] ?? '',
        'table' => !empty($_POST['main_table_data']) ? json_decode($_POST['main_table_data']) : []
    ];
    // Encode properly for DB
    $main_question_json = mysqli_real_escape_string($conn, json_encode($main_data));

    // 2. Default Values
    $options_json = "NULL";
    $correct_idx = "NULL";
    $answer_text = "NULL";

    if ($paper_type === 'MCQ') {
        if(isset($_POST['mcq_options'])) {
            $options_json = "'" . mysqli_real_escape_string($conn, json_encode($_POST['mcq_options'])) . "'";
            $correct_idx = (int)$_POST['correct_idx'];
        }
    } else {
        // 3. Structured/Essay (Nested JSON data)
        if(!empty($_POST['nested_data_json'])) {
            $answer_text = "'" . mysqli_real_escape_string($conn, $_POST['nested_data_json']) . "'";
        }
    }

    $sql = "INSERT INTO user_revisions (user_id, subject_name, paper_type, lesson_name, question, options_json, correct_idx, answer_text, created_at) 
            VALUES ('$user_id', '$subject', '$paper_type', '$lesson', '$main_question_json', $options_json, $correct_idx, $answer_text, '$date')";

    if (mysqli_query($conn, $sql)) {
        $message = "<div class='alert alert-success border-0 rounded-4 shadow-sm py-3'>🚀 Revision Saved Successfully!</div>";
    } else {
        $message = "<div class='alert alert-danger border-0 rounded-4 shadow-sm'>Error: " . mysqli_error($conn) . "</div>";
    }
}

// Get subjects for dropdown
$user_res = mysqli_query($conn, "SELECT subjects FROM users WHERE id = '$user_id'");
$user_row = mysqli_fetch_assoc($user_res);
$subjects_list = ($user_row && $user_row['subjects']) ? explode(',', $user_row['subjects']) : [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nexus | Add Revision</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --accent: #6366f1; --bg: #020617; --card: #0f172a; --border: #334155; }
        body { background: var(--bg); color: #e2e8f0; font-family: 'Plus Jakarta Sans', sans-serif; padding: 20px; }
        .form-container { max-width: 1000px; margin: auto; background: var(--card); padding: 35px; border-radius: 30px; border: 1px solid rgba(255,255,255,0.05); box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5); }
        .input-box { background: #1e293b !important; border: 1px solid var(--border) !important; color: white !important; border-radius: 12px; margin-bottom: 8px; }
        .part-box { background: rgba(30, 41, 59, 0.3); padding: 20px; border-radius: 20px; border: 1px solid rgba(255,255,255,0.03); margin-bottom: 15px; }
        .node-container { border-left: 2px solid var(--border); margin-left: 15px; padding-left: 20px; margin-top: 10px; }
        .btn-tool { font-size: 0.75rem; color: #94a3b8; background: transparent; border: 1px solid var(--border); padding: 6px 14px; border-radius: 10px; cursor: pointer; transition: 0.2s; }
        .btn-tool:hover { background: var(--accent); color: white; border-color: var(--accent); }
        .preview-img { max-height: 180px; display: none; margin: 15px 0; border-radius: 12px; border: 2px solid var(--accent); }
        .table-builder { background: #020617; padding: 15px; border-radius: 15px; margin: 10px 0; border: 1px dashed var(--accent); }
        .hidden { display: none; }
    </style>
</head>
<body>

<div class="form-container">
    <div class="d-flex justify-content-between align-items-center mb-5">
        <h3 class="fw-bold m-0 text-white">Add Question</h3>
        <a href="revision.php" class="btn btn-sm btn-outline-secondary rounded-pill px-4">Cancel</a>
    </div>

    <?= $message ?>

    <form method="POST" id="mainForm" onsubmit="return syncAllData()">
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <label class="text-secondary small fw-bold">Subject</label>
                <select name="subject_name" class="form-select input-box">
                    <?php foreach($subjects_list as $sub): ?><option value="<?= trim($sub) ?>"><?= trim($sub) ?></option><?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="text-secondary small fw-bold">Type</label>
                <select name="paper_type" id="paper_type" class="form-select input-box" onchange="handleTypeChange()">
                    <option value="MCQ">MCQ</option>
                    <option value="Structured">Structured</option>
                    <option value="Essay">Essay</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="text-secondary small fw-bold">Lesson</label>
                <input type="text" name="lesson_name" class="form-control input-box" placeholder="e.g. Thermodynamics" required>
            </div>
        </div>

        <div class="part-box" style="border-top: 4px solid var(--accent);">
            <label class="text-accent small fw-bold mb-2">Main Scenario / Header</label>
            <textarea name="question" class="form-control input-box" rows="3" placeholder="Enter main question text..." required></textarea>
            
            <div class="d-flex gap-2">
                <button type="button" class="btn-tool" onclick="document.getElementById('m_file').click()"><i class="fa fa-image me-1"></i> Add Image</button>
                <button type="button" class="btn-tool" onclick="toggleT('m_tbl_w')"><i class="fa fa-table me-1"></i> Add Table</button>
            </div>

            <input type="file" id="m_file" class="hidden" accept="image/*" onchange="preview(this, 'm_p', 'm_d')">
            <input type="hidden" name="main_image_data" id="m_d">
            <img id="m_p" class="preview-img">

            <div id="m_tbl_w" class="table-builder hidden">
                <div class="d-flex gap-2 mb-2">
                    <input type="number" id="m_r" class="form-control input-box w-25" placeholder="Rows" value="2">
                    <input type="number" id="m_c" class="form-control input-box w-25" placeholder="Cols" value="2">
                    <button type="button" class="btn btn-sm btn-primary" onclick="buildMainTable()">Build</button>
                </div>
                <div class="table-grid table-responsive"></div>
                <input type="hidden" name="main_table_data" id="m_tbl_d">
            </div>
        </div>

        <div id="mcq_section" class="mt-4">
            <label class="text-secondary small fw-bold">MCQ Options</label>
            <div id="mcq_container"></div>
            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill mt-2" onclick="addMcqOp()"><i class="fa fa-plus"></i> Add Option</button>
        </div>

        <div id="theory_section" class="hidden mt-4">
            <label class="text-secondary small fw-bold">Sub Questions Hierarchy</label>
            <div id="container_0"></div> <button type="button" class="btn btn-sm btn-primary rounded-pill mt-3 px-4" onclick="addNode('0')">
                <i class="fa fa-plus-circle me-1"></i> Add Part
            </button>
            <input type="hidden" name="nested_data_json" id="nested_data_json">
        </div>

        <button type="submit" name="save_revision" class="btn btn-primary w-100 mt-5 py-3 fw-bold rounded-4 shadow-lg border-0">SAVE QUESTION</button>
    </form>
</div>

<script>
    let nIdx = 1;

    function handleTypeChange() {
        const type = document.getElementById('paper_type').value;
        document.getElementById('mcq_section').classList.toggle('hidden', type !== 'MCQ');
        document.getElementById('theory_section').classList.toggle('hidden', type === 'MCQ');
    }

    // --- Image Preview Logic ---
    function preview(input, imgId, dataId) {
        const file = input.files[0];
        if(file) {
            const reader = new FileReader();
            reader.onloadend = () => {
                const img = document.getElementById(imgId);
                img.src = reader.result;
                img.style.display = 'block';
                document.getElementById(dataId).value = reader.result;
            }
            reader.readAsDataURL(file);
        }
    }

    // --- Main Table Logic ---
    function toggleT(id) { document.getElementById(id).classList.toggle('hidden'); }
    
    function buildMainTable() {
        const r = document.getElementById('m_r').value || 2;
        const c = document.getElementById('m_c').value || 2;
        let h = '<table class="table table-bordered table-sm table-dark m-0">';
        for(let i=0; i<r; i++) {
            h += '<tr>';
            for(let j=0; j<c; j++) h += `<td><input type="text" class="input-box m-0 w-100 border-0 text-center"></td>`;
            h += '</tr>';
        }
        document.querySelector('#m_tbl_w .table-grid').innerHTML = h + '</table>';
    }

    // --- Sub-Table Logic (Dynamic) ---
    function buildSubTable(btn) {
        // Find inputs relative to the clicked button
        const wrap = btn.closest('.table-builder');
        const rows = wrap.querySelector('.rows-in').value || 2;
        const cols = wrap.querySelector('.cols-in').value || 2;
        const grid = wrap.querySelector('.table-grid');
        
        let h = '<table class="table table-bordered table-sm table-dark m-0">';
        for(let i=0; i<rows; i++) {
            h += '<tr>';
            for(let j=0; j<cols; j++) h += `<td><input type="text" class="input-box m-0 w-100 border-0 text-center sub-tbl-cell"></td>`;
            h += '</tr>';
        }
        grid.innerHTML = h + '</table>';
    }

    // --- MCQ Logic ---
    function addMcqOp() {
        const cont = document.getElementById('mcq_container');
        const i = cont.children.length;
        const div = document.createElement('div');
        div.className = "d-flex align-items-center gap-2 mb-2";
        div.innerHTML = `<input type="radio" name="correct_idx" value="${i}" ${i===0?'checked':''}><input type="text" name="mcq_options[]" class="form-control input-box m-0" placeholder="Option ${i+1}"><i class="fa fa-times text-danger cursor-pointer" onclick="this.parentElement.remove()"></i>`;
        cont.appendChild(div);
    }

    // --- Infinite Nesting Logic (FIXED) ---
    function addNode(parentId) {
        const cont = document.getElementById(`container_${parentId}`);
        const id = nIdx++;
        const div = document.createElement('div');
        div.className = "node-container";
        div.id = `node_${id}`; // Unique ID for this node
        
        // Note the specific Classes added (sub-q-text, sub-img-data, sub-tbl-grid) used by Sync function
        div.innerHTML = `
            <div class="part-box">
                <div class="d-flex justify-content-between mb-2">
                    <span class="badge bg-secondary">Part</span>
                    <i class="fa fa-trash text-danger cursor-pointer" onclick="document.getElementById('node_${id}').remove()"></i>
                </div>
                <textarea class="form-control input-box sub-q-text" rows="2" placeholder="Sub-question text..."></textarea>
                
                <div class="d-flex gap-2">
                    <button type="button" class="btn-tool" onclick="document.getElementById('f_${id}').click()"><i class="fa fa-image"></i></button>
                    <button type="button" class="btn-tool" onclick="document.getElementById('tw_${id}').classList.toggle('hidden')"><i class="fa fa-table"></i></button>
                </div>

                <input type="file" id="f_${id}" class="hidden" accept="image/*" onchange="preview(this, 'p_${id}', 'd_${id}')">
                <input type="hidden" class="sub-img-data" id="d_${id}">
                <img id="p_${id}" class="preview-img">

                <div id="tw_${id}" class="table-builder hidden">
                    <div class="d-flex gap-1 mb-2">
                        <input type="number" class="form-control input-box p-1 w-25 rows-in" placeholder="R" value="2">
                        <input type="number" class="form-control input-box p-1 w-25 cols-in" placeholder="C" value="2">
                        <button type="button" class="btn btn-sm btn-primary px-2" onclick="buildSubTable(this)">Build</button>
                    </div>
                    <div class="table-grid sub-tbl-grid"></div>
                </div>

                <textarea class="form-control input-box sub-a-text mt-2" rows="1" placeholder="Model Answer..."></textarea>
                
                <div id="container_${id}"></div>
                <button type="button" class="btn-tool mt-2 text-primary border-primary" onclick="addNode('${id}')">+ Sub-part</button>
            </div>
        `;
        cont.appendChild(div);
    }

    // --- FINAL SYNC LOGIC (The Crucial Fix) ---
    function syncAllData() {
        // 1. Sync Main Table
        const mainTblData = [];
        document.querySelectorAll('#m_tbl_w .table-grid tr').forEach(r => {
            let row = [];
            r.querySelectorAll('input').forEach(c => row.push(c.value));
            if(row.length) mainTblData.push(row);
        });
        document.getElementById('m_tbl_d').value = JSON.stringify(mainTblData);

        // 2. Sync Nested Data (Recursive Scraper)
        const type = document.getElementById('paper_type').value;
        if(type !== 'MCQ') {
            const getNodes = (cId) => {
                const nodes = document.querySelectorAll(`#container_${cId} > .node-container`);
                let data = [];
                
                nodes.forEach(node => {
                    const myId = node.id.split('_')[1]; // Get unique numeric ID
                    
                    // Scrape Table Data specific to this node
                    let tData = [];
                    // We look inside THIS node specifically
                    const tRows = node.querySelectorAll(`.sub-tbl-grid tr`); 
                    tRows.forEach(r => {
                        let row = [];
                        r.querySelectorAll('input').forEach(c => row.push(c.value));
                        tData.push(row);
                    });

                    // Push object
                    data.push({
                        q: node.querySelector('.sub-q-text').value,
                        a: node.querySelector('.sub-a-text').value,
                        img: node.querySelector('.sub-img-data').value, // This is the fix for images
                        tbl: tData, // This is the fix for tables
                        subs: getNodes(myId) // Recurse
                    });
                });
                return data;
            };
            document.getElementById('nested_data_json').value = JSON.stringify(getNodes('0'));
        }
        return true;
    }

    // Initialize
    handleTypeChange();
    for(let i=0; i<4; i++) addMcqOp();
</script>

</body>
</html>