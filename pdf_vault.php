<?php
session_start();
include 'db.php';
date_default_timezone_set('Asia/Colombo');

if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }
$user_id = $_SESSION['user_id'];

// --- File Upload Logic ---
if (isset($_POST['upload_pdf'])) {
    $subject = mysqli_real_escape_string($conn, $_POST['subject']);
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $target_dir = "uploads/pdfs/";
    if (!file_exists($target_dir)) { mkdir($target_dir, 0777, true); }
    $file_name = time() . '_' . basename($_FILES["pdf_file"]["name"]);
    $target_file = $target_dir . $file_name;
    if (strtolower(pathinfo($target_file, PATHINFO_EXTENSION)) == "pdf") {
        if (move_uploaded_file($_FILES["pdf_file"]["tmp_name"], $target_file)) {
            mysqli_query($conn, "INSERT INTO user_pdfs (user_id, subject_name, pdf_title, file_path) VALUES ('$user_id', '$subject', '$title', '$target_file')");
            header("Location: pdf_vault.php?success=1"); exit();
        }
    }
}

// --- Delete Logic ---
if (isset($_GET['delete_id'])) {
    $delete_id = mysqli_real_escape_string($conn, $_GET['delete_id']);
    $res = mysqli_query($conn, "SELECT file_path FROM user_pdfs WHERE id = '$delete_id' AND user_id = '$user_id'");
    if ($row = mysqli_fetch_assoc($res)) {
        if (file_exists($row['file_path'])) { unlink($row['file_path']); }
        mysqli_query($conn, "DELETE FROM user_pdfs WHERE id = '$delete_id'");
        header("Location: pdf_vault.php?deleted=1"); exit();
    }
}

$user_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT subjects FROM users WHERE id = '$user_id'"));
$subjects = !empty($user_data['subjects']) ? explode(',', $user_data['subjects']) : [];

