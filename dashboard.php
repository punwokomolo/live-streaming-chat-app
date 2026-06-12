<?php

session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

include "config/db.php";

$userId = $_SESSION['user_id'];

$sql = "SELECT * FROM users WHERE id='$userId'";
$result = $conn->query($sql);
$user = $result->fetch_assoc();
echo "Session User ID: ";
echo $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<div class="dashboard">

    <!-- HEADER START -->
    <div class="header">

        <h2>
            Welcome <?php echo htmlspecialchars($user['fullname']); ?>
        </h2>

        <p>
            <?php echo htmlspecialchars($user['email']); ?>
        </p>

        <a href="logout.php">
            <button>Logout</button>
        </a>

    </div>
    <!-- HEADER END -->

    <div class="content">

        <div class="users-panel">

            <h3>Users</h3>

            <?php

            $currentUser = $_SESSION['user_id'];

            $users = $conn->query(
                "SELECT * FROM users WHERE id != '$currentUser'"
            );

            while($row = $users->fetch_assoc()){

            ?>

            <div class="user-card">
<div>
                <strong>
                    <?php echo htmlspecialchars($row['fullname']); ?>
                </strong>
                </div>

                <a class="chat-btn"
                 href="chat.php?user=<?php echo $row['id']; ?>">
                    <button>Chat</button>
                </a>

            </div>

            <?php } ?>

        </div>

        <div class="stats-panel">

            <h3>Statistics</h3>

            <?php

            $total = $conn->query(
                "SELECT COUNT(*) as total FROM users"
            );

            $count = $total->fetch_assoc();

            ?>

            <p>Total Users: <?php echo $count['total']; ?></p>

        </div>

    </div>

</div>

</body>
</html>