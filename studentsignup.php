<?php
session_start(); // Start session to handle login state

// Database connection details
$servername = "localhost";
$username = "root"; // replace with your database username
$password = ""; // replace with your database password
$dbname = "cse_department";

// Create a connection to the MySQL database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check if the connection was successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form is submitted via POST method
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the form data from the POST request
    $name = $_POST['name'];
    $contact_number = $_POST['contact_number'];
    $email = $_POST['email'];
    $dob = $_POST['dob'];
    $aktu_roll_no = $_POST['aktu_roll_no'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password for security
    $year = $_POST['year'];
    $semester = $_POST['semester'];

    // Prepare the SQL query to insert data into the student_signup table
    $sql = "INSERT INTO student_signup (name, contact_number, email, date_of_birth, aktu_roll_no, password, year, semester)
            VALUES ('$name', '$contact_number', '$email', '$dob', '$aktu_roll_no', '$password', '$year', '$semester')";

    // Execute the query and check if it was successful
    if ($conn->query($sql) === TRUE) {
        // On successful sign-up, set session variables for login
        $_SESSION['email'] = $email;
        $_SESSION['name'] = $name;  // You can store name for the welcome message
        
        // Redirect to the welcome page
        header("Location: welcome.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Close the database connection
$conn->close();
?>
