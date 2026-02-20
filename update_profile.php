<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['profile_img'])) {
    $file = $_FILES['profile_img'];

    $fileName = $file['name'];
    $fileTmpName = $file['tmp_name'];
    $fileSize = $file['size'];
    $fileError = $file['error'];

    $fileExt = explode('.', $fileName);
    $fileActualExt = strtolower(end($fileExt));
    $allowed = array('jpg', 'jpeg', 'png');

    if (in_array($fileActualExt, $allowed)) {
        if ($fileError === 0) {
            if ($fileSize < 5000000) { // 5MB Limit
                $fileNameNew = "profile_" . $user_id . "_" . time() . "." . $fileActualExt;
                $fileDestination = 'uploads/' . $fileNameNew;

                if (move_uploaded_file($fileTmpName, $fileDestination)) {
                    
                    // 1. පරණ image එක අයින් කිරීම
                    $old_img_query = mysqli_query($conn, "SELECT profile_image FROM users WHERE id = '$user_id'");
                    $old_img_data = mysqli_fetch_assoc($old_img_query);
                    $old_path = $old_img_data['profile_image'];
                    
                    // Default image එකක් පාවිච්චි කරනවා නම් ඒක delete වෙන්න දෙන්න එපා
                    if (!empty($old_path) && file_exists($old_path) && strpos($old_path, 'default') === false) {
                        unlink($old_path);
                    }

                    // 2. Database එක update කිරීම
                    $sql = "UPDATE users SET profile_image = '$fileDestination' WHERE id = '$user_id'";
                    if (mysqli_query($conn, $sql)) {
                        // 3. වැදගත්ම දේ: SESSION එකත් UPDATE කරන්න
                        $_SESSION['user_image'] = $fileDestination;
                        
                        header("Location: dashboard.php?upload=success");
                        exit();
                    } else {
                        header("Location: dashboard.php?status=db_error");
                    }
                } else {
                    header("Location: dashboard.php?status=upload_failed");
                }
            } else {
                header("Location: dashboard.php?status=too_big");
            }
        } else {
            header("Location: dashboard.php?status=error");
        }
    } else {
        header("Location: dashboard.php?status=invalid_type");
    }
} else {
    header("Location: dashboard.php");
}
?>