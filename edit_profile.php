<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) { 
    header("Location: login.php"); 
    exit(); 
}
$user_id = $_SESSION['user_id'];

// පරිශීලක දත්ත ලබා ගැනීම
$query = mysqli_query($conn, "SELECT * FROM users WHERE id = '$user_id'");
$user = mysqli_fetch_assoc($query);

// Subjects string එකක් ලෙස තිබේ නම් එය array එකක් කරමු
$current_subjects = !empty($user['subjects']) ? explode(", ", $user['subjects']) : [];

$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $exam_year = mysqli_real_escape_string($conn, $_POST['exam_year']);
    $exam_date = mysqli_real_escape_string($conn, $_POST['exam_date']);
    
    // subjects array එක string එකක් බවට පත් කිරීම
    if (isset($_POST['subjects']) && is_array($_POST['subjects'])) {
        $subjects_cleaned = array_map(function($s) use ($conn) {
            return mysqli_real_escape_string($conn, trim($s));
        }, $_POST['subjects']);
        $subjects_str = implode(", ", $subjects_cleaned);
    } else {
        $subjects_str = "";
    }

    // UPDATE query එක සකස් කිරීම
    $update_sql = "UPDATE users SET 
                    name='$name', 
                    exam_year='$exam_year', 
                    exam_date='$exam_date', 
                    subjects='$subjects_str' 
                    WHERE id='$user_id'";

    // මෙතන තමයි වැරැද්ද තිබුණේ - $conn parameter එක ඇතුළත් කළා
    if (mysqli_query($conn, $update_sql)) {
        $message = "<div class='alert alert-success border-0 shadow-sm' style='background: rgba(16, 185, 129, 0.2); color: #10b981;'><i class='fa fa-check-circle me-2'></i>Profile updated successfully!</div>";
        header("Refresh:1; url=dashboard.php");
    } else {
        $message = "<div class='alert alert-danger'>Error: " . mysqli_error($conn) . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Profile | Nexus Pro</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary: #6366f1;
            --dark-bg: #0f172a;
            --card-bg: rgba(255, 255, 255, 0.03);
            --input-bg: rgba(255, 255, 255, 0.05);
        }

        body { 
            background-color: var(--dark-bg); 
            background-image: radial-gradient(circle at top right, rgba(99, 102, 241, 0.1), transparent 500px);
            font-family: 'Plus Jakarta Sans', sans-serif; 
            color: #f8fafc;
            min-height: 100vh;
        }

        .edit-card { 
            max-width: 650px; 
            margin: 40px auto; 
            background: var(--card-bg); 
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 32px; 
            padding: 40px; 
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }

        .form-control {
            background: var(--input-bg);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 14px;
            color: white;
            padding: 12px 16px;
            transition: 0.3s;
        }

        .form-control:focus {
            background: rgba(255, 255, 255, 0.08);
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.2);
            color: white;
        }

        .subject-input-group { 
            background: rgba(255, 255, 255, 0.03); 
            padding: 8px 12px; 
            border-radius: 16px; 
            margin-bottom: 12px; 
            display: flex; 
            align-items: center; 
            gap: 10px;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .btn-add { 
            background: var(--primary); 
            color: white; 
            border: none; 
            border-radius: 14px; 
            padding: 0 25px; 
            font-weight: 700; 
            transition: 0.3s; 
        }

        .btn-remove { 
            background: rgba(239, 68, 68, 0.15); 
            color: #ef4444; 
            border: none; 
            border-radius: 10px; 
            width: 38px; 
            height: 38px; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
        }

        .btn-save { 
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); 
            color: white; 
            width: 100%; 
            padding: 16px; 
            border-radius: 16px; 
            border: none; 
            font-weight: 800; 
            margin-top: 30px; 
        }

        .back-btn {
            width: 45px;
            height: 45px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            background: var(--input-bg);
            color: white;
            text-decoration: none;
        }
    </style>
</head>
<body>

<div class="container py-4">
    <div class="edit-card">
        <div class="d-flex align-items-center mb-5">
            <a href="dashboard.php" class="back-btn me-3"><i class="fa fa-chevron-left"></i></a>
            <div>
                <h3 class="fw-800 mb-0">Edit Profile</h3>
                <p class="text-muted mb-0 small">Update your goals and subject list</p>
            </div>
        </div>

        <?= $message ?>

        <form method="POST">
            <div class="row">
                <div class="col-md-12 mb-4">
                    <label class="mb-2">Full Name</label>
                    <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($user['name']) ?>" required>
                </div>
                <div class="col-6 mb-4">
                    <label class="mb-2">Exam Year</label>
                    <input type="text" name="exam_year" class="form-control" value="<?= htmlspecialchars($user['exam_year']) ?>">
                </div>
                <div class="col-6 mb-4">
                    <label class="mb-2">Exam Date</label>
                    <input type="date" name="exam_date" class="form-control" value="<?= $user['exam_date'] ?>">
                </div>
            </div>

            <hr>
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-800 mb-0"><i class="fa fa-book-open text-primary me-2"></i>My Subjects</h5>
            </div>
            
            <div id="subject-container">
                <?php foreach($current_subjects as $sub): if(empty(trim($sub))) continue; ?>
                <div class="subject-input-group">
                    <input type="text" name="subjects[]" class="form-control border-0 bg-transparent" value="<?= htmlspecialchars($sub) ?>" readonly>
                    <button type="button" class="btn-remove" onclick="removeSubject(this)"><i class="fa fa-trash-can"></i></button>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="mt-4">
                <div class="input-group" style="background: var(--input-bg); padding: 5px; border-radius: 18px; border: 1px solid rgba(255,255,255,0.08);">
                    <input type="text" id="new-subject-name" class="form-control border-0 bg-transparent" placeholder="Add new subject...">
                    <button type="button" class="btn-add" onclick="addSubject()">ADD</button>
                </div>
            </div>

            <button type="submit" class="btn-save shadow-lg">Save All Changes</button>
        </form>
    </div>
</div>

<script>
    function addSubject() {
        const input = document.getElementById('new-subject-name');
        const container = document.getElementById('subject-container');
        const subName = input.value.trim();

        if (subName === "") return;

        const div = document.createElement('div');
        div.className = 'subject-input-group';
        div.innerHTML = `
            <input type="text" name="subjects[]" class="form-control border-0 bg-transparent" value="${subName}" readonly>
            <button type="button" class="btn-remove" onclick="removeSubject(this)"><i class="fa fa-trash-can"></i></button>
        `;
        
        container.appendChild(div);
        input.value = "";
        input.focus();
    }

    function removeSubject(btn) {
        if(confirm("Are you sure you want to drop this subject?")) {
            btn.parentElement.remove();
        }
    }
</script>

</body>
</html>