<?php
session_start();
include 'db.php';
$res_id = $_GET['res_id'];

$res_query = mysqli_query($conn, "SELECT * FROM room_results WHERE id = '$res_id'");
$result = mysqli_fetch_assoc($res_query);
$user_answers = json_encode($result['answers_json']); // Wait, simple fix:
$user_answers = json_decode($result['answers_json'], true);

$room_id = $result['room_id'];
$questions = mysqli_query($conn, "SELECT * FROM room_questions WHERE room_id = '$room_id'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Review Paper</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #030518; color: white; }
        .glass { background: rgba(255, 255, 255, 0.05); border-radius: 15px; padding: 20px; margin-bottom: 20px; }
        .correct { border: 2px solid #28a745; background: rgba(40, 167, 69, 0.1); }
        .wrong { border: 2px solid #dc3545; background: rgba(220, 53, 69, 0.1); }
        .badge-correct { background: #28a745; }
    </style>
</head>
<body>
<div class="container py-5">
    <h2 class="mb-4">Review Your Paper</h2>
    <?php $i=1; while($q = mysqli_fetch_assoc($questions)): 
        $q_id = $q['id'];
        $u_ans = $user_answers[$q_id];
    ?>
    <div class="glass">
        <h5><?= $i++ ?>. <?= $q['question_text'] ?></h5>
        <div class="mt-3">
            <?php 
            $opts = mysqli_query($conn, "SELECT * FROM room_question_options WHERE question_id = '$q_id'");
            while($o = mysqli_fetch_assoc($opts)):
                $class = "";
                if($o['is_correct']) $class = "text-success fw-bold"; // නිවැරදි එක
                if($u_ans == $o['id'] && !$o['is_correct']) $class = "text-danger fw-bold"; // යූසර් වැරදි එකක් තේරුවා නම්
            ?>
            <div class="p-2 <?= ($u_ans == $o['id']) ? 'border rounded mb-1' : '' ?> <?= $class ?>">
                <?= ($u_ans == $o['id']) ? '➤ ' : '' ?> <?= $o['option_text'] ?>
                <?php if($o['is_correct']) echo ' <span class="badge badge-correct">Correct</span>'; ?>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
    <?php endwhile; ?>
    <a href="results.php" class="btn btn-primary">Back to Results</a>
</div>
</body>
</html>