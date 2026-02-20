<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include 'db.php';

if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }
$user_id = $_SESSION['user_id'];

$u_res = mysqli_query($conn, "SELECT current_room_id FROM users WHERE id = '$user_id'");
$u_data = mysqli_fetch_assoc($u_res);
$room_id = $u_data['current_room_id'];

if (!$room_id) { header("Location: battle.php"); exit(); }

$room_res = mysqli_query($conn, "SELECT room_name FROM battle_rooms WHERE id = '$room_id'");
$room = mysqli_fetch_assoc($room_res);
$questions_query = mysqli_query($conn, "SELECT * FROM room_questions WHERE room_id = '$room_id'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Live Paper | <?= htmlspecialchars($room['room_name']) ?></title>
    
    <link href="https://fonts.googleapis.com/css2?family=Crimson+Pro:wght@400;600;700&family=Plus+Jakarta+Sans:wght@700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --paper-bg: #fdfaf3; /* ඇසට පහසු Creamy පැහැය */
            --ink-color: #003399; /* පෑනක නිල් පැහැය */
            --border-color: #333;
        }

        body { 
            background: #cbd5e1; 
            font-family: 'Crimson Pro', serif; /* Realistic Exam Font */
            padding: 40px 10px;
        }

        /* සැබෑ කඩදාසියක පෙනුම */
        .paper-outer {
            background: var(--paper-bg);
            max-width: 850px;
            margin: 0 auto;
            padding: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            position: relative;
        }

        .paper-inner {
            border: 3px double var(--border-color); /* Double line border */
            padding: 50px 45px;
            min-height: 1000px;
        }

        .paper-header {
            text-align: center;
            border-bottom: 2px solid var(--border-color);
            margin-bottom: 40px;
            padding-bottom: 20px;
        }

        .header-title {
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 2rem;
        }

        .meta-data {
            display: flex;
            justify-content: space-between;
            font-weight: 700;
            font-size: 0.9rem;
            margin-top: 15px;
            text-transform: uppercase;
        }

        .question-block {
            margin-bottom: 35px;
            border-bottom: 1px dotted #ccc;
            padding-bottom: 20px;
        }

        .q-text {
            font-weight: 700;
            font-size: 1.3rem;
            margin-bottom: 15px;
            line-height: 1.4;
        }

        /* Radio Buttons සඟවන්න */
        .answer-input {
            display: none !important;
        }

        .options-list {
            display: grid;
            grid-template-columns: 1fr 1fr; /* පේළියට දෙක බැගින් */
            gap: 15px;
            padding-left: 20px;
        }

        .option-label {
            cursor: pointer;
            font-size: 1.2rem;
            position: relative;
            display: flex;
            align-items: center;
        }

        .option-content {
            position: relative;
            padding-bottom: 2px;
            transition: color 0.3s ease;
        }

        /* පිළිතුර ක්ලික් කළ විට ඇඳෙන ඉර (Hand-drawn effect) */
        .option-content::after {
            content: "";
            position: absolute;
            left: 0;
            bottom: 0;
            width: 0;
            height: 3px;
            background: var(--ink-color);
            border-radius: 40% 60% 45% 55% / 45% 45% 55% 55%; /* අතින් ඇන්දා වැනි හැඩය */
            transition: width 0.35s ease-out;
            opacity: 0.8;
        }

        /* Click කළ විට ඉර 100% දිග වීම */
        .answer-input:checked + .option-content::after {
            width: 100%;
        }

        .answer-input:checked + .option-content {
            color: var(--ink-color);
            font-weight: 700;
        }

        .btn-submit {
            background: transparent;
            color: var(--border-color);
            border: 2px solid var(--border-color);
            padding: 12px 50px;
            font-weight: 800;
            text-transform: uppercase;
            font-family: 'Plus Jakarta Sans', sans-serif;
            transition: 0.3s;
            margin-top: 30px;
        }

        .btn-submit:hover {
            background: var(--border-color);
            color: #fff;
        }

        @media (max-width: 768px) {
            .options-list { grid-template-columns: 1fr; }
            .paper-inner { padding: 30px 20px; }
            .header-title { font-size: 1.5rem; }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="paper-outer">
        <div class="paper-inner">
            
            <div class="paper-header">
                <div class="header-title"><?= htmlspecialchars($room['room_name']) ?></div>
                <p class="mb-0 small">BATTLE ARENA • OFFICIAL QUESTION PAPER</p>
                <div class="meta-data border-top pt-3">
                    <span>INDEX NO: #<?= str_pad($user_id, 5, '0', STR_PAD_LEFT) ?></span>
                    <span>SUBJECT: MCQ BATTLE</span>
                    <span>DATE: <?= date('Y.m.d') ?></span>
                </div>
            </div>

            <form action="submit_paper.php" method="POST">
                <?php 
                $count = 1;
                while($q = mysqli_fetch_assoc($questions_query)): 
                    $q_id = $q['id'];
                ?>
                <div class="question-block">
                    <div class="q-text">
                        <span class="me-2"><?= $count ?>.</span>
                        <?= htmlspecialchars($q['question_text']) ?>
                    </div>
                    
                    <div class="options-list">
                        <?php 
                        $opt_query = mysqli_query($conn, "SELECT * FROM room_question_options WHERE question_id = '$q_id'");
                        $i = 1;
                        while($opt = mysqli_fetch_assoc($opt_query)):
                        ?>
                        <label class="option-label">
                            <input type="radio" name="q_<?= $q_id ?>" value="<?= $opt['id'] ?>" class="answer-input" required>
                            <span class="option-content">
                                <span class="me-2 fw-bold">(<?= $i ?>)</span>
                                <?= htmlspecialchars($opt['option_text']) ?>
                            </span>
                        </label>
                        <?php $i++; endwhile; ?>
                    </div>
                </div>
                <?php $count++; endwhile; ?>

                <div class="text-center">
                    <button type="submit" class="btn btn-submit">Submit Answers</button>
                    <div class="mt-4 text-muted small" style="font-style: italic; opacity: 0.7;">
                        --- End of Question Paper ---
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>

    
    
    <script>
let tabSwitchCount = 0;

document.addEventListener('visibilitychange', function() {
    if (document.hidden) {
        tabSwitchCount++;
        if(tabSwitchCount <= 2) {
            alert("අවවාදයයි! විභාගය අතරතුර වෙනත් Tab වලට යාම තහනම්. ඔබ තවත් " + (3 - tabSwitchCount) + " වතාවක් මෙසේ කළහොත් පේපර් එක ඉබේම Submit වනු ඇත.");
        } else {
            alert("ඔබ නීති උල්ලංඝනය කළ බැවින් පේපර් එක ස්වයංක්‍රීයව Submit වේ!");
            document.querySelector('form').submit(); // පේපර් එක auto submit කිරීම
        }
    }
});
</script>
</body>
</html>