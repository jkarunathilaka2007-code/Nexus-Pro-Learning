<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    header("Location: revision_info.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$q_id = mysqli_real_escape_string($conn, $_GET['id']);

// ප්‍රශ්නයේ විස්තර ලබා ගැනීම
$res = mysqli_query($conn, "SELECT * FROM user_revisions WHERE id = '$q_id' AND user_id = '$user_id'");
$data = mysqli_fetch_assoc($res);

if (!$data) { die("Question not found!"); }

$isMCQ = ($data['paper_type'] == 'MCQ');
$options = $isMCQ ? json_decode($data['options_json']) : [];

// UPDATE PROCESS
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $question = mysqli_real_escape_string($conn, $_POST['question']);
    
    if ($isMCQ) {
        $new_opts = [$_POST['opt0'], $_POST['opt1'], $_POST['opt2'], $_POST['opt3']];
        $opts_json = mysqli_real_escape_string($conn, json_encode($new_opts));
        $correct_idx = mysqli_real_escape_string($conn, $_POST['correct_idx']);
        $sql = "UPDATE user_revisions SET question='$question', options_json='$opts_json', correct_idx='$correct_idx' WHERE id='$q_id'";
    } else {
        $ans_text = mysqli_real_escape_string($conn, $_POST['answer_text']);
        $sql = "UPDATE user_revisions SET question='$question', answer_text='$ans_text' WHERE id='$q_id'";
    }

    if (mysqli_query($conn, $sql)) {
        header("Location: revision_info.php?msg=success");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Question | Nexus</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #020617; color: #cbd5e1; padding: 50px 0; }
        .edit-card { background: #0f172a; border: 1px solid rgba(255,255,255,0.1); border-radius: 20px; padding: 30px; }
        .form-control { background: #020617; border: 1px solid rgba(255,255,255,0.1); color: white; border-radius: 10px; }
        .form-control:focus { background: #020617; color: white; border-color: #6366f1; box-shadow: none; }
        .btn-save { background: #6366f1; border: none; padding: 12px; font-weight: bold; border-radius: 10px; }
    </style>
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="edit-card shadow-lg">
                <h4 class="text-white mb-4">Edit <?= $data['paper_type'] ?> Question</h4>
                <form method="POST">
                    <div class="mb-4">
                        <label class="text-muted small mb-2">QUESTION</label>
                        <textarea name="question" class="form-control" rows="3" required><?= htmlspecialchars($data['question']) ?></textarea>
                    </div>

                    <?php if($isMCQ): ?>
                        <label class="text-muted small mb-2">OPTIONS (Select the radio for correct answer)</label>
                        <?php for($i=0; $i<4; $i++): ?>
                        <div class="input-group mb-3">
                            <div class="input-group-text bg-dark border-secondary">
                                <input type="radio" name="correct_idx" value="<?= $i ?>" class="form-check-input" <?= ($data['correct_idx'] == $i) ? 'checked' : '' ?>>
                            </div>
                            <input type="text" name="opt<?= $i ?>" class="form-control" value="<?= htmlspecialchars($options[$i] ?? '') ?>" required>
                        </div>
                        <?php endfor; ?>
                    <?php else: ?>
                        <div class="mb-4">
                            <label class="text-muted small mb-2">ANSWER TEXT</label>
                            <textarea name="answer_text" class="form-control" rows="6" required><?= htmlspecialchars($data['answer_text']) ?></textarea>
                        </div>
                    <?php endif; ?>

                    <div class="d-flex gap-2">
                        <a href="revision_info.php" class="btn btn-outline-secondary w-50 py-2">Cancel</a>
                        <button type="submit" class="btn btn-save w-50 text-white">Update Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>