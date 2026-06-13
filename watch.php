<?php
session_start();
if(!isset($_SESSION['user_id'])){ header("Location: login.php"); exit(); }
include "config/db.php";
$user_id = $_SESSION['user_id'];

$room_code = $_GET['code'] ?? '';
if(!$room_code) die("No room code provided.");
$stmt = $conn->prepare("SELECT id, streamer_id FROM streams WHERE room_code = ? AND is_active = 1");
$stmt->bind_param("s", $room_code);
$stmt->execute();
$stream = $stmt->get_result()->fetch_assoc();
if(!$stream) die("Stream not found or ended.");
$stream_id = $stream['id'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Watching Live</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/peerjs/1.5.2/peerjs.min.js"></script>
</head>
<body>
<div class="stream-container">
    <div class="video-section">
        <div id="videoPlaceholder" style="background:#000; color:white; display:flex; align-items:center; justify-content:center; height:400px;">
            Attempting video connection...<br>
            (Chat is active)
        </div>
        <video id="remoteVideo" autoplay playsinline style="width:100%; display:none;"></video>
        <div class="room-info">👀 Watching: <?php echo htmlspecialchars($room_code); ?></div>
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

async function connectToBroadcaster() {
    try {
        const res = await fetch("get_broadcaster_peer.php?stream_id=" + streamId);
        const data = await res.json();
        if (data.peer_id) {
            console.log("Broadcaster peer ID:", data.peer_id);
            // Safely create peer
            let peer;
            try {
                peer = new Peer();
                if (!peer) throw new Error("Peer creation failed");
            } catch(e) {
                console.error("Peer constructor error:", e);
                showVideoFailed("PeerJS initialization failed.");
                return;
            }
            peer.on('open', () => {
                console.log("Viewer peer ready");
                const call = peer.call(data.peer_id, null);
                call.on('stream', remoteStream => {
                    // Hide placeholder, show video
                    document.getElementById("videoPlaceholder").style.display = "none";
                    const vid = document.getElementById("remoteVideo");
                    vid.style.display = "block";
                    vid.srcObject = remoteStream;
                });
                call.on('error', err => {
                    console.error("Call error:", err);
                    showVideoFailed("Call error: " + err.message);
                });
            });
            peer.on('error', err => {
                console.error("Peer error:", err);
                showVideoFailed("Peer connection lost. Chat remains active.");
            });
        } else {
            showVideoFailed("Broadcaster not ready. Chat still works.");
        }
    } catch(err) {
        console.error("Connection error:", err);
        showVideoFailed("Failed to connect video. Chat is active.");
    }
}

function showVideoFailed(msg) {
    const placeholder = document.getElementById("videoPlaceholder");
    if (placeholder) placeholder.innerHTML = "⚠️ " + msg + "<br>📢 Live chat is working normally.";
}

connectToBroadcaster();

// Chat functions (unchanged)
function loadMessages() {
    fetch("get_stream_messages.php?stream_id=" + streamId)
        .then(res => res.text())
        .then(data => {
            document.getElementById("chatBox").innerHTML = data;
            document.getElementById("chatBox").scrollTop = document.getElementById("chatBox").scrollHeight;
        })
        .catch(err => console.error("Load messages error:", err));
}
function sendMessage(msg) {
    if(!msg.trim()) return;
    fetch("send_stream_message.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "stream_id=" + streamId + "&message=" + encodeURIComponent(msg)
    })
    .then(() => loadMessages())
    .catch(err => console.error("Send error:", err));
}
document.getElementById("sendBtn").onclick = () => {
    sendMessage(document.getElementById("chatMessage").value);
    document.getElementById("chatMessage").value = "";
};
loadMessages();
setInterval(loadMessages, 2000);
</script>
</body>
</html>