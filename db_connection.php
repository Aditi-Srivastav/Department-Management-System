<?php
$servername = "localhost";  // Database host (e.g., localhost)
$username = "root";         // Database username
$password = "";             // Database password (empty by default for XAMPP)
$dbname = "CSE_Department"; // Database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
