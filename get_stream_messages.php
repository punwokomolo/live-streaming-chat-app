<?php
session_start();
include "config/db.php";
$stream_id = $_GET['stream_id'];
$stmt = $conn->prepare("
    SELECT m.*, u.fullname 
    FROM stream_messages m 
    JOIN users u ON m.user_id = u.id 
    WHERE m.stream_id = ? 
    ORDER BY m.sent_at ASC
");
$stmt->bind_param("i", $stream_id);
$stmt->execute();
$result = $stmt->get_result();

while($msg = $result->fetch_assoc()) {
    $isMine = ($msg['user_id'] == $_SESSION['user_id']);
    $class = $isMine ? 'my-message' : 'other-message';
    $bubble = $isMine ? 'mine' : 'theirs';
    echo "<div class='$class'>
            <div class='bubble $bubble'>
                <strong>" . htmlspecialchars($msg['fullname']) . "</strong><br>
                " . htmlspecialchars($msg['message']) . "<br>
                <small>" . date("h:i A", strtotime($msg['sent_at'])) . "</small>
            </div>
          </div>";
}
?>