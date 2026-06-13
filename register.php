<?php

include "config/db.php";

if($_SERVER["REQUEST_METHOD"]=="POST"){

$name=$_POST["fullname"];
$email=$_POST["email"];
$password=password_hash(
$_POST["password"],
PASSWORD_DEFAULT
);

$sql="INSERT INTO users
(fullname,email,password)
VALUES
('$name','$email','$password')";

$conn->query($sql);

echo "Registration Successful";

}
?>


<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="auth-container">

    <h1>Register</h1>

    <form method="POST">

        <input type="text" name="fullname" placeholder="Full Name" required>

        <input type="email" name="email" placeholder="Email" required>

        <input type="password" name="password" placeholder="Password" required>

        <button type="submit">Register</button>

    </form>

    <div class="auth-link">
        <a href="login.php">Back to login</a>
    </div>

</div>
</body>
</html>
