<?php
session_start();
include 'db.php';

if (isset($_POST['options']) && isset($_POST['question'])) {
    $rid = (int)$_POST['room_id'];
    $uid = $_SESSION['user_id'];
    $q = mysqli_real_escape_string($conn, $_POST['question']);
    $opts = $_POST['options']; // මේක Array එකක්

    // Options 5ක් සඳහා හිස් Array එකක් හදාගන්නවා (Default values)
    $o = array_fill(0, 5, ''); 

    // ආපු Options ටික පිළිවෙළට Array එකට දානවා
    foreach($opts as $key => $val) {
        if($key < 5) {
            $o[$key] = mysqli_real_escape_string($conn, $val);
        }
    }

    // Database එකට Insert කිරීම
    $sql = "INSERT INTO study_polls (room_id, user_id, question, option_a, option_b, option_c, option_d, option_e) 
            VALUES ('$rid', '$uid', '$q', '$o[0]', '$o[1]', '$o[2]', '$o[3]', '$o[4]')";
    
    if(mysqli_query($conn, $sql)) {
        $last_id = mysqli_insert_id($conn);
        // Chat එකට Poll ID එක යවනවා
        $msg = "[POLL:" . $last_id . "]";
        mysqli_query($conn, "INSERT INTO study_group_messages (room_id, user_id, message) VALUES ('$rid', '$uid', '$msg')");
        echo "success";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>