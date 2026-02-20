<?php
session_start();
include 'db.php';
if (!isset($_SESSION['user_id'])) { die("Access Denied"); }

if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    $user_id = $_SESSION['user_id'];

    // තමන්ගේ සූත්‍රයක්මද මකන්නේ කියලා check කරනවා ආරක්ෂාවට
    $sql = "DELETE FROM user_formulas WHERE id = '$id' AND user_id = '$user_id'";
    
    if (mysqli_query($conn, $sql)) {
        header("Location: maths_bank.php?msg=Deleted");
    } else {
        echo "Error deleting record: " . mysqli_error($conn);
    }
}
?>