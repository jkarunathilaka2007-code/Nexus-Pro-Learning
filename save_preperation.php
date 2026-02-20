<?php
session_start();
include 'db.php';

// පරිශීලකයා Login වී ඇත්දැයි බැලීම
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    
    // Form එකෙන් එන දත්ත ලබා ගැනීම
    $subject = mysqli_real_escape_string($conn, $_POST['subject']);
    $start_year = (int)$_POST['start_year'];
    $end_year = (int)$_POST['end_year'];
    
    // තෝරාගත් Paper Types (Array එකක් ලෙස එයි)
    $selected_types = isset($_POST['types']) ? $_POST['types'] : [];
    
    // ප්‍රශ්න ගණන සහ කාලය (Nested Array එකක් ලෙස එයි)
    $configs = isset($_POST['config']) ? $_POST['config'] : [];

    if (empty($selected_types)) {
        echo "<script>alert('කරුණාකර අවම වශයෙන් එක් පේපර් වර්ගයක්වත් තෝරන්න!'); window.history.back();</script>";
        exit();
    }

    // --- වැදගත්ම කොටස ---
    // Edit හෝ Add දෙකේදීම පහසුම ක්‍රමය වන්නේ අදාළ විෂයට ඇති පරණ දත්ත මකා අලුත් දත්ත ඇතුළත් කිරීමයි.
    $delete_old = "DELETE FROM trackers WHERE user_id = '$user_id' AND subject = '$subject'";
    mysqli_query($conn, $delete_old);

    $success = true;

    // තෝරාගත් සෑම පේපර් වර්ගයක් සඳහාම වෙන වෙනම record එකක් ඇතුළත් කිරීම
    foreach ($selected_types as $type) {
        $q_count = (int)$configs[$type]['q_count'];
        $time = (int)$configs[$type]['time'];

        $sql = "INSERT INTO trackers (user_id, subject, paper_type, question_count, start_year, end_year, allocated_time) 
                VALUES ('$user_id', '$subject', '$type', '$q_count', '$start_year', '$end_year', '$time')";
        
        if (!mysqli_query($conn, $sql)) {
            $success = false;
            break;
        }
    }

    if ($success) {
        // සාර්ථක නම් නැවත ලිස්ට් එකට යැවීම
        header("Location: add_subject_preparation.php?status=success");
    } else {
        echo "දත්ත ඇතුළත් කිරීමේදී දෝෂයක් සිදුවිය: " . mysqli_error($conn);
    }

} else {
    // POST Request එකක් නොවේ නම් ආපසු හරවා යැවීම
    header("Location: add_subject_preparation.php");
}
?>