<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) { 
    header("Location: login.php"); 
    exit(); 
}
$user_id = $_SESSION['user_id'];

// --- 1. Get Subjects from Users Table ---
$user_query = mysqli_query($conn, "SELECT subjects FROM users WHERE id = '$user_id'");
$user_data = mysqli_fetch_assoc($user_query);
$my_subjects = !empty($user_data['subjects']) ? explode(',', $user_data['subjects']) : [];

// --- AJAX Actions ---
if (isset($_POST['mark_revised'])) {
    $note_id = mysqli_real_escape_string($conn, $_POST['note_id']);
    mysqli_query($conn, "UPDATE question_notes SET revision_count = revision_count + 1 WHERE id = '$note_id' AND user_id = '$user_id'");
    exit();
}
if (isset($_POST['toggle_favorite'])) {
    $note_id = mysqli_real_escape_string($conn, $_POST['note_id']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    mysqli_query($conn, "UPDATE question_notes SET is_favorite = '$status' WHERE id = '$note_id' AND user_id = '$user_id'");
    exit();
}
if (isset($_POST['update_note'])) {
    $note_id = mysqli_real_escape_string($conn, $_POST['note_id']);
    $new_content = mysqli_real_escape_string($conn, $_POST['content']);
    mysqli_query($conn, "UPDATE question_notes SET note = '$new_content' WHERE id = '$note_id' AND user_id = '$user_id'");
    exit();
}
if (isset($_POST['delete_note'])) {
    $note_id = mysqli_real_escape_string($conn, $_POST['note_id']);
    mysqli_query($conn, "DELETE FROM question_notes WHERE id = '$note_id' AND user_id = '$user_id'");
    exit();
}

// --- NEW ACTION: Quick Add Note WITH FILE UPLOAD (STRICT FIX FOR DUPLICATE ERROR) ---
if (isset($_POST['quick_add_note'])) {
    $sub = mysqli_real_escape_string($conn, $_POST['subject']);
    $yr  = mysqli_real_escape_string($conn, $_POST['year']);
    $tp  = mysqli_real_escape_string($conn, $_POST['type']);
    $nt  = mysqli_real_escape_string($conn, $_POST['note']);
    $u_id = (int)$user_id;
    $image_path = "";

    // Duplicate entry වළක්වන්න ඊළඟට හිස්ව තියෙන අංකය හොයාගැනීම
    $res = mysqli_query($conn, "SELECT MAX(question_no) as max_qn FROM question_notes WHERE user_id = '$u_id' AND subject = '$sub'");
    $row = mysqli_fetch_assoc($res);
    $qn = ($row['max_qn']) ? $row['max_qn'] + 1 : 1;

    if (isset($_FILES['note_image']) && $_FILES['note_image']['error'] == 0) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
        
        $file_ext = pathinfo($_FILES["note_image"]["name"], PATHINFO_EXTENSION);
        $file_name = time() . "_" . uniqid() . "." . $file_ext;
        $target_file = $target_dir . $file_name;

        if (move_uploaded_file($_FILES["note_image"]["tmp_name"], $target_file)) {
            $image_path = $target_file;
        }
    }

    $sql = "INSERT INTO `question_notes` (`user_id`, `subject`, `paper_year`, `paper_type`, `question_no`, `note`, `image_path`) 
            VALUES ('$u_id', '$sub', '$yr', '$tp', $qn, '$nt', '$image_path')";
    
    if(mysqli_query($conn, $sql)) echo "success";
    else echo mysqli_error($conn);
    exit();
}

function getRandomStickyColor() {
    $colors = ['#ff7eb9', '#ffffa5', '#7afcff', '#98ff98', '#ffcc99', '#d1d1ff'];
    return $colors[array_rand($colors)];
}

function formatNote($text) {
    $text = htmlspecialchars($text);
    $text = preg_replace('/^#\s+(.*)$/m', '<div class="notebook-heading">$1</div>', $text);
    $text = preg_replace('/^-\s+(.*)$/m', '<li class="sub-item">$1</li>', $text);
    $text = preg_replace('/^\*\s+(.*)$/m', '<li class="main-item">$1</li>', $text);
    if (strpos($text, '<li') !== false) {
        $text = preg_replace('/((?:<li.*?>.*?<\/li>\s*)+)/s', '<ul class="notebook-list">$1</ul>', $text);
    }
    return nl2br($text);
}

