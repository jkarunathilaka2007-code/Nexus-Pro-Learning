<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) { 
    header("Location: login.php"); 
    exit(); 
}
$user_id = $_SESSION['user_id'];

// 1. Database eken data gamu
$res = mysqli_query($conn, "SELECT * FROM timetables WHERE user_id = '$user_id' LIMIT 1");
$tt_row = mysqli_fetch_assoc($res);

if (!$tt_row) {
    header("Location: create_timetable.php");
    exit();
}

$timetable = json_decode($tt_row['content'], true);
$days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

// Default events list
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
    <title>Edit Smart Schedule | Nexus Pro</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root { --primary: #6366f1; --dark: #0f172a; --card: rgba(30, 41, 59, 0.7); }
        body { background: var(--dark); color: #f8fafc; font-family: 'Plus Jakarta Sans', sans-serif; }
        .glass-card { background: var(--card); backdrop-filter: blur(15px); border: 1px solid rgba(255,255,255,0.08); border-radius: 24px; padding: 25px; margin-bottom: 25px; }
        .tt-dropdown { background-color: #000 !important; color: #fff !important; border: 1px solid rgba(255,255,255,0.2) !important; border-radius: 8px; }
        .event-chip { background: rgba(99, 102, 241, 0.1); border: 1px solid var(--primary); color: #818cf8; padding: 5px 15px; border-radius: 50px; display: inline-flex; align-items: center; gap: 8px; margin: 5px; }
        .event-chip i { cursor: pointer; color: #ef4444; }
        .time-box { background: #000 !important; color: #10b981 !important; font-weight: bold; border: none; text-align: center; }
        .day-label { color: var(--primary); font-weight: 800; font-size: 0.7rem; text-transform: uppercase; }
    </style>
</head>
<body>

<div class="container py-4">
    <form action="save_timetable.php" method="POST" id="editForm">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <a href="timetable.php" class="text-white-50 text-decoration-none small"><i class="fa fa-arrow-left"></i> Back</a>
                <h3 class="fw-800 mb-0">Customize <span class="text-primary">Schedule</span></h3>
            </div>
            <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow">SAVE UPDATES</button>
        </div>

        <div class="glass-card">
            <h6 class="fw-bold mb-3 text-white-50 small">EVENT MANAGER</h6>
            <div id="eventContainer" class="mb-3">
                <?php foreach($default_events as $ev): ?>
                    <div class="event-chip default-ev" data-name="<?= $ev['name'] ?>" data-min="<?= $ev['min'] ?>">
                        <span><?= $ev['name'] ?> (<?= $ev['min'] ?>m)</span>
                    </div>
                <?php endforeach; ?>
                </div>
            
            <div class="row g-2">
                <div class="col-md-5">
                    <input type="text" id="evName" class="form-control" placeholder="New Subject (e.g. Chemistry)">
                </div>
                <div class="col-md-3">
                    <input type="number" id="evMin" class="form-control" placeholder="Mins (e.g. 90)">
                </div>
                <div class="col-md-4">
                    <button type="button" class="btn btn-outline-primary w-100" onclick="addNewEventChip()">Add to List</button>
                </div>
            </div>
        </div>

        <div class="glass-card p-0 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-dark mb-0 text-center">
                    <thead>
                        <tr style="background: rgba(255,255,255,0.02);">
                            <th class="p-3" style="width: 180px;">Time Range</th>
                            <?php foreach($days as $d) echo "<th class='p-3 day-label'>".substr($d,0,3)."</th>"; ?>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        <?php 
                        $slots = $timetable['slots'];
                        $data = $timetable['data'];
                        foreach($slots as $index => $slot): 
                            $row_id = $index + 1;
                            $t = explode(' - ', $slot['time']);
                        ?>
                        <tr>
                            <td class="p-2">
                                <div class="d-flex align-items-center gap-1">
                                    <input type="text" name="slot_start[]" class="form-control form-control-sm time-box start-time" value="<?= $t[0] ?>" readonly>
                                    <input type="text" name="slot_end[]" class="form-control form-control-sm time-box end-time" value="<?= $t[1] ?>" readonly>
                                </div>
                            </td>
                            <?php foreach($days as $day): 
                                $val = $data[$row_id][$day] ?? '';
                            ?>
                            <td class="p-1">
                                <select name="data[<?= $row_id ?>][<?= $day ?>]" class="form-select form-select-sm tt-dropdown" data-selected="<?= $val ?>" onchange="calculateTimes()">
                                    <option value="">-</option>
                                    <?php if($val): ?>
                                        <option value="<?= $val ?>" selected><?= $val ?></option>
                                    <?php endif; ?>
                                </select>
                            </td>
                            <?php endforeach; ?>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <input type="hidden" id="startTime" value="<?= explode(' - ', $timetable['slots'][0]['time'])[0] ?>">
    </form>
</div>

<script>
let customEvents = [];

function addNewEventChip() {
    const name = document.getElementById('evName').value.trim();
    const min = document.getElementById('evMin').value.trim();
    
    if(!name || !min) return;

    const chipId = 'ev-' + Date.now();
    const chipHtml = `
        <div class="event-chip custom-ev" id="${chipId}" data-name="${name}" data-min="${min}">
            <span>${name} (${min}m)</span>
            <i class="fa fa-times-circle ms-2" onclick="removeEventChip('${chipId}')"></i>
        </div>`;
    
    document.getElementById('eventContainer').insertAdjacentHTML('beforeend', chipHtml);
    document.getElementById('evName').value = '';
    document.getElementById('evMin').value = '';
    
    updateDropdowns();
}

function removeEventChip(id) {
    document.getElementById(id).remove();
    updateDropdowns();
    calculateTimes(); // Re-calculate if a deleted event was used
}

function updateDropdowns() {
    // Collect all events from chips
    let allEvents = [];
    document.querySelectorAll('.event-chip').forEach(chip => {
        allEvents.push({
            name: chip.getAttribute('data-name'),
            min: chip.getAttribute('data-min')
        });
    });

    document.querySelectorAll('.tt-dropdown').forEach(select => {
        const currentVal = select.value;
        select.innerHTML = '<option value="">- Select -</option>';
        
        allEvents.forEach(ev => {
            const isSelected = (currentVal === ev.name) ? 'selected' : '';
            select.insertAdjacentHTML('beforeend', `
                <option value="${ev.name}" data-min="${ev.min}" ${isSelected}>${ev.name}</option>
            `);
        });
    });
}

function calculateTimes() {
    const startVal = document.getElementById('startTime').value;
    const rows = document.querySelectorAll('#tableBody tr');
    let currentTime = new Date(`2026-01-01T${startVal}:00`);

    rows.forEach(row => {
        const startInput = row.querySelector('.start-time');
        const endInput = row.querySelector('.end-time');
        let maxMin = 0;

        row.querySelectorAll('.tt-dropdown').forEach(select => {
            const opt = select.options[select.selectedIndex];
            const m = opt ? parseInt(opt.getAttribute('data-min')) || 0 : 0;
            if(m > maxMin) maxMin = m;
        });

        if(maxMin === 0) maxMin = 60; // Default fallback

        startInput.value = currentTime.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: false });
        currentTime = new Date(currentTime.getTime() + maxMin * 60000);
        endInput.value = currentTime.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: false });
    });
}

// Initial Load
window.onload = () => {
    updateDropdowns();
    calculateTimes();
};
</script>

</body>
</html>