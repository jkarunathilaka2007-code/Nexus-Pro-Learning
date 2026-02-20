<?php
include 'db.php'; 

if (isset($_POST['register'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $exam_year = mysqli_real_escape_string($conn, $_POST['exam_year']);
    $exam_date = mysqli_real_escape_string($conn, $_POST['exam_date']);

    // Subjects array එක string එකක් බවට පත් කිරීම
    $subjects_array = $_POST['subjects']; 
    $subjects_string = implode(", ", array_filter($subjects_array));

    // Profile Image Upload
    $target_dir = "uploads/";
    if (!file_exists($target_dir)) { mkdir($target_dir, 0777, true); }
    
    $file_name = time() . "_" . basename($_FILES["profile_image"]["name"]);
    $target_file = $target_dir . $file_name;

    if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file)) {
        $sql = "INSERT INTO users (name, email, password, exam_year, subjects, exam_date, profile_image) 
                VALUES ('$name', '$email', '$password', '$exam_year', '$subjects_string', '$exam_date', '$target_file')";
        
        if (mysqli_query($conn, $sql)) {
            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
            echo "<script>
                setTimeout(function() {
                    Swal.fire('Success!', 'Registration Successful!', 'success').then(() => {
                        window.location='login.php';
                    });
                }, 100);
            </script>";
        } else {
            $error = "Error: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account | Nexus Exam Pro</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary: #6366f1;
            --accent: #06b6d4;
            --dark: #0f172a;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--dark);
            background-image: radial-gradient(circle at 10% 20%, rgba(99, 102, 241, 0.1) 0%, transparent 40%),
                              radial-gradient(circle at 90% 80%, rgba(6, 182, 212, 0.05) 0%, transparent 40%);
            color: white;
            min-height: 100vh;
            padding: 40px 20px;
        }

        .reg-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 28px;
            padding: 40px;
            width: 100%;
            max-width: 650px;
            margin: auto;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }

        .brand-logo {
            font-size: 1.5rem;
            font-weight: 800;
            text-align: center;
            margin-bottom: 20px;
        }

        .form-label {
            font-weight: 600;
            font-size: 0.85rem;
            color: rgba(255, 255, 255, 0.6);
            margin-bottom: 8px;
        }

        .form-control {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
            padding: 12px 15px;
            border-radius: 12px;
            transition: 0.3s;
        }

        .form-control:focus {
            background: rgba(255, 255, 255, 0.08);
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.2);
            color: white;
        }

        .subject-input-group {
            background: rgba(255, 255, 255, 0.02);
            padding: 15px;
            border-radius: 16px;
            border: 1px dashed rgba(255, 255, 255, 0.1);
        }

        .btn-add-sub {
            background: rgba(16, 185, 129, 0.1);
            color: #10b981;
            border: 1px solid rgba(16, 185, 129, 0.2);
            font-size: 0.8rem;
            font-weight: 700;
        }

        .btn-add-sub:hover {
            background: #10b981;
            color: white;
        }

        .btn-reg {
            background: linear-gradient(135deg, var(--primary), var(--accent));
            border: none;
            padding: 15px;
            border-radius: 12px;
            font-weight: 700;
            transition: 0.3s;
        }

        .btn-reg:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(99, 102, 241, 0.4);
        }

        input::placeholder { color: rgba(255, 255, 255, 0.3) !important; }
        
        /* Chrome, Safari, Edge, Opera - hide number arrows */
        input::-webkit-outer-spin-button, input::-webkit-inner-spin-button {
            -webkit-appearance: none; margin: 0;
        }
    </style>

    <script>
        function addSubjectField() {
            const container = document.getElementById('subject-container');
            const div = document.createElement('div');
            div.className = 'input-group mb-2 animate__animated animate__fadeIn';
            div.innerHTML = `
                <input type="text" name="subjects[]" class="form-control" placeholder="Subject Name (e.g. Physics)" required>
                <button type="button" class="btn btn-outline-danger border-0 px-3" onclick="this.parentElement.remove()">
                    <i class="fa fa-times"></i>
                </button>
            `;
            container.appendChild(div);
        }
    </script>
</head>
<body>

<div class="reg-card">
    <div class="brand-logo">
        <i class="fa fa-rocket text-primary me-2"></i>NEXUS<span class="text-primary">PRO</span>
    </div>

    <h3 class="fw-800 mb-1 text-center">Start Your Journey</h3>
    <p class="text-muted small text-center mb-4">Create your account to track A/L progress</p>

    <form action="" method="POST" enctype="multipart/form-data">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Full Name</label>
                <input type="text" name="name" class="form-control" placeholder="John Doe" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control" placeholder="john@example.com" required>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Security Password</label>
            <input type="password" name="password" class="form-control" placeholder="••••••••" required>
        </div>

        <div class="subject-input-group mb-3">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <label class="form-label mb-0">Exam Subjects</label>
                <button type="button" class="btn btn-add-sub btn-sm rounded-pill px-3" onclick="addSubjectField()">
                    <i class="fa fa-plus-circle me-1"></i> Add Subject
                </button>
            </div>
            <div id="subject-container">
                <div class="input-group mb-2">
                    <input type="text" name="subjects[]" class="form-control" placeholder="Subject Name (e.g. Chemistry)" required>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-6 mb-3">
                <label class="form-label">Target Year</label>
                <input type="number" name="exam_year" class="form-control" placeholder="2025" required>
            </div>
            <div class="col-6 mb-3">
                <label class="form-label">Exam Date</label>
                <input type="date" name="exam_date" class="form-control" required>
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label">Profile Avatar</label>
            <input type="file" name="profile_image" class="form-control fw-bold" accept="image/*" required>
        </div>

        <button type="submit" name="register" class="btn btn-primary btn-reg w-100 mb-3">
            Create My Account <i class="fa fa-paper-plane ms-2"></i>
        </button>

        <div class="text-center">
            <p class="small text-muted mb-0">Already a member? 
                <a href="login.php" class="text-primary text-decoration-none fw-bold">Login here</a>
            </p>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>