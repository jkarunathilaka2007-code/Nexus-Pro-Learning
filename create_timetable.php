<?php
session_start();
include 'db.php';
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }
$user_id = $_SESSION['user_id'];

$default_events = [
    ['name' => 'Wake Up', 'min' => 30],
    ['name' => 'Breakfast', 'min' => 30],
    ['name' => 'Lunch', 'min' => 60],
    ['name' => 'Dinner', 'min' => 45],
    ['name' => 'Sleep', 'min' => 480]
];
?>

<!DOCTYPE html>
<html lang="si">
<head>
    <meta charset="UTF-8">
    <title>Smart Timetable Creator | Nexus Pro</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root { --primary: #6366f1; --dark: #0f172a; --card: rgba(30, 41, 59, 0.7); }
        body { background: var(--dark); color: #f8fafc; font-family: 'Plus Jakarta Sans', sans-serif; }
        
        /* Glass Effect Cards */
        .glass-card { background: var(--card); backdrop-filter: blur(15px); border: 1px solid rgba(255,255,255,0.08); border-radius: 24px; padding: 25px; margin-bottom: 25px; }
        
        /* Dropdown Styling - Black Background, White Text */
        .tt-dropdown { 
            background-color: #000000 !important; 
            color: #ffffff !important; 
            border: 1px solid rgba(255,255,255,0.2) !important;
            cursor: pointer;
        }
        .tt-dropdown option { background: #000; color: #fff; padding: 10px; }

        .event-chip { background: rgba(99, 102, 241, 0.1); border: 1px solid var(--primary); color: #818cf8; padding: 5px 15px; border-radius: 50px; display: inline-flex; align-items: center; gap: 8px; margin: 5px; }
        .form-control { background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: white; border-radius: 12px; }
        
        .help-section { background: rgba(99, 102, 241, 0.05); border-left: 4px solid var(--primary); padding: 15px; border-radius: 12px; margin-bottom: 20px; }
        .day-label { color: var(--primary); font-weight: 800; font-size: 0.7rem; text-transform: uppercase; }
        .start-time, .end-time { background: #000 !important; color: #10b981 !important; font-weight: bold; border: none; }
    </style>
</head>
<body>

<div class="container py-4">
    <form action="save_timetable.php" method="POST" id="ttForm">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <a href="timetable.php" class="text-white-50 text-decoration-none small"><i class="fa fa-arrow-left"></i> Back</a>
                <h3 class="fw-800 mb-0">Smart <span class="text-primary">Planner</span></h3>
            </div>
            <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">SAVE TIMETABLE</button>
        </div>

        <div class="help-section">
            <h6 class="fw-bold text-primary"><i class="fa fa-lightbulb me-2"></i>How to use</h6>
            <ul class="small mb-0 text-white-700">
                <li>First, <b>Setup Subjects</b> Add the subjects you need and the time (minutes) spent on them separately.</li>
                <li>Enter the time you wake up.</li>
                <li>In the table <b>Dropdown</b> Select the subject for each day through me.</li>
                <li>The time when the next subject starts, according to the subject you choose <b>Automatically</b> Will be prepared.</li>
            </ul>
        </div>

        <div class="glass-card">
            <h6 class="fw-bold mb-3 text-white-50 uppercase small">1. Setup Subjects & Events</h6>
            <div id="eventList" class="mb-3">
                <?php foreach($default_events as $de): ?>
                    <div class="event-chip">
                        <span><?= $de['name'] ?> (<?= $de['min'] ?>m)</span>
                        <input type="hidden" name="event_names[]" value="<?= $de['name'] ?>">
                        <input type="hidden" name="event_mins[]" value="<?= $de['min'] ?>">
                        <i class="fa fa-times-circle" style="cursor:pointer" onclick="this.parentElement.remove(); updateDropdowns();"></i>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="small text-muted">Subject/Event Name</label>
                    <input type="text" id="newEvName" class="form-control" placeholder="e.g. Mathematics">
                </div>
                <div class="col-md-3">
                    <label class="small text-muted">Time (Minutes)</label>
                    <input type="number" id="newEvMin" class="form-control" placeholder="60">
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-outline-primary w-100" onclick="addEvent()"><i class="fa fa-plus"></i> Add</button>
                </div>
            </div>
        </div>

        <div class="glass-card">
            <div class="row g-3 align-items-center">
                <div class="col-md-3">
                    <label class="small text-muted">Wake up Time</label>
                    <input type="time" name="start_day_time" id="start_day_time" class="form-control" value="05:00" onchange="calculateTimes()">
                </div>
                <div class="col-md-3">
                    <label class="small text-muted">No. of Rows</label>
                    <input type="number" id="rowCount" class="form-control" value="8" min="1">
                </div>
                <div class="col-md-2">
                    <label class="d-none d-md-block">&nbsp;</label>
                    <button type="button" class="btn btn-secondary w-100" onclick="generateTable()">Set Rows</button>
                </div>
            </div>
        </div>

        <div class="glass-card p-0 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-dark mb-0 text-center">
                    <thead>
                        <tr style="background: rgba(255,255,255,0.02);">
                            <th class="p-3" style="width: 180px;">Time Range</th>
                            <?php $days = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday']; 
                            foreach($days as $d) echo "<th class='p-3 day-label'>".substr($d,0,3)."</th>"; ?>
                        </tr>
                    </thead>
                    <tbody id="tableBody"></tbody>
                </table>
            </div>
        </div>
    </form>
</div>

<script>
function addEvent() {
    let name = document.getElementById('newEvName').value;
    let min = document.getElementById('newEvMin').value;
    if(!name || !min) return;

    let chip = `<div class="event-chip">
                    <span>${name} (${min}m)</span>
                    <input type="hidden" name="event_names[]" value="${name}">
                    <input type="hidden" name="event_mins[]" value="${min}">
                    <i class="fa fa-times-circle" style="cursor:pointer" onclick="this.parentElement.remove(); updateDropdowns();"></i>
                </div>`;
    document.getElementById('eventList').insertAdjacentHTML('beforeend', chip);
    document.getElementById('newEvName').value = '';
    document.getElementById('newEvMin').value = '';
    updateDropdowns();
}

function generateTable() {
    let rows = document.getElementById('rowCount').value;
    let tbody = document.getElementById('tableBody');
    tbody.innerHTML = '';

    for(let i=1; i<=rows; i++) {
        let rowHtml = `<tr>
            <td class="p-2">
                <div class="d-flex align-items-center gap-1">
                    <input type="text" name="slot_start[]" class="form-control form-control-sm text-center start-time" readonly>
                    <input type="text" name="slot_end[]" class="form-control form-control-sm text-center end-time" readonly>
                </div>
            </td>`;
        <?php foreach($days as $d): ?>
            rowHtml += `<td class="p-1">
                <select name="data[${i}][<?= $d ?>]" class="form-select form-select-sm tt-dropdown" onchange="calculateTimes()">
                    <option value="">-</option>
                </select>
            </td>`;
        <?php endforeach; ?>
        rowHtml += `</tr>`;
        tbody.insertAdjacentHTML('beforeend', rowHtml);
    }
    updateDropdowns();
    calculateTimes();
}

function updateDropdowns() {
    let currentEvents = [];
    document.querySelectorAll('#eventList .event-chip').forEach(chip => {
        currentEvents.push({
            name: chip.querySelector('input[name="event_names[]"]').value,
            min: chip.querySelector('input[name="event_mins[]"]').value
        });
    });

    document.querySelectorAll('.tt-dropdown').forEach(dropdown => {
        let selected = dropdown.value;
        dropdown.innerHTML = '<option value="">- Select -</option>';
        currentEvents.forEach(ev => {
            dropdown.insertAdjacentHTML('beforeend', `<option value="${ev.name}" data-min="${ev.min}" ${selected == ev.name ? 'selected' : ''}>${ev.name}</option>`);
        });
    });
}

function calculateTimes() {
    let startTimeStr = document.getElementById('start_day_time').value;
    let rows = document.querySelectorAll('#tableBody tr');
    let lastEndTime = new Date(`2026-01-01T${startTimeStr}:00`);

    rows.forEach((row) => {
        let startInput = row.querySelector('.start-time');
        let endInput = row.querySelector('.end-time');
        let rowMaxMin = 0;

        row.querySelectorAll('.tt-dropdown').forEach(select => {
            let opt = select.options[select.selectedIndex];
            let m = opt ? parseInt(opt.getAttribute('data-min')) || 0 : 0;
            if(m > rowMaxMin) rowMaxMin = m;
        });

        if(rowMaxMin == 0) rowMaxMin = 60; 

        startInput.value = lastEndTime.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: false });
        lastEndTime = new Date(lastEndTime.getTime() + rowMaxMin * 60000);
        endInput.value = lastEndTime.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: false });
    });
}

window.onload = generateTable;
</script>
</body>
</html>