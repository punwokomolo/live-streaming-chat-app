<?php session_start(); if(!isset($_SESSION['user_id'])){ header("Location: login.php"); exit(); } ?>
<!DOCTYPE html>
<html>
<head><title>Join Stream</title><link rel="stylesheet" href="css/style.css"></head>
<body>
<div class="auth-container">
    <h2>Enter Room Code</h2>
    <form method="GET" action="watch.php">
        <input type="text" name="code" placeholder="e.g., A1B2C3" required>
        <button type="submit">Watch</button>
    </form>
</div>
</body>
</html>