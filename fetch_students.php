<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cse_department";

$year = $_GET['year']; // Get the selected year from the request

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch students based on the selected year
$sql = "SELECT name, aktu_roll_no FROM student_signup WHERE year = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $year); // 's' is for string (year is a string in the database)
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo '<div>';
        echo '<label>' . $row['name'] . ' (' . $row['aktu_roll_no'] . ')</label>';
        echo '<input type="checkbox" name="attendance[' . $row['aktu_roll_no'] . ']" value="present"> Present';
        echo '<input type="checkbox" name="attendance[' . $row['aktu_roll_no'] . ']" value="absent"> Absent';
        echo '</div>';
    }
} else {
    echo "No students found for this year.";
}

$stmt->close();
$conn->close();
?>
