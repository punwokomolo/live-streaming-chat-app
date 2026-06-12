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

<div class="register-container">

    <h1>Create Account</h1>

    <form action="" method="POST" class="register-form">

        <input
        type="text"
        name="fullname"
        placeholder="Full Name"
        required>

        <input
        type="email"
        name="email"
        placeholder="Email Address"
        required>

        <input
        type="password"
        name="password"
        placeholder="Password"
        required>

        <button
        type="submit"
        class="register-btn">
            Register
        </button>

    </form>

    <div class="login-link">
        Already have an account?
        <a href="login.php">Login</a>
    </div>

</div>

</body>
</html>
