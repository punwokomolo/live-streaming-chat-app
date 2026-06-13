<?php
session_start();
include "config/db.php";
if(!isset($_SESSION['user_id'])) die();
$user_id = $_SESSION['user_id'];
$stream_id = $_POST['stream_id'];
$message = trim($_POST['message']);
if($message === "") die();
$stmt = $conn->prepare("INSERT INTO stream_messages (stream_id, user_id, message) VALUES (?, ?, ?)");
$stmt->bind_param("iis", $stream_id, $user_id, $message);
$stmt->execute();
?>