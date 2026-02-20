<?php
session_start();
include 'db.php'; 

// දැනටමත් Login වෙලා නම් කෙලින්ම Dashboard එකට යවනවා
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

if (isset($_POST['login'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email='$email'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);
        
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['user_name'] = $row['name'];
            $_SESSION['user_image'] = $row['profile_image'];
            $_SESSION['exam_date'] = $row['exam_date'];
            $_SESSION['subjects'] = $row['subjects'];

            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Password වැරදියි. නැවත උත්සාහ කරන්න.";
        }
    } else {
        $error = "මෙම Email ලිපිනය පද්ධතියේ ලියාපදිංචි කර නැත.";
    }
}
?>

<!DOCTYPE html>
<html lang="si">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Nexus Exam Pro</title>
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
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-image: radial-gradient(circle at 10% 20%, rgba(99, 102, 241, 0.1) 0%, transparent 40%),
                              radial-gradient(circle at 90% 80%, rgba(6, 182, 212, 0.05) 0%, transparent 40%);
            color: white;
            padding: 20px;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 28px;
            padding: 40px;
            width: 100%;
            max-width: 450px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }

        .brand-logo {
            font-size: 1.8rem;
            font-weight: 800;
            text-align: center;
            margin-bottom: 30px;
            letter-spacing: -1px;
        }

        .form-label {
            font-weight: 600;
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.7);
            margin-left: 5px;
        }

        .input-group-text {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: rgba(255, 255, 255, 0.5);
            border-radius: 12px 0 0 12px;
        }

        .form-control {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
            padding: 12px 15px;
            border-radius: 0 12px 12px 0;
            transition: 0.3s;
        }

        .form-control:focus {
            background: rgba(255, 255, 255, 0.08);
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.2);
            color: white;
        }

        .btn-login {
            background: linear-gradient(135deg, var(--primary), var(--accent));
            border: none;
            padding: 14px;
            border-radius: 12px;
            font-weight: 700;
            margin-top: 10px;
            transition: 0.3s;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(99, 102, 241, 0.4);
        }

        .alert-custom {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.2);
            color: #fca5a5;
            border-radius: 12px;
            font-size: 0.9rem;
        }

        .register-link {
            color: var(--primary);
            text-decoration: none;
            font-weight: 700;
            transition: 0.2s;
        }

        .register-link:hover {
            color: var(--accent);
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="brand-logo">
        <i class="fa fa-rocket text-primary me-2"></i>NEXUS<span class="text-primary">PRO</span>
    </div>

    <h4 class="fw-bold mb-1">Welcome Back!</h4>
    <p class="text-muted small mb-4">Please enter your details to sign in.</p>
    
    <?php if(isset($error)): ?>
        <div class="alert alert-custom py-2 mb-4" role="alert">
            <i class="fa fa-exclamation-circle me-2"></i><?php echo $error; ?>
        </div>
    <?php endif; ?>

    <form action="login.php" method="POST">
        <div class="mb-3">
            <label class="form-label">Email Address</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fa fa-envelope"></i></span>
                <input type="email" name="email" class="form-control" placeholder="name@example.com" required>
            </div>
        </div>
        
        <div class="mb-4">
            <label class="form-label">Password</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fa fa-lock"></i></span>
                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>
        </div>

        <div class="d-grid">
            <button type="submit" name="login" class="btn btn-primary btn-login">
                Sign In <i class="fa fa-arrow-right ms-2 small"></i>
            </button>
        </div>
    </form>
    
    <div class="text-center mt-4">
        <p class="mb-0 small text-muted">Don't have an account? 
            <a href="register.php" class="register-link">Create Account</a>
        </p>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>