<?php
session_start();
if(!isset($_SESSION['user_id'])){ header("Location: login.php"); exit(); }
include "config/db.php";
$user_id = $_SESSION['user_id'];

$room_code = substr(str_shuffle("ABCDEFGHJKLMNPQRSTUVWXYZ0123456789"), 0, 6);
$stmt = $conn->prepare("INSERT INTO streams (streamer_id, room_code) VALUES (?, ?)");
$stmt->bind_param("is", $user_id, $room_code);
$stmt->execute();
$stream_id = $conn->insert_id;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Live Broadcast</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/peerjs/1.5.2/peerjs.min.js"></script>
</head>
<body>
<div class="stream-container">
    <div class="video-section">
        <video id="localVideo" autoplay muted playsinline style="width:100%; background:#000;"></video>
        <div class="room-info">🔴 LIVE | Code: <strong><?php echo $room_code; ?></strong> <button onclick="copyCode()">Copy</button></div>
    </div>
    <div class="chat-section">
        <div class="chat-header">💬 Live Chat</div>
        <div id="chatBox" style="height:400px; overflow-y:auto; padding:10px; background:#f8fafc;"></div>
        <div style="padding:10px; display:flex; gap:5px;">
            <input type="text" id="chatMessage" placeholder="Type a message..." style="flex:1; padding:8px;">
            <button id="sendBtn">Send</button>
        </div>
    </div>
</div>
<script>
const streamId = <?php echo $stream_id; ?>;
const roomCode = "<?php echo $room_code; ?>";

let peer = null;
let localStream = null;

navigator.mediaDevices.getUserMedia({ video: true, audio: true })
    .then(stream => {
        localStream = stream;
        document.getElementById("localVideo").srcObject = stream;
        console.log("Camera ready");
        initPeer();
    })
    .catch(err => {
        console.error("Camera error:", err);
        document.getElementById("localVideo").parentElement.innerHTML += "<p style='color:red'>Camera/mic access denied.</p>";
    });

function initPeer() {
    peer = new Peer();
    peer.on('open', id => {
        console.log("Broadcaster peer ID:", id);
        fetch("save_peer_id.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: "stream_id=" + streamId + "&peer_id=" + id
        });
    });
    peer.on('call', call => {
        if (localStream) {
            call.answer(localStream);
            console.log("Answered call");
        }
    });
    peer.on('error', err => console.error("Peer error:", err));
    peer.on('disconnected', () => {
        console.log("Disconnected, reconnecting...");
        setTimeout(() => peer.reconnect(), 1000);
    });
}

// Chat functions
function loadMessages() {
    fetch("get_stream_messages.php?stream_id=" + streamId)
        .then(res => res.text())
        .then(data => {
            document.getElementById("chatBox").innerHTML = data;
            document.getElementById("chatBox").scrollTop = document.getElementById("chatBox").scrollHeight;
        });
}
function sendMessage(msg) {
    if(!msg.trim()) return;
    fetch("send_stream_message.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "stream_id=" + streamId + "&message=" + encodeURIComponent(msg)
    }).then(() => loadMessages());
}
document.getElementById("sendBtn").onclick = () => {
    sendMessage(document.getElementById("chatMessage").value);
    document.getElementById("chatMessage").value = "";
};
loadMessages();
setInterval(loadMessages, 2000);
function copyCode() { navigator.clipboard.writeText(roomCode); alert("Code copied!"); }
</script>
</body>
</html>