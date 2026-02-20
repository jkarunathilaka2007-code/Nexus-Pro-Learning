<?php
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NexusPro | Lucky Spin 🎡</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
    <style>
        :root { --primary: #3b82f6; --dark: #050810; }
        body { background: var(--dark); color: white; font-family: 'Inter', sans-serif; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; margin: 0; }
        .main-card { background: rgba(15, 23, 42, 0.95); padding: 30px; border-radius: 30px; border: 1px solid rgba(59, 130, 246, 0.2); width: 100%; max-width: 420px; text-align: center; box-shadow: 0 20px 50px rgba(0,0,0,0.5); }
        .wheel-box { position: relative; width: 100%; max-width: 300px; aspect-ratio: 1/1; margin: 25px auto; }
        #wheel { width: 100%; height: 100%; border-radius: 50%; border: 6px solid #1e293b; transition: transform 5s cubic-bezier(0.1, 0, 0.1, 1); }
        .pointer { position: absolute; top: -15px; left: 50%; transform: translateX(-50%); width: 0; height: 0; border-left: 15px solid transparent; border-right: 15px solid transparent; border-top: 30px solid #ef4444; z-index: 10; filter: drop-shadow(0 5px 5px rgba(0,0,0,0.5)); }
        .center-pin { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 40px; height: 40px; background: white; border-radius: 50%; z-index: 11; border: 4px solid #1e293b; box-shadow: 0 0 15px rgba(0,0,0,0.3); }
        .coin-balance { background: rgba(59, 130, 246, 0.1); padding: 10px 20px; border-radius: 50px; border: 1px solid rgba(59, 130, 246, 0.3); display: inline-flex; align-items: center; margin-bottom: 10px; }
        .btn-spin { background: linear-gradient(135deg, #3b82f6, #2563eb); border: none; color: white; padding: 14px 0; width: 100%; border-radius: 50px; font-weight: 800; font-size: 1.2rem; transition: 0.3s; margin-top: 15px; box-shadow: 0 10px 20px rgba(37, 99, 235, 0.3); }
        .btn-spin:disabled { opacity: 0.6; transform: scale(0.98); }
        #spinNote { font-size: 0.85rem; margin-top: 10px; color: #94a3b8; }
    </style>
</head>
<body>

<div class="main-card">
    <div class="coin-balance">
        <img src="https://cdn-icons-png.flaticon.com/512/272/272525.png" width="22" class="me-2">
        <span class="fw-bold">Balance: <span id="balanceDisplay">...</span> Coins</span>
    </div>

    <h2 class="fw-bold mb-0">Lucky <span class="text-primary">Spin</span></h2>
    <p id="spinNote">Checking status...</p>

    <div class="wheel-box">
        <div class="pointer"></div>
        <div class="center-pin"></div>
        <canvas id="wheel" width="500" height="500"></canvas>
    </div>

    <button id="spinBtn" class="btn-spin" onclick="handleSpinClick()">SPIN NOW</button>
</div>

<audio id="tickSound" src="https://assets.mixkit.co/active_storage/sfx/2003/2003-preview.mp3"></audio>

<script>
    const canvas = document.getElementById('wheel');
    const ctx = canvas.getContext('2d');
    const balanceDisplay = document.getElementById('balanceDisplay');
    const spinBtn = document.getElementById('spinBtn');
    const spinNote = document.getElementById('spinNote');
    
    const rewards = [
        {label: "10", color: "#1e293b"}, {label: "50", color: "#3b82f6"},
        {label: "0", color: "#0f172a"}, {label: "100", color: "#10b981"},
        {label: "5", color: "#1e293b"}, {label: "25", color: "#8b5cf6"},
        {label: "200", color: "#f59e0b"}, {label: "0", color: "#0f172a"}
    ];

    let currentRotation = 0;
    let userBalance = 0;
    let isFreeAvailable = false;

    function drawWheel() {
        const numSectors = rewards.length;
        const arc = 2 * Math.PI / numSectors;
        rewards.forEach((reward, i) => {
            const angle = i * arc;
            ctx.beginPath();
            ctx.fillStyle = reward.color;
            ctx.moveTo(250, 250); ctx.arc(250, 250, 250, angle, angle + arc);
            ctx.fill();
            ctx.save();
            ctx.translate(250, 250); ctx.rotate(angle + arc / 2);
            ctx.textAlign = "right"; ctx.fillStyle = "#fff"; ctx.font = "bold 30px sans-serif";
            ctx.fillText(reward.label, 230, 10); ctx.restore();
        });
    }
    drawWheel();

    async function refreshStatus() {
        const res = await fetch('check_status.php');
        const data = await res.json();
        userBalance = parseInt(data.total_coins);
        isFreeAvailable = data.free_spin;
        balanceDisplay.innerText = userBalance;
        spinNote.innerText = isFreeAvailable ? "Daily free spin is ready! 🎁" : "Next Spin: 25 Coins 💰";
    }
    refreshStatus();

    function handleSpinClick() {
        if (isFreeAvailable) {
            startRotation(0);
        } else {
            Swal.fire({
                title: 'Paid Spin?',
                text: "නොමිලේ අවස්ථාව අවසන්. Coins 25ක් වියදම් කරනවාද?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3b82f6',
                confirmButtonText: 'ඔව්, Spin කරන්න!',
                background: '#0f172a', color: '#fff'
            }).then((result) => {
                if (result.isConfirmed) {
                    if (userBalance >= 25) startRotation(25);
                    else Swal.fire('Error', 'Coins මදි මචං!', 'error');
                }
            });
        }
    }

    function startRotation(cost) {
        spinBtn.disabled = true;
        const deg = (360 * 8) + Math.floor(Math.random() * 360); 
        currentRotation += deg;
        canvas.style.transform = `rotate(${currentRotation}deg)`;

        setTimeout(async () => {
            const normalized = currentRotation % 360;
            const sectorDeg = 360 / rewards.length;
            let winIdx = Math.floor((360 - (normalized) + 270) % 360 / sectorDeg);
            const winAmt = rewards[winIdx].label;

            // Save to DB
            const response = await fetch('save_spin_logic.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `amount=${winAmt}&cost=${cost}`
            });
            const data = await response.json();

            if(data.status === 'success') {
                if(winAmt !== "0") {
                    confetti({ particleCount: 150, spread: 60 });
                    Swal.fire('දිනුමක්!', `ඔයා Coins ${winAmt} දිනුවා!`, 'success');
                } else {
                    Swal.fire('අයියෝ!', 'වාසනාව මදි, ආයෙ බලමු!', 'info');
                }
                refreshStatus();
            }
            spinBtn.disabled = false;
        }, 5000);
    }
</script>
</body>
</html>