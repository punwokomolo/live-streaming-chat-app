<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
include "config/db.php";

$currentUser = (int)$_SESSION['user_id'];

$stmt = $conn->prepare("SELECT fullname FROM users WHERE id = ?");
$stmt->bind_param("i", $currentUser);
$stmt->execute();
$myName = $stmt->get_result()->fetch_assoc()['fullname'];

// Get all users except current
$otherUsers = $conn->query("SELECT id, fullname FROM users WHERE id != $currentUser");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="dashboard">
    <div class="header">
        <h2>Welcome, <?php echo htmlspecialchars($myName); ?> (Your ID: <?php echo $currentUser; ?>)</h2>
        <a href="logout.php"><button>Logout</button></a>
    </div>
    <div class="users-panel">
        <h3>Other Users</h3>
        <?php if ($otherUsers->num_rows == 0): ?>
            <p>No other users. <a href="register.php">Register another account</a></p>
        <?php else: ?>
            <?php while ($user = $otherUsers->fetch_assoc()): ?>
                <div class="user-card">
                    <strong><?php echo htmlspecialchars($user['fullname']); ?></strong> 
                    (ID: <?php echo $user['id']; ?>)
                    <a href="chat.php?user=<?php echo $user['id']; ?>" class="chat-btn">Chat</a>
                </div>
            <?php endwhile; ?>
        <?php endif; ?>
    </div>
    <div style="margin-top: 20px;">
        <a href="start_stream.php">🎥 Go Live</a> | 
        <a href="join_stream.php">👀 Join a Stream</a>
    </div>
<div style="margin-top: 20px;">
    <a href="start_stream.php" style="background:green; color:white; padding:10px 20px; text-decoration:none; border-radius:8px;">🎥 Go Live</a>
    <a href="join_stream.php" style="background:orange; color:white; padding:10px 20px; text-decoration:none; border-radius:8px;">👀 Join a Stream</a>
</div>

</div>
</body>
</html>