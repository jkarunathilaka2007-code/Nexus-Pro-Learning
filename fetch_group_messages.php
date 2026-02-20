<?php
include 'db.php';
session_start();

$room_id = (int)$_GET['room_id'];
$user_id = $_SESSION['user_id'];

// මෙන්න මේ Query එක තමයි අඩුවෙලා තිබුණේ මචං
$query = "SELECT gm.*, u.name FROM study_group_messages gm 
          JOIN users u ON gm.user_id = u.id 
          WHERE gm.room_id = '$room_id' ORDER BY gm.created_at ASC";

$res = mysqli_query($conn, $query);

if (!$res) {
    die("Query Failed: " . mysqli_error($conn));
}

while($m = mysqli_fetch_assoc($res)) {
    $isMe = ($m['user_id'] == $user_id);
    $msgText = htmlspecialchars($m['message']);
    
    echo '<div class="msg-bubble '.($isMe ? 'msg-me' : 'msg-other').' mb-2 shadow-sm">';
    if(!$isMe) echo '<span class="sender-tag" style="color:#00d4ff; font-size:10px; font-weight:bold;">'.$m['name'].'</span>';

    // 1. POLL PARSER
    if (strpos($msgText, '[POLL:') !== false) {
        preg_match('/\[POLL:(\d+)\]/', $msgText, $matches);
        $pollId = $matches[1];

        $poll_data = mysqli_query($conn, "SELECT * FROM study_polls WHERE id = '$pollId'");
        $p = mysqli_fetch_assoc($poll_data);

        if($p) {
            echo '<div class="poll-card p-2 text-white" style="min-width: 220px; background: rgba(13, 202, 240, 0.1); border-radius: 10px; border: 1px solid #0dcaf0;">';
            echo '<h6 class="fw-bold mb-2 pb-1 border-bottom border-info text-info"><i class="fa fa-chart-simple me-2"></i>' . htmlspecialchars($p['question']) . '</h6>';

            $opts = [
                'a' => [$p['option_a'], $p['votes_a']],
                'b' => [$p['option_b'], $p['votes_b']],
                'c' => [$p['option_c'], $p['votes_c']],
                'd' => [$p['option_d'], $p['votes_d']],
                'e' => [$p['option_e'], $p['votes_e']]
            ];

            foreach($opts as $key => $data) {
                if(!empty($data[0])) {
                    echo '<button class="btn btn-sm btn-outline-info w-100 mb-1 rounded-pill text-start d-flex justify-content-between align-items-center" onclick="vote('.$pollId.', \''.$key.'\')" style="font-size: 12px;">
                            <span>'.htmlspecialchars($data[0]).'</span>
                            <span class="badge bg-info text-dark">'.$data[1].'</span>
                          </button>';
                }
            }
            echo '</div>';
        }
    } 
    // 2. NOTE SHARE PARSER
    else if (strpos($msgText, '[NOTE_SHARE:') !== false) {
        preg_match('/\[NOTE_SHARE:(\d+)\] (.*)/', $msgText, $matches);
        $noteId = $matches[1];
        $noteTitle = $matches[2];

        echo '<div class="shared-note-card p-2 border border-warning rounded" style="background: rgba(255, 193, 7, 0.05);">';
        echo '<small class="text-warning fw-bold">📚 SHARED NOTE</small><br>';
        echo '<span class="small">' . htmlspecialchars($noteTitle) . '</span>';
        if (!$isMe) {
            echo '<br><button class="btn btn-sm btn-warning mt-2 rounded-pill fw-bold w-100" style="font-size:10px;" onclick="useNote('.$noteId.')">GET NOTE</button>';
        }
        echo '</div>';
    } 
    // 3. NORMAL TEXT
    else {
        echo $msgText;
    }
    
    echo '</div>';
}
?>