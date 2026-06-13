<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include "config/db.php";

$currentUser = (int)$_SESSION['user_id'];

if (!isset($_GET['user'])) {
    die("No user selected.");
}

$requested = (int)trim($_GET['user']);

// If user tries to chat with themselves, find any other user
if ($requested == $currentUser) {
    $result = $conn->query("SELECT id FROM users WHERE id != $currentUser LIMIT 1");
    if ($result && $result->num_rows > 0) {
        $other = $result->fetch_assoc();
        $correctReceiver = $other['id'];
        // Redirect to the correct chat page
        header("Location: chat.php?user=$correctReceiver");
        exit();
    } else {
        die("No other users to chat with.");
    }
} else {
    $correctReceiver = $requested;
}

// Verify receiver exists
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $correctReceiver);
$stmt->execute();
$receiverUser = $stmt->get_result()->fetch_assoc();

if (!$receiverUser) {
    die("User not found.");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Chat</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<div class="chat-container">
    <div class="chat-header">
        Chat with <?php echo htmlspecialchars($receiverUser['fullname']); ?>
    </div>

    <div id="chat-box"></div>

    <form class="message-form" action="send_message.php" method="POST">
        <input type="hidden" name="receiver_id" value="<?php echo $correctReceiver; ?>">
        <input type="text" name="message" placeholder="Type a message..." required>
        <button type="submit">Send</button>
    </form>
</div>

<script>
function loadMessages() {
    let receiver = <?php echo $correctReceiver; ?>;
    fetch("get_messages.php?receiver=" + receiver)
        .then(response => response.text())
        .then(data => {
            document.getElementById("chat-box").innerHTML = data;
            let chatBox = document.getElementById("chat-box");
            chatBox.scrollTop = chatBox.scrollHeight;
        })
        .catch(error => console.log("Error loading messages:", error));
}

loadMessages();
setInterval(loadMessages, 2000);
</script>

</body>
</html>