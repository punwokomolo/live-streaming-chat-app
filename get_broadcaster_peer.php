<?php
header('Content-Type: application/json');
error_reporting(0); // Turn off HTML errors that break JSON

include "config/db.php";

if (!isset($_GET['stream_id'])) {
    echo json_encode(['peer_id' => null, 'error' => 'No stream_id']);
    exit();
}

$stream_id = (int)$_GET['stream_id'];

$result = $conn->query("SELECT peer_id FROM stream_peers WHERE stream_id = $stream_id");
if ($result && $row = $result->fetch_assoc()) {
    echo json_encode(['peer_id' => $row['peer_id']]);
} else {
    echo json_encode(['peer_id' => null, 'error' => 'No peer found']);
}
?>