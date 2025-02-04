<?php
session_start();

// If the user is not logged in, redirect to the login page
if (!isset($_SESSION['email'])) {
    header("Location: login.html"); // Redirect to  page
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome</title>
    <link rel="stylesheet" href="welcome.css">
</head>
<body>
    <div class="welcome-message">
        <h2>Hello, <?php echo $_SESSION['name']; ?>! Welcome to your Dashboard.</h2>
        <a href="login.html" class ="index-btn">Explore</a>
    </div>
</body>
</html>
