<?php
session_start();
include 'db.php';
if (!isset($_SESSION['user_id']) || !isset($_GET['goal_id'])) { exit("Access Denied"); }

$goal_id = $_GET['goal_id'];
$user_id = $_SESSION['user_id'];

// Goal Data & History
$goal_q = mysqli_query($conn, "SELECT g.*, 
          (SELECT SUM(duration) FROM timer_sessions WHERE goal_id = g.goal_id) as total_completed 
          FROM goals g WHERE g.goal_id = '$goal_id'");
$goal = mysqli_fetch_assoc($goal_q);

$completed_before = $goal['total_completed'] ? (int)$goal['total_completed'] : 0;
$goal_seconds = (int)$goal['goal_duration_seconds'];
$focus_limit = (int)$goal['focus_period'] * 60; 
$break_limit = (int)$goal['interval_period'] * 60;
$start_time_now = date('Y-m-d H:i:s');

// Stop Logic
if (isset($_POST['stop'])) {
    $end_time = date('Y-m-d H:i:s');
    $session_duration = (int)$_POST['session_seconds'];
    
    // Only save if duration > 0
    if ($session_duration > 0) {
        $stmt = $conn->prepare("INSERT INTO timer_sessions (goal_id, user_id, start_time, end_time, duration) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iissi", $goal_id, $user_id, $_POST['start_time_val'], $end_time, $session_duration);
        $stmt->execute();

        if (($completed_before + $session_duration) >= $goal_seconds) {
            mysqli_query($conn, "UPDATE goals SET done = 1 WHERE goal_id = '$goal_id'");
        }
    }
    header("Location: timer.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Nexus Focus | Voice Enabled Timer</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root { --bg: #050810; --accent: #10b981; --break: #f59e0b; }
        body { 
            background: var(--bg); background-image: radial-gradient(circle at center, #111827 0%, #050810 100%);
            color: white; height: 100vh; display: flex; align-items: center; justify-content: center; font-family: 'Plus Jakarta Sans', sans-serif; overflow: hidden;
        }
        .timer-wrapper { position: relative; width: 400px; height: 400px; display: flex; align-items: center; justify-content: center; }
        svg { position: absolute; top: 0; left: 0; transform: rotate(-90deg); }
        .circle-bg { fill: none; stroke: rgba(255, 255, 255, 0.05); stroke-width: 10; }
        .circle-progress { fill: none; stroke: var(--accent); stroke-width: 10; stroke-linecap: round; stroke-dasharray: 1131; stroke-dashoffset: 1131; transition: 0.1s linear; }
        .ring-content { text-align: center; z-index: 10; }
        .status-badge { font-size: 0.7rem; font-weight: 800; letter-spacing: 2px; text-transform: uppercase; padding: 4px 12px; border-radius: 50px; border: 1px solid var(--accent); color: var(--accent); display: inline-block; margin-bottom: 10px; }
        .main-clock { font-size: 4.5rem; font-weight: 800; line-height: 1; letter-spacing: -2px; margin-bottom: 5px; }
        .progress-text { font-size: 0.9rem; color: #94a3b8; font-weight: 600; }
        .break-clock { font-size: 1.5rem; color: var(--break); font-weight: 700; display: none; margin-top: 5px; }
        .stop-btn { margin-top: 50px; background: rgba(239, 68, 68, 0.1); border: 1px solid #ef4444; color: #ef4444; padding: 12px 40px; border-radius: 50px; font-weight: 800; transition: 0.3s; cursor: pointer; position: absolute; bottom: -100px; left: 50%; transform: translateX(-50%); }
        .stop-btn:hover { background: #ef4444; color: white; box-shadow: 0 0 25px rgba(239, 68, 68, 0.4); }
    </style>
</head>
<body>

<div class="timer-wrapper">
    <svg width="400" height="400">
        <circle class="circle-bg" cx="200" cy="200" r="180"></circle>
        <circle id="ring" class="circle-progress" cx="200" cy="200" r="180"></circle>
    </svg>

    <div class="ring-content">
        <div id="status-badge" class="status-badge">Focusing</div>
        <div id="main-clock" class="main-clock">00:00:00</div>
        <div id="break-clock" class="break-clock">Rest: 00:00</div>
        <div class="progress-text">Goal Progress: <span id="pct-text" class="text-white">0%</span></div>
    </div>

    <form method="POST" id="stopForm">
        <input type="hidden" name="session_seconds" id="session_seconds" value="0">
        <input type="hidden" name="start_time_val" value="<?= $start_time_now ?>">
        <button type="submit" name="stop" id="actualStopBtn" class="stop-btn">STOP SESSION</button>
    </form>
</div>

<script>
    // --- CONFIGURATION ---
    let completedBefore = <?= $completed_before ?>;
    let goalSecs = <?= $goal_seconds ?>;
    let focusLimit = <?= $focus_limit ?>; // e.g., 1500 seconds (25 mins)
    let breakLimit = <?= $break_limit ?>; // e.g., 300 seconds (5 mins)

    // --- STATE VARIABLES ---
    let sessionSecs = 0.0; // High precision float
    let lastTick = Date.now();
    let isBreak = false;
    let breakLeft = 0;
    
    // This is the FIX: We track the NEXT break time target
    // Instead of checking (sessionSecs % limit == 0), we check (sessionSecs >= nextTarget)
    let nextBreakThreshold = focusLimit; 

    // UI Elements
    const ring = document.getElementById('ring');
    const circum = 1131;

    // --- WAKE LOCK (Mobile Screen On) ---
    async function requestWakeLock() {
        try {
            if ('wakeLock' in navigator) {
                let wakeLock = await navigator.wakeLock.request('screen');
                console.log('Screen Wake Lock active');
            }
        } catch (err) { console.log('Wake Lock Error:', err); }
    }
    requestWakeLock();
    document.addEventListener('visibilitychange', () => {
        if (document.visibilityState === 'visible') { requestWakeLock(); lastTick = Date.now(); }
    });

    // --- VOICE FUNCTION ---
    function speak(message) {
        if ('speechSynthesis' in window) {
            const speech = new SpeechSynthesisUtterance();
            speech.text = message;
            speech.volume = 1;
            speech.rate = 1.0; 
            window.speechSynthesis.speak(speech);
        }
    }

    // --- UI UPDATER ---
    function updateUI() {
        let displaySecs = Math.floor(sessionSecs);
        let totalTime = completedBefore + displaySecs;
        let pct = Math.min((totalTime / goalSecs) * 100, 100);

        // Ring
        ring.style.strokeDashoffset = circum - (pct / 100 * circum);
        document.getElementById('pct-text').innerText = Math.round(pct) + "%";

        // Clock
        let h = Math.floor(displaySecs / 3600);
        let m = Math.floor((displaySecs % 3600) / 60);
        let s = displaySecs % 60;
        document.getElementById('main-clock').innerText = 
            `${h.toString().padStart(2,'0')}:${m.toString().padStart(2,'0')}:${s.toString().padStart(2,'0')}`;

        // Hidden Form Input
        document.getElementById('session_seconds').value = displaySecs;

        // Completion Check
        if (pct >= 100) handleGoalCompletion();
    }

    // --- MAIN LOOP ---
    function tick() {
        const now = Date.now();
        const delta = (now - lastTick) / 1000; // Exact seconds passed (e.g., 1.005)
        lastTick = now;

        if (!isBreak) {
            // Add exact time passed
            sessionSecs += delta;
            updateUI();

            // --- BREAK CHECK LOGIC (FIXED) ---
            // If we crossed the threshold, trigger break
            if (sessionSecs >= nextBreakThreshold) {
                startRest();
                // Move the goalpost for the next break
                nextBreakThreshold += focusLimit; 
            }

        } else {
            // Break Logic
            breakLeft -= delta;
            let displayBreak = Math.ceil(breakLeft);
            if (displayBreak < 0) displayBreak = 0;

            document.getElementById('break-clock').innerText = "Rest: " + Math.floor(displayBreak/60) + ":" + (displayBreak%60).toString().padStart(2,'0');
            
            if (breakLeft <= 0) stopRest();
        }
    }

    // --- BREAK FUNCTIONS ---
    function startRest() {
        isBreak = true;
        breakLeft = breakLimit;
        
        speak("Time for a break, buddy! Relax for a moment.");

        // UI Changes for Break
        document.getElementById('status-badge').innerText = "On Break";
        document.getElementById('status-badge').style.color = "var(--break)";
        document.getElementById('status-badge').style.borderColor = "var(--break)";
        document.getElementById('main-clock').style.opacity = "0.2";
        document.getElementById('break-clock').style.display = "block";
        ring.style.stroke = "var(--break)";
    }

    function stopRest() {
        isBreak = false;
        
        speak("Break is over! Let's focus again.");

        Swal.fire({
            title: 'Break Over!',
            text: 'Let\'s get back to work!',
            icon: 'info',
            confirmButtonText: 'Start Focus'
        }).then(() => {
            // Restore UI
            document.getElementById('status-badge').innerText = "Focusing";
            document.getElementById('status-badge').style.color = "var(--accent)";
            document.getElementById('status-badge').style.borderColor = "var(--accent)";
            document.getElementById('main-clock').style.opacity = "1";
            document.getElementById('break-clock').style.display = "none";
            ring.style.stroke = "var(--accent)";
            
            // Reset delta to avoid jump
            lastTick = Date.now();
        });
    }

    function handleGoalCompletion() {
        clearInterval(timerInt);
        speak("Congratulations! Goal reached.");
        Swal.fire({
            title: 'Goal Reached! 🎉',
            text: 'You did it!',
            icon: 'success',
            confirmButtonText: 'Finish'
        }).then(() => { document.getElementById('actualStopBtn').click(); });
    }

    // High frequency interval (100ms) for smooth updates
    // The 'delta' logic ensures the time is accurate regardless of frequency
    let timerInt = setInterval(tick, 100);

</script>

</body>
</html>