function getSubjectColor($subject) {
    $colors = [
        'Physics'   => '#ef4444',
        'Combined'  => '#3b82f6',
        'Chemistry' => '#10b981',
        'ICT'       => '#f59e0b',
        'Biology'   => '#ec4899',
    ];
    return $colors[trim($subject)] ?? '#6366f1';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Realistic Library | Nexus Pro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap');
        
        body { background: #020617; color: #f8fafc; font-family: 'Plus Jakarta Sans', sans-serif; }

        /* Floating Add Button */
        .fab-add {
            position: fixed; bottom: 30px; right: 30px;
            width: 60px; height: 60px; background: #6366f1;
            border-radius: 50%; display: flex; align-items: center; justify-content: center;
            font-size: 24px; color: white; box-shadow: 0 10px 20px rgba(99, 102, 241, 0.4);
            cursor: pointer; z-index: 100; transition: 0.3s; border: none;
        }
        .fab-add:hover { transform: scale(1.1) rotate(90deg); background: #4f46e5; }

        /* Search & Filter Bar */
        .search-container {
            background: rgba(30, 41, 59, 0.5);
            backdrop-filter: blur(10px);
            border-radius: 20px; padding: 15px 25px;
            border: 1px solid rgba(255,255,255,0.1);
        }

        /* 3D Book Styles (As before with some polish) */
        .book-wrapper { perspective: 1200px; margin: 40px 0; display: flex; flex-direction: column; align-items: center; }
        .book { width: 160px; height: 230px; position: relative; transform-style: preserve-3d; transition: transform 0.6s ease; cursor: pointer; }
        .book:hover { transform: rotateY(-30deg) translateX(10px); }
        .book-cover {
            position: absolute; width: 100%; height: 100%; border-radius: 3px 12px 12px 3px;
            display: flex; flex-direction: column; justify-content: center; align-items: center;
            padding: 15px; text-align: center; z-index: 5; transform-origin: left;
            box-shadow: 15px 15px 25px rgba(0,0,0,0.5); border: 1px solid rgba(255,255,255,0.1);
        }
        .book-side { position: absolute; width: 40px; height: 98%; top: 1%; left: -20px; transform: rotateY(-90deg); }
        .book-title { font-size: 0.9rem; font-weight: 800; color: #fff; text-transform: uppercase; }
        .book-badge { font-size: 0.65rem; background: rgba(0,0,0,0.3); padding: 3px 10px; border-radius: 20px; position: absolute; bottom: 20px; }

        /* Modal Customization */
        .modal-content { background: #0f172a; border: 1px solid rgba(255,255,255,0.1); border-radius: 25px; color: white; }
        .form-control, .form-select { background: #1e293b !important; border: 1px solid #334155 !important; color: white !important; border-radius: 12px; }
        
        .book-actions { margin-top: 15px; opacity: 0; transition: 0.3s; }
        .book-wrapper:hover .book-actions { opacity: 1; }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-5">
        <h1 class="fw-800 m-0">Nexus Library</h1>
        <a href="dashboard.php" class="btn btn-outline-secondary rounded-pill">Back</a>
    </div>

    <div class="search-container mb-5 shadow-sm">
        <div class="row g-3">
            <div class="col-md-8">
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-0 text-muted"><i class="fa fa-search"></i></span>
                    <input type="text" id="searchInput" class="form-control bg-transparent border-0 text-white" placeholder="Search by book title..." onkeyup="filterBooks()">
                </div>
            </div>
            <div class="col-md-4">
                <select id="subjectFilter" class="form-select bg-transparent border-0 text-white" onchange="filterBooks()">
                    <option value="all">All Subjects</option>
                    <?php foreach($subjects as $sub): ?>
                        <option value="<?= trim($sub) ?>"><?= trim($sub) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </div>

    <div class="row g-4" id="booksGrid">
        <?php
        $res = mysqli_query($conn, "SELECT * FROM user_pdfs WHERE user_id = '$user_id' ORDER BY uploaded_at DESC");
        while($pdf = mysqli_fetch_assoc($res)):
            $bookColor = getSubjectColor($pdf['subject_name']);
        ?>
        <div class="col-6 col-md-4 col-lg-3 book-item" data-title="<?= strtolower($pdf['pdf_title']) ?>" data-subject="<?= trim($pdf['subject_name']) ?>">
            <div class="book-wrapper">
                <div class="book" onclick="window.open('<?= $pdf['file_path'] ?>', '_blank')">
                    <div class="book-side" style="background: <?= $bookColor ?>; filter: brightness(0.6);"></div>
                    <div class="book-cover" style="background: linear-gradient(135deg, <?= $bookColor ?> 0%, #1e293b 100%);">
                        <div class="book-title"><?= htmlspecialchars($pdf['pdf_title']) ?></div>
                        <div class="book-badge"><?= $pdf['subject_name'] ?></div>
                    </div>
                </div>
                <div class="book-actions">
                    <a href="javascript:void(0)" onclick="confirmDelete(<?= $pdf['id'] ?>)" class="text-danger text-decoration-none small fw-bold"><i class="fa fa-trash me-1"></i> Delete</a>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</div>

<button class="fab-add" data-bs-toggle="modal" data-bs-target="#uploadModal">
    <i class="fa fa-plus"></i>
</button>

<div class="modal fade" id="uploadModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg">
            <div class="modal-header border-0 p-4">
                <h5 class="modal-title fw-bold">Add New Book</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 pt-0">
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="small text-muted mb-2">Book Title</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="small text-muted mb-2">Subject</label>
                        <select name="subject" class="form-select">
                            <?php foreach($subjects as $sub): ?>
                                <option value="<?= trim($sub) ?>"><?= trim($sub) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="small text-muted mb-2">Upload PDF</label>
                        <input type="file" name="pdf_file" class="form-control" accept=".pdf" required>
                    </div>
                    <button type="submit" name="upload_pdf" class="btn btn-primary w-100 py-3 fw-bold rounded-3">Upload to Vault</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Search & Filter Logic
function filterBooks() {
    let search = document.getElementById('searchInput').value.toLowerCase();
    let subject = document.getElementById('subjectFilter').value;
    let books = document.querySelectorAll('.book-item');

    books.forEach(book => {
        let title = book.getAttribute('data-title');
        let bookSub = book.getAttribute('data-subject');
        
        let matchesSearch = title.includes(search);
        let matchesSubject = (subject === 'all' || bookSub === subject);

        if (matchesSearch && matchesSubject) {
            book.style.display = "block";
        } else {
            book.style.display = "none";
        }
    });
}

function confirmDelete(id) {
    Swal.fire({
        title: 'Delete Book?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#1e293b',
        confirmButtonText: 'Yes, Delete',
        background: '#0f172a',
        color: '#fff'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'pdf_vault.php?delete_id=' + id;
        }
    })
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>