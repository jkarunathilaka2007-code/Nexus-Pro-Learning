<?php
session_start();
if (isset($_GET['subject'])) {
    $subject = urlencode($_GET['subject']);
    // මුලින්ම කිසිම HTML එකක් නැතුව මේ ලින්ක් එකට යවනවා
    header("Location: start_preperation.php?subject=$subject&mode=split");
    exit();
} else {
    header("Location: dashboard.php");
    exit();
}
?>