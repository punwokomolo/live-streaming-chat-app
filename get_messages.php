<?php
session_start();
include "config/db.php";

if (!isset($_SESSION['user_id'])) {
    exit("User not logged in");
}

if (!isset($_GET['receiver'])) {
    exit("Receiver missing");
}

$currentUser = $_SESSION['user_id'];
$receiver = $_GET['receiver'];

$stmt = $conn->prepare("
    SELECT * FROM messages
    WHERE (sender_id = ? AND receiver_id = ?)
       OR (sender_id = ? AND receiver_id = ?)
    ORDER BY sent_at ASC
");
$stmt->bind_param("iiii", $currentUser, $receiver, $receiver, $currentUser);
$stmt->execute();
$result = $stmt->get_result();

if (!$result) {
    echo "DB Error: " . $conn->error;
    exit;
}

while ($msg = $result->fetch_assoc()) {
    $time = date("h:i A", strtotime($msg['sent_at']));
    if ($msg['sender_id'] == $currentUser) {
        echo "
        <div class='my-message'>
            <div class='bubble mine'>
                ".htmlspecialchars($msg['message'])."
                <br><small>$time</small>
            </div>
        </div>";
    } else {
        echo "
        <div class='other-message'>
            <div class='bubble theirs'>
                ".htmlspecialchars($msg['message'])."
                <br><small>$time</small>
            </div>
        </div>";
    }
}
?>