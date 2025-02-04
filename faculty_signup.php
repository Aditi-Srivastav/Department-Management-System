<?php
// Database connection
$servername = "localhost";
$username = "root"; // Replace with your database username
$password = ""; // Replace with your database password
$dbname = "cse_department";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("<div class='error-message'>Connection failed: " . $conn->connect_error . "</div>");
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password
    $contact_number = $_POST['contact_number']; // Capture contact number
    $subjects = $_POST['subjects']; // Get the array of selected subjects

    // Prepare SQL statement to insert data into Faculty table
    $sql = "INSERT INTO Faculty (name, email, password, contact_number) 
            VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    // Check if the statement was prepared successfully
    if ($stmt === false) {
        die("<div class='error-message'>Error in preparing statement: " . $conn->error . "</div>");
    }

    // Bind parameters to the statement
    $stmt->bind_param("ssss", $name, $email, $password, $contact_number);

    // Execute the statement
    if ($stmt->execute()) {
        // Get the last inserted Faculty ID
        $faculty_id = $stmt->insert_id;

        // Insert each subject as a separate row in the 'Faculty_Subjects' table
        $subject_stmt = $conn->prepare("INSERT INTO Faculty_Subjects (faculty_id, subject) VALUES (?, ?)");

        foreach ($subjects as $subject) {
            $subject_stmt->bind_param("is", $faculty_id, $subject);
            $subject_stmt->execute();
        }

        // Redirect to the login page after successful signup
        header("Location: faculty_login.php");
        exit(); // Stop further script execution
    } else {
        echo "<div class='error-message'>Error: " . $stmt->error . "</div>";
    }

    // Close the statement and the connection
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty Sign-Up</title>
    <style>
        body {
    font-family: Arial, sans-serif;
    background: linear-gradient(135deg, #3b4cca, #0a1f55); /* Blue to Dark Blue Gradient */
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    color: #fff; /* Text color for better contrast */
}

.container {
    background: rgba(255, 255, 255, 0.9); /* Slightly transparent background */
    padding: 30px 40px;
    border-radius: 10px;
    box-shadow: 0 15px 25px rgba(0, 0, 0, 0.1);
    max-width: 450px;
    width: 100%;
    backdrop-filter: blur(10px); /* Adds a blurred effect behind the container */
}

h2 {
    text-align: center;
    color: #023e8a; /* Dark blue color for the heading */
    margin-bottom: 25px;
    font-size: 24px;
}

label {
    display: block;
    margin: 10px 0 5px;
    color: #333;
    font-weight: 600;
}

input, select {
    width: calc(100% - 10px);
    padding: 12px 10px;
    margin-bottom: 15px;
    border: 1px solid #ccc;
    border-radius: 6px;
    font-size: 16px;
}

select {
    background-color: #fff;
    background-image: url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTYiIGhlaWdodD0iMTYiIHZpZXdCb3g9IjAgMCAxNiAxNiIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHBhdGggZD0iTTMuNSA2LjUwMDAwMUw4IDExLjUwMDAwMUwxMi41IDYuNTAwMDAxIiBzdHJva2U9IiMyMDIwMjAiIHN0cm9rZS13aWR0aD0iMiIgc3Ryb2tlLWxpbmVjYXAtcm91bmQ9InJvdW5kIi8+Cjwvc3ZnPgo='); 
    background-repeat: no-repeat;
    background-position: right 10px center;
}

button {
    width: 100%;
    padding: 12px;
    background: #023e8a;
    color: #fff;
    border: none;
    border-radius: 6px;
    font-size: 16px;
    cursor: pointer;
    transition: background-color 0.3s ease-in-out;
}

button:hover {
    background-color: #0077b6;
}

.error-message {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
    padding: 10px;
    border-radius: 6px;
    margin-bottom: 15px;
    font-size: 14px;
}

.subject-dropdown {
    display: none;
    margin-bottom: 15px;
}

.subject-dropdown label {
    display: block;
    margin: 5px 0;
}

.dropdown-btn {
    background-color: #007bff;
    color: white;
    padding: 12px;
    border: none;
    width: 100%;
    cursor: pointer;
    border-radius: 6px;
    text-align: left;
    font-size: 16px;
    transition: background-color 0.3s ease-in-out;
}

.dropdown-btn:hover {
    background-color: #0056b3;
}

.dropdown-btn:focus {
    outline: none;
}

@media (max-width: 480px) {
    .container {
        padding: 20px;
        width: 100%;
    }

    button {
        padding: 10px;
    }

    .dropdown-btn {
        font-size: 14px;
    }
}

    </style>
</head>
<body>
    <div class="container">
        <h2>Faculty Sign-Up</h2>
        <form action="faculty_signup.php" method="POST">
            <label for="name">Full Name:</label>
            <input type="text" id="name" name="name" required>

            <label for="contact_number">Contact Number:</label>
            <input type="text" id="contact_number" name="contact_number" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <label for="subjects">Select Subjects:</label>
            <button type="button" class="dropdown-btn" onclick="toggleDropdown()">Select Subjects</button>
            <div class="subject-dropdown" id="subject-dropdown">
                <label>
                    <input type="checkbox" name="subjects[]" value="Programming in C"> Programming in C
                </label>
                <label>
                    <input type="checkbox" name="subjects[]" value="Data Structures"> Data Structures
                </label>
                <label>
                    <input type="checkbox" name="subjects[]" value="Algorithms"> Algorithms
                </label>
                <label>
                    <input type="checkbox" name="subjects[]" value="Operating Systems"> Operating Systems
                </label>
                <label>
                    <input type="checkbox" name="subjects[]" value="Database Management Systems"> Database Management Systems
                </label>
                <label>
                    <input type="checkbox" name="subjects[]" value="Computer Networks"> Computer Networks
                </label>
                <label>
                    <input type="checkbox" name="subjects[]" value="Artificial Intelligence"> Artificial Intelligence
                </label>
                <label>
                    <input type="checkbox" name="subjects[]" value="Machine Learning"> Machine Learning
                </label>
                <label>
                    <input type="checkbox" name="subjects[]" value="Web Development"> Web Development
                </label>
                <label>
                    <input type="checkbox" name="subjects[]" value="Cyber Security"> Cyber Security
                </label>
            </div>

            <button type="submit">Sign Up</button>
        </form>
    </div>

    <script>
        function toggleDropdown() {
            var dropdown = document.getElementById("subject-dropdown");
            if (dropdown.style.display === "none" || dropdown.style.display === "") {
                dropdown.style.display = "block";
            } else {
                dropdown.style.display = "none";
            }
        }
    </script>
</body>
</html>
