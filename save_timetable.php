<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// 1. Form එකෙන් එන දත්ත ලබා ගැනීම
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $slot_starts = $_POST['slot_start'];
    $slot_ends = $_POST['slot_end'];
    $table_data = $_POST['data']; // සතියේ දින 7 ක දත්ත array එක

    // 2. Time Slots ටික ලස්සනට format කරගමු
    $slots = [];
    foreach ($slot_starts as $index => $start) {
        $slots[] = [
            'time' => $start . " - " . $slot_ends[$index]
        ];
    }

    // 3. Database එකට දාන්න පුළුවන් විදිහට මුළු Timetable එකම JSON කරමු
    $full_timetable = [
        'slots' => $slots,
        'data' => $table_data
    ];

    $json_content = mysqli_real_escape_string($conn, json_encode($full_timetable));

    // 4. දැනටමත් Timetable එකක් තියෙනවාද බලන්න
    $check = mysqli_query($conn, "SELECT id FROM timetables WHERE user_id = '$user_id'");

    if (mysqli_num_rows($check) > 0) {
        // තිබේ නම් - Update කරන්න
        $query = "UPDATE timetables SET content = '$json_content' WHERE user_id = '$user_id'";
    } else {
        // නැත්නම් - අලුතින් Insert කරන්න
        $query = "INSERT INTO timetables (user_id, content) VALUES ('$user_id', '$json_content')";
    }

    if (mysqli_query($conn, $query)) {
        // සාර්ථක නම් නැවත timetable.php වෙත යවන්න
        header("Location: timetable.php?status=success");
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>