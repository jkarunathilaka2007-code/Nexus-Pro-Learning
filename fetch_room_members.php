<?php
include 'db.php';
$room_id = (int)$_GET['room_id'];

// දැනට room එකේ ඉන්න අය fetch කරනවා
$query = "SELECT u.name, u.profile_image FROM room_members rm 
          JOIN users u ON rm.user_id = u.id 
          WHERE rm.room_id = '$room_id' 
          ORDER BY rm.joined_at DESC";

$res = mysqli_query($conn, $query);

while($row = mysqli_fetch_assoc($res)) {
    echo '<div class="member-card">
            <img src="'.($row['profile_image'] ?: 'default.png').'" width="35" height="35" class="rounded-circle border border-primary border-opacity-50">
            <div class="small fw-bold text-truncate">'.$row['name'].'</div>
            <div class="ms-auto"><span class="status-online" style="width:8px; height:8px; background:#00ff88; border-radius:50%; display:inline-block;"></span></div>
          </div>';
}
?>