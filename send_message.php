<?php

session_start();
include "config/db.php";

$currentUser = $_SESSION['user_id'];

$receiver = $_POST['receiver_id'];

$message = $_POST['message'];

$sql = "INSERT INTO messages
(sender_id, receiver_id, message)
VALUES
('$currentUser','$receiver','$message')";

$conn->query($sql);

header("Location: chat.php?user=".$receiver);

?>