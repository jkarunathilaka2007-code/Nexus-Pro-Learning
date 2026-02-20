<?php
error_reporting(0);
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include 'db.php';

if (!isset($_GET['res_id'])) {
    die("<div style='color:white; text-align:center; padding-top:100px;'><h3>Invalid Access!</h3></div>");
}

$res_id = mysqli_real_escape_string($conn, $_GET['res_id']);
$query = "SELECT rr.*, u.name FROM room_results rr JOIN users u ON rr.user_id = u.id WHERE rr.id = '$res_id'";
$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);

if (!$data) { die("Result not found!"); }

$percentage = ($data['total_questions'] > 0) ? round(($data['score'] / $data['total_questions']) * 100) : 0;
$formatted_date = ($data['created_at']) ? date("F j, Y", strtotime($data['created_at'])) : date("F j, Y");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate - <?= htmlspecialchars($data['name']) ?></title>
    
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@700&family=Great+Vibes&family=Plus+Jakarta+Sans:wght@400;600&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

    <style>
        body { background: #0b0e14; margin: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 100vh; padding: 10px; box-sizing: border-box; }

        /* සහතිකය Responsive කිරීම */
        #certificate-area {
            width: 100%;
            max-width: 850px; /* උපරිම පළල */
            aspect-ratio: 850 / 600; /* සහතිකයක හැඩය පවත්වා ගැනීම */
            background: #111522;
            padding: 5%;
            position: relative;
            border: 8px solid #b49410;
            outline: 2px solid #e6c646;
            outline-offset: -15px;
            color: #fff;
            text-align: center;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            justify-content: space-around;
        }

        .header h1 { font-family: 'Cinzel', serif; font-size: 5vw; color: #e6c646; margin: 0; letter-spacing: 4px; }
        @media(min-width: 850px) { .header h1 { font-size: 50px; } }

        .presented-to { font-size: 14px; color: #94a3b8; text-transform: uppercase; margin-top: 20px; font-family: sans-serif; }
        
        .student-name { 
            font-family: 'Great Vibes', cursive; 
            font-size: 8vw; 
            color: #fff; 
            margin: 10px 0;
            border-bottom: 2px solid rgba(180, 148, 16, 0.3);
            display: inline-block;
        }
        @media(min-width: 850px) { .student-name { font-size: 70px; } }

        .achievement-text { font-family: 'Plus Jakarta Sans', sans-serif; font-size: 16px; color: #cbd5e1; max-width: 80%; margin: 0 auto; }

        .footer-stats { display: flex; justify-content: space-around; align-items: center; margin-top: 20px; }
        .sign-box { border-top: 1px solid rgba(148, 163, 184, 0.3); width: 25%; padding-top: 5px; font-size: 12px; color: #94a3b8; font-family: sans-serif; }
        
        .gold-seal {
            width: 80px; height: 80px;
            background: radial-gradient(circle, #f4d03f 0%, #b49410 100%);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            color: #000; font-weight: 800; font-family: 'Cinzel', serif;
            border: 3px double #111522; font-size: 10px;
        }

        /* Controls */
        .controls { margin-top: 30px; display: flex; gap: 10px; flex-wrap: wrap; justify-content: center; }
        .btn { background: #e6c646; color: #000; padding: 12px 25px; border-radius: 10px; font-weight: 800; text-decoration: none; border: none; cursor: pointer; font-family: sans-serif; }
        .btn-img { background: #2ecc71; color: white; }
    </style>
</head>
<body>

    <div id="certificate-area">
        <div class="header">
            <h1>CERTIFICATE</h1>
            <p style="letter-spacing: 3px; font-size: 12px; color: #94a3b8; margin: 0;">OF EXCELLENCE</p>
        </div>

        <div class="content">
            <p class="presented-to">This certificate is proudly presented to</p>
            <div class="student-name"><?= htmlspecialchars($data['name']) ?></div>
            <p class="achievement-text">
                For outstanding performance in the Battle Challenge with a score of 
                <strong><?= $data['score'] ?>/<?= $data['total_questions'] ?></strong> 
                and an accuracy of <strong><?= $percentage ?>%</strong>.
            </p>
        </div>

        <div class="footer-stats">
            <div class="sign-box">
                <div style="color: #e6c646; margin-bottom: 5px;"><?= $formatted_date ?></div>
                ISSUE DATE
            </div>
            <div class="gold-seal">
                <div style="text-align:center;">PASSED<br><span style="font-size: 16px;"><?= $percentage ?>%</span></div>
            </div>
            <div class="sign-box">
                <div style="color: #fff; margin-bottom: 5px; font-weight: bold;">NEXUS PRO</div>
                OFFICIAL SEAL
            </div>
        </div>
    </div>

    <div class="controls">
        <a href="results.php" class="btn" style="background: #34495e; color: white;">Back</a>
        <button onclick="downloadImage()" class="btn btn-img">Download as Image (PNG)</button>
        <button onclick="window.print()" class="btn">Print PDF</button>
    </div>

    <script>
    function downloadImage() {
        const cert = document.getElementById('certificate-area');
        // Image එක download කරන වෙලාවට responsive ප්‍රශ්න නැතිවීමට scale එක වැඩි කරනවා
        html2canvas(cert, { scale: 2 }).then(canvas => {
            let link = document.createElement('a');
            link.download = 'Nexus_Certificate_<?= htmlspecialchars($data['name']) ?>.png';
            link.href = canvas.toDataURL("image/png");
            link.click();
        });
    }
    </script>

</body>
</html>