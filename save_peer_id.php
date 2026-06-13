<?php
session_start();
header('Content-Type: text/plain');
include "config/db.php";

if (!isset($_POST['stream_id']) || !isset($_POST['peer_id'])) {
    echo "Missing data";
    exit();
}

$stream_id = (int)$_POST['stream_id'];
$peer_id = $_POST['peer_id'];

$stmt = $conn->prepare("REPLACE INTO stream_peers (stream_id, peer_id) VALUES (?, ?)");
$stmt->bind_param("is", $stream_id, $peer_id);
if ($stmt->execute()) {
    echo "OK";
} else {
    echo "DB error: " . $stmt->error;
}
?>