$notes_query = mysqli_query($conn, "SELECT * FROM question_notes WHERE user_id = '$user_id' AND note != '' ORDER BY is_favorite DESC, subject ASC, paper_year DESC");
$all_notes = [];
while($row = mysqli_fetch_assoc($notes_query)) { $all_notes[$row['subject']][] = $row; }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nexus Study Board Pro</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=Permanent+Marker&family=Indie+Flower&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <style>
        body { background-color: #1a1a2e; color: #f8fafc; font-family: 'Plus Jakarta Sans', sans-serif; padding: 20px; }
        .floating-add-btn {
            position: fixed; bottom: 40px; right: 40px; width: 70px; height: 70px;
            background: linear-gradient(135deg, #6366f1, #a855f7); color: white;
            border-radius: 50%; display: flex; align-items: center; justify-content: center;
            font-size: 30px; box-shadow: 0 10px 20px rgba(0,0,0,0.4); cursor: pointer;
            z-index: 9999; transition: 0.3s; border: none;
        }
        
        .format-btn { background: #4f46e5; color: white; border: none; padding: 5px 12px; border-radius: 5px; font-size: 0.75rem; margin-right: 5px; margin-bottom: 10px; }
        .sticky-note {
            background: #ffef3e; color: #2c3e50; width: 100%; height: 260px;
            padding: 25px 20px; position: relative;
            box-shadow: 0 10px 20px rgba(0,0,0,0.2), inset 0 -40px 40px rgba(0,0,0,0.1);
            transition: 0.4s; margin-bottom: 30px; cursor: pointer; overflow: hidden;
            font-family: 'Indie Flower', cursive; border-bottom-right-radius: 60px 10px;
        }
        .notebook-container {
            background: #fff; background-image: linear-gradient(#e1e9ff 1px, transparent 1px);
            background-size: 100% 28px; color: #2c3e50; padding: 60px 50px 50px 80px;
            min-height: 800px; position: relative; text-align: left;
        }
        .notebook-container::before { content: ''; position: absolute; top: 0; left: 65px; width: 2px; height: 100%; background: #ffadad; }
        .notebook-heading { font-weight: 800; font-size: 1.3rem; color: #ef4646; text-decoration: underline; margin-top: 10px; }
        .notebook-list { list-style-type: none; padding-left: 0; }
        .main-item::before { content: '➢'; color: #6366f1; margin-right: 10px; }
        .sub-item { padding-left: 30px; font-size: 0.95em; color: #4b5563; }
        .sub-item::before { content: '○'; color: #9ca3af; margin-right: 8px; }
        .star-btn { position: absolute; top: 15px; right: 15px; font-size: 1.4rem; color: rgba(0,0,0,0.15); z-index: 20; transition: 0.3s; }
        .star-btn.active { color: #e67e22; transform: scale(1.2); }
        .hidden-note { display: none !important; }
        .action-bar { background: #f8f9fa; padding: 12px; border-radius: 10px 10px 0 0; display: flex; gap: 8px; justify-content: flex-end; }
        .swal2-input, .swal2-select, .swal2-textarea, .swal2-file { background: #2c2c44 !important; color: white !important; border: 1px solid #444 !important; }
    </style>
</head>
<body>

<button class="floating-add-btn" onclick="openQuickAdd()">
    <i class="fa fa-plus"></i>
</button>

<div class="container-fluid" style="max-width: 1400px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="fw-800 m-0 text-white">NEXUS STUDY BOARD</h1>
        <div class="d-flex gap-2">
            <a href="dashboard.php" class="btn btn-primary rounded-pill px-4 fw-bold shadow">Back to Dashboard</a>
            <a href="book.php" class="btn btn-warning rounded-pill px-4 fw-bold shadow"><i class="fa fa-book-open me-2"></i> 3D Book View</a>
        </div>
    </div>

    <div class="filter-panel bg-dark p-4 rounded-4 mb-5 border border-secondary shadow">
        <div class="row g-3">
            <div class="col-md-4"><input type="text" id="searchNote" class="form-control bg-black text-white border-secondary" placeholder="Search..." onkeyup="filterNotes()"></div>
            <div class="col-md-3">
                <select id="filterSubject" class="form-select bg-black text-white border-secondary" onchange="filterNotes()">
                    <option value="all">All Subjects</option>
                    <?php foreach(array_keys($all_notes) as $sub): ?><option value="<?= $sub ?>"><?= $sub ?></option><?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <select id="filterType" class="form-select bg-black text-white border-secondary" onchange="filterNotes()">
                    <option value="all">All Types</option>
                    <option value="MCQ">MCQ</option><option value="Essay">Essay</option><option value="Structured">Structured</option><option value="GENERAL">General</option><option value="Papers">Monthly Papers</option>
                </select>
            </div>
        </div>
    </div>

    <div id="notesContainer">
        <?php foreach($all_notes as $subject => $notes): ?>
            <div class="subject-section mb-5" data-subject="<?= $subject ?>">
                <h3 class="mb-4 text-uppercase fw-800" style="color: #a5b4fc; border-left: 5px solid #6366f1; padding-left: 15px;"><?= $subject ?></h3>
                <div class="row g-4">
                    <?php foreach($notes as $n): 
                        $stickyBg = getRandomStickyColor();
                        $formattedBody = formatNote($n['note']);
                        $json_safe = htmlspecialchars(json_encode([
                            'id' => $n['id'], 'sub' => $subject, 'year' => $n['paper_year'],
                            'type' => $n['paper_type'], 'raw' => $n['note'], 'formatted' => $formattedBody, 
                            'img' => $n['image_path']
                        ]), ENT_QUOTES, 'UTF-8');
                    ?>
                        <div class="col-12 col-sm-6 col-md-4 col-lg-3 note-item" data-type="<?= $n['paper_type'] ?>" data-content="<?= strtolower(htmlspecialchars($n['note'])) ?>">
                            <div class="sticky-note" style="background-color: <?= $stickyBg ?>;" onclick="viewRuledNote(<?= $json_safe ?>)">
                                <i class="fa-star <?= $n['is_favorite'] ? 'fas active' : 'far' ?> star-btn" onclick="toggleStar(event, <?= $n['id'] ?>, <?= $n['is_favorite'] ?>)"></i>
                                <div class="fw-bold mb-2 small text-decoration-underline"><?= $n['paper_year'] ?> - <?= $n['paper_type'] ?></div>
                                <div class="sticky-content"><?= mb_strimwidth(strip_tags($formattedBody), 0, 160, "...") ?></div>
                                <?php if($n['image_path']): ?><i class="fa fa-paperclip text-muted position-absolute" style="bottom:10px; right:15px;"></i><?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
function insertFormat(tag) {
    const textarea = document.getElementById('q_note');
    if(!textarea) return;
    const start = textarea.selectionStart;
    textarea.value = textarea.value.substring(0, start) + "\n" + tag + " " + textarea.value.substring(start);
    textarea.focus();
}

function openQuickAdd() {
    const subOptions = <?= json_encode($my_subjects) ?>.map(s => `<option value="${s.trim()}">${s.trim()}</option>`).join('');

    Swal.fire({
        title: '<span style="color:white">Quick Add Note</span>',
        background: '#1a1a2e',
        html: `
            <div class="text-start">
                <select id="q_sub" class="swal2-select m-0 w-100 mb-3">${subOptions}</select>
                <div class="row">
                    <div class="col-6"><input id="q_year" type="number" class="swal2-input m-0 w-100 mb-3" value="2026"></div>
                    <div class="col-6">
                        <select id="q_type" class="swal2-select m-0 w-100 mb-3">
                            <option value="GENERAL">General</option><option value="MCQ">MCQ</option><option value="Essay">Essay</option><option value="Structured">Structured</option><option value="Papers">Monthly Papers</option>
                        </select>
                    </div>
                </div>
                <label class="text-white small">Upload Image (Optional)</label>
                <input id="q_file" type="file" class="swal2-file m-0 w-100 mb-3" accept="image/*">
                
                <div class="mb-2">
                    <button type="button" class="format-btn" onclick="insertFormat('#')">Headline (#)</button>
                    <button type="button" class="format-btn" onclick="insertFormat('*')">Main Item (*)</button>
                    <button type="button" class="format-btn" onclick="insertFormat('-')">Sub Item (-)</button>
                </div>
                <textarea id="q_note" class="swal2-textarea m-0 w-100" style="height:150px" placeholder="Write details..."></textarea>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Save Note',
        preConfirm: () => {
            const formData = new FormData();
            formData.append('quick_add_note', 'true');
            formData.append('subject', document.getElementById('q_sub').value);
            formData.append('year', document.getElementById('q_year').value);
            formData.append('type', document.getElementById('q_type').value);
            formData.append('note', document.getElementById('q_note').value);
            
            const fileInput = document.getElementById('q_file');
            if (fileInput.files[0]) {
                formData.append('note_image', fileInput.files[0]);
            }
            return formData;
        }
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '', type: 'POST', data: result.value,
                processData: false, contentType: false,
                success: (res) => {
                    if(res.trim() === "success") location.reload();
                    else Swal.fire('Error', res, 'error');
                }
            });
        }
    });
}

function viewRuledNote(d) {
    const rawTxt = d.raw.replace(/\\/g, '\\\\').replace(/`/g, '\\`').replace(/\$/g, '\\$');
    Swal.fire({
        width: '850px', background: '#f8f9fa', showConfirmButton: false, showCloseButton: true, padding: '0',
        html: `
            <div class="action-bar">
                <button class="btn btn-sm btn-outline-primary" onclick="editNote(${d.id}, \`${rawTxt}\`)">Edit</button>
                <button class="btn btn-sm btn-success" onclick="downloadPNG()">Save Image</button>
                <button class="btn btn-sm btn-danger" onclick="delNote(${d.id})">Delete</button>
            </div>
            <div id="captureArea">
                <div class="notebook-container">
                    <h1 class="notebook-title">${d.sub}</h1>
                    <p class="text-muted fw-bold">${d.year} | ${d.type}</p>
                    <div class="mt-4">${d.formatted}</div>
                    ${d.img ? `<div class="mt-4 text-center"><img src="${d.img}" class="shadow-sm border rounded" style="max-width:100%; border: 5px solid white;"></div>` : ''}
                </div>
            </div>
        `
    });
}

function toggleStar(e, id, s) { e.stopPropagation(); $.post('', { toggle_favorite: true, note_id: id, status: s?0:1 }, () => location.reload()); }
function editNote(id, txt) { 
    Swal.fire({ title: 'Edit', input: 'textarea', inputValue: txt, background: '#1a1a2e', color: '#fff' })
    .then(r => { if(r.isConfirmed) $.post('', { update_note: true, note_id: id, content: r.value }, () => location.reload()); });
}
function delNote(id) { 
    if(confirm('මකන්නද?')) $.post('', { delete_note: true, note_id: id }, () => location.reload()); 
}
function downloadPNG() {
    html2canvas(document.getElementById('captureArea'), { scale: 2 }).then(canvas => {
        const a = document.createElement('a'); a.download = 'Note_Export.png'; a.href = canvas.toDataURL(); a.click();
    });
}
function filterNotes() {
    const s = document.getElementById('searchNote').value.toLowerCase();
    const sub = document.getElementById('filterSubject').value;
    const t = document.getElementById('filterType').value;
    document.querySelectorAll('.subject-section').forEach(sec => {
        let has = false;
        sec.querySelectorAll('.note-item').forEach(item => {
            const match = (sub === 'all' || sec.dataset.subject === sub) && (t === 'all' || item.dataset.type === t) && (s === '' || item.dataset.content.includes(s));
            item.classList.toggle('hidden-note', !match);
            if(match) has = true;
        });
        sec.classList.toggle('hidden-note', !has);
    });
}
</script>
</body>
</html>