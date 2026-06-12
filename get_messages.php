<?php

session_start();
include "config/db.php";

if (!isset($_SESSION['user_id'])) {
    exit("User not logged in");
}

if (!isset($_GET['receiver'])) {
    exit("Receiver missing");
}

$currentUser = $_SESSION['user_id'];
$receiver = $_GET['receiver'];

$sql = "
SELECT *
FROM messages

WHERE
(sender_id='$currentUser' AND receiver_id='$receiver')

OR

(sender_id='$receiver' AND receiver_id='$currentUser')

ORDER BY sent_at ASC
";

$result = $conn->query($sql);

while($msg = $result->fetch_assoc()){

    if($msg['sender_id'] == $currentUser){

        echo "
        <div class='my-message'>
            <strong>You:</strong>
            ".$msg['message']."
        </div>
        ";

    }else{

        echo "
        <div class='other-message'>
            ".$msg['message']."
        </div>
        ";

    }

}
?>