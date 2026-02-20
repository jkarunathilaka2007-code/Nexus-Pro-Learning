<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include 'db.php';
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Battle Lobby | Nexus Pro</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        :root {
            --vision-bg: #030518;
            --accent-blue: #0075ff;
            --accent-green: #2dce89;
            --glass: linear-gradient(127.09deg, rgba(6, 11, 40, 0.94) 19.41%, rgba(10, 14, 35, 0.49) 76.65%);
            --glass-border: rgba(255, 255, 255, 0.1);
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--vision-bg);
            color: white;
            min-height: 100vh;
            background-image: url('https://raw.githubusercontent.com/creativetimofficial/public-assets/master/vision-ui-dashboard-chakra/surface.png');
            background-size: cover;
            background-attachment: fixed;
            display: flex;
            align-items: center;
        }

        .container { max-width: 900px; }

        .glass-card {
            background: var(--glass);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 30px;
            padding: 40px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
        }

        /* Tabs Styling */
        .nav-pills {
            background: rgba(255, 255, 255, 0.05);
            padding: 8px;
            border-radius: 18px;
            border: 1px solid var(--glass-border);
            margin-bottom: 40px;
        }

        .nav-pills .nav-link {
            border-radius: 14px;
            color: #a0aec0;
            font-weight: 700;
            font-size: 0.9rem;
            padding: 12px 25px;
            transition: 0.3s;
        }

        .nav-pills .nav-link.active {
            background: var(--accent-blue) !important;
            color: white !important;
            box-shadow: 0 4px 15px rgba(0, 117, 255, 0.3);
        }

        /* Form Inputs */
        .form-control {
            background: rgba(15, 21, 53, 0.5) !important;
            border: 1px solid var(--glass-border) !important;
            color: white !important;
            border-radius: 15px;
            padding: 15px 20px;
            transition: 0.3s;
        }

        .form-control:focus {
            border-color: var(--accent-blue) !important;
            box-shadow: 0 0 10px rgba(0, 117, 255, 0.2);
            transform: translateY(-2px);
        }

        label {
            font-size: 0.75rem;
            font-weight: 700;
            color: #718096;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Buttons */
        .btn-vision {
            background: linear-gradient(135deg, #0075ff, #005bc5);
            border: none;
            color: white;
            font-weight: 800;
            padding: 15px;
            border-radius: 15px;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: 0.4s;
            position: relative;
            overflow: hidden;
        }

        .btn-vision:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0, 117, 255, 0.4);
            color: white;
        }

        .btn-vision::after {
            content: '';
            position: absolute;
            top: 0; left: -100%;
            width: 100%; height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: 0.5s;
        }

        .btn-vision:hover::after { left: 100%; }

        .btn-join {
            background: linear-gradient(135deg, #2dce89, #2dcecc);
        }

        .btn-join:hover {
            box-shadow: 0 10px 25px rgba(45, 206, 137, 0.4);
        }

        .icon-box {
            width: 60px; height: 60px;
            background: var(--accent-blue);
            border-radius: 18px;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 20px;
            font-size: 1.5rem;
            box-shadow: 0 0 20px rgba(0, 117, 255, 0.3);
        }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-6">
            <div class="text-center mb-5">
                <h1 class="fw-800 mb-0">BATTLE <span style="color:var(--accent-blue)">ARENA</span></h1>
                <p class="text-muted small">Collaborative Learning & Competitive Quizzing</p>
            </div>

            <div class="glass-card">
                <ul class="nav nav-pills nav-justified" id="battleTab" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#admin">
                            <i class="fa fa-crown me-2"></i> ADMIN
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="pill" data-bs-target="#user">
                            <i class="fa fa-users me-2"></i> USERS
                        </button>
                    </li>
                </ul>

                <div class="tab-content pt-2">
                    <div class="tab-pane fade show active" id="admin">
                        <div class="icon-box"><i class="fa fa-plus"></i></div>
                        <h4 class="text-center fw-700 mb-4">Host New Room</h4>
                        <form action="room_logic.php" method="POST">
                            <div class="mb-3">
                                <label>Room Name</label>
                                <input type="text" name="room_name" class="form-control" placeholder="Ex: Physics Masters" required>
                            </div>
                            <div class="mb-3">
                                <label>Max Members (Inc. Admin)</label>
                                <input type="number" name="max_members" class="form-control" placeholder="5" required>
                            </div>
                            <div class="mb-4">
                                <label>Referral Code</label>
                                <input type="text" name="ref_code" class="form-control" placeholder="PHY101" required>
                            </div>
                            <button name="create_room" class="btn btn-vision w-100">Create Room <i class="fa fa-arrow-right ms-2"></i></button>
                        </form>
                    </div>

                    <div class="tab-pane fade" id="user">
                        <div class="icon-box" style="background:var(--accent-green); box-shadow: 0 0 20px rgba(45, 206, 137, 0.3);"><i class="fa fa-door-open"></i></div>
                        <h4 class="text-center fw-700 mb-4">Join Room</h4>
                        <form action="room_logic.php" method="POST">
                            <div class="mb-3">
                                <label>Target Room Name</label>
                                <input type="text" name="room_name_join" class="form-control" placeholder="Ex: Physics Masters" required>
                            </div>
                            <div class="mb-4">
                                <label>Enter Referral Code</label>
                                <input type="text" name="ref_code_join" class="form-control" placeholder="Enter Code Here" required>
                            </div>
                            <button name="join_room" class="btn btn-vision btn-join w-100">Join Battle <i class="fa fa-bolt ms-2"></i></button>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="text-center mt-4">
                <a href="dashboard.php" class="text-muted text-decoration-none small fw-600 hover-white">
                    <i class="fa fa-chevron-left me-1"></i> Back to Dashboard
                </a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>