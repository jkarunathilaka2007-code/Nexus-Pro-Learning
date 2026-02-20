<?php
session_start();
include 'db.php';

if (isset($_POST['poll_id']) && isset($_POST['option'])) {
    $pid = (int)$_POST['poll_id'];
    $opt = $_POST['option'];
    $uid = $_SESSION['user_id'];

    // 1. මේ user දැනටමත් vote කරලාද කියලා බලනවා
    $check = mysqli_query($conn, "SELECT id FROM poll_votes WHERE poll_id = '$pid' AND user_id = '$uid'");

    if (mysqli_num_rows($check) == 0) {
        // 2. තවම vote කරලා නැත්නම්, vote එක register කරනවා
        mysqli_query($conn, "INSERT INTO poll_votes (poll_id, user_id) VALUES ('$pid', '$uid')");

        // 3. අදාළ column එක update කරනවා
        $allowed = ['a', 'b', 'c', 'd', 'e'];
        if (in_array($opt, $allowed)) {
            $col = "votes_" . $opt;
            mysqli_query($conn, "UPDATE study_polls SET $col = $col + 1 WHERE id = '$pid'");
            echo "success";
        }
    } else {
        echo "already_voted";
    }
}
?>