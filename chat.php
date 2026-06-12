<?php

session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

include "config/db.php";

$currentUser = $_SESSION['user_id'];

$receiver = $_GET['user'];

$sql = "SELECT * FROM users WHERE id='$receiver'";
$result = $conn->query($sql);

$receiverUser = $result->fetch_assoc();

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

    <form class="message-form"
          action="send_message.php"
          method="POST">

    <input
        type="hidden"
        name="receiver_id"
        value="<?php echo $receiver; ?>">

    <input
        type="text"
        name="message"
        placeholder="Type a message..."
        >

    <button >
        Send
    </button>

  </form>
</div>
<script>

function loadMessages(){

    let receiver = <?php echo $receiver; ?>;

    console.log(receiver);

    fetch("get_messages.php?receiver=" + receiver)
    .then(response => response.text())
    .then(data => {

        document.getElementById("chat-box").innerHTML = data;

    });

}

loadMessages();

setInterval(loadMessages, 2000);

</script>
</body>
</html>