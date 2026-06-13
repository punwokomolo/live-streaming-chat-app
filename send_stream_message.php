<?php
session_start();
include "config/db.php";

if (!isset($_SESSION['user_id'])) {
    die("Not logged in");
}
if (!isset($_POST['receiver_id']) || !isset($_POST['message'])) {
    die("Missing data");
}

$currentUser = (int)$_SESSION['user_id'];
$receiver = (int)$_POST['receiver_id'];
$message = trim($_POST['message']);

if ($currentUser == $receiver) {
    die("Error: Cannot send to yourself.");
}
if (empty($message)) {
    die("Message cannot be empty.");
}

$stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
$stmt->bind_param("iis", $currentUser, $receiver, $message);
if ($stmt->execute()) {
    header("Location: chat.php?user=$receiver");
    exit();
} else {
    echo "DB Error: " . $stmt->error;
}
?>