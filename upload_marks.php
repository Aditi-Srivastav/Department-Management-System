<?php
// Include the database connection
include('db_connection.php');

// Start session and check if the faculty is logged in
session_start();
if (!isset($_SESSION['faculty_email'])) {
    header('Location: login.php');
    exit();
}

// Fetch the logged-in faculty's email
$faculty_email = $_SESSION['faculty_email'];

// Fetch subjects for the logged-in faculty from the 'faculty_subjects' table
$subjects = [];  // Variable to hold the subjects
$subject_query = "SELECT subject FROM faculty_subjects WHERE faculty_email = '$faculty_email'";
$subject_result = $conn->query($subject_query);

if ($subject_result->num_rows > 0) {
    while ($row = $subject_result->fetch_assoc()) {
        $subjects[] = $row['subject'];
    }
}

// Handle form submission for marks upload
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $selected_year = $_POST['year'];
    $selected_subject = $_POST['subject'];  // Get selected subject from form
    $selected_exam_type = $_POST['exam_type']; // Get selected exam type
    $selected_students = $_POST['students'] ?? [];

    // Validate the form input
    if (empty($selected_year) || empty($selected_subject) || empty($selected_exam_type)) {
        echo "<div class='error'>Please select year, subject, and exam type.</div>";
    } elseif (!empty($selected_students)) {
        foreach ($selected_students as $student_id => $marks) {
            // Fetch aktu_roll_no and year for the student
            $student_query = "SELECT aktu_roll_no, year FROM student_signup WHERE id = '$student_id'";
            $student_result = $conn->query($student_query);

            if ($student_result->num_rows > 0) {
                $student_data = $student_result->fetch_assoc();
                $aktu_roll_no = $student_data['aktu_roll_no'];
                $year = $student_data['year'];

                // Insert marks into the database for the selected students
                $query = "INSERT INTO Marks (student_id, aktu_roll_no, year, subject, marks, exam_type) 
                          VALUES ('$student_id', '$aktu_roll_no', '$year', '$selected_subject', '$marks', '$selected_exam_type')";
                $conn->query($query);
            }
        }

        // Success message after processing
        echo "<div class='message'>Marks uploaded successfully!</div>";
    } else {
        echo "<div class='error'>Please enter marks for at least one student.</div>";
    }
}

// Fetch students based on the selected year (AJAX-style)
if (isset($_GET['year'])) {
    $year = $_GET['year'];
    $query = "SELECT * FROM student_signup WHERE year = '$year'";
    $result = $conn->query($query);

    $students = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $students[] = $row;
        }
    }

    echo json_encode($students);  // Return the students list as JSON
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Marks</title>
    <style>
    body {
        font-family: 'Arial', sans-serif;
        background: linear-gradient(to right,rgb(10, 35, 48),rgb(39, 130, 180)); /* Gradient from bright blue to dark blue */
        color: #fff;
        margin: 0;
        padding: 0;
    }

    h1 {
        text-align: center;
        color: #fff;
        font-size: 2.2em; /* Slightly smaller title */
        margin-top: 50px;
        padding: 20px;
        background-color:rgb(32, 142, 193);
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
    }

    form {
        background-color: #ffffff;
        padding: 20px; /* Reduced padding */
        border-radius: 10px;
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
        max-width: 600px; /* Reduced max width */
        margin: 30px auto;
        color: #333;
    }

    label {
        font-weight: bold;
        font-size: 14px; /* Reduced font size */
        color: #444;
        margin-bottom: 8px;
        display: block;
    }

    select, input[type="text"], input[type="submit"] {
        width: 100%;
        padding: 10px; /* Reduced padding */
        margin: 8px 0; /* Reduced margin */
        font-size: 14px; /* Reduced font size */
        border: 2px solid #ddd;
        border-radius: 6px;
        box-sizing: border-box;
        background-color: #f9f9f9;
        transition: all 0.3s ease;
    }

    select:focus, input[type="text"]:focus {
        border-color: #1e90ff;
        outline: none;
    }

    .marks-section {
        margin: 15px 0; /* Reduced margin */
    }

    .student-input {
        display: flex;
        justify-content: space-between;
        margin-bottom: 8px; /* Reduced margin */
        padding: 8px; /* Reduced padding */
        background-color: #f1f1f1;
        border-radius: 6px;
        transition: background-color 0.3s ease;
    }

    .student-input:hover {
        background-color: #e0f7ff;
    }

    .student-info {
        font-size: 14px; /* Reduced font size */
        color: #333;
    }

    input[type="submit"] {
        background-color: #1e90ff;
        color: white;
        border: none;
        cursor: pointer;
        font-size: 16px;
        font-weight: bold;
        padding: 12px; /* Reduced padding */
        border-radius: 8px;
        transition: background-color 0.3s ease;
    }

    input[type="submit"]:hover {
        background-color: #0077cc;
    }

    .form-footer {
        text-align: center;
        font-size: 14px;
        color: #555;
        margin-top: 20px; /* Reduced margin */
    }

    .message {
        text-align: center;
        color: green;
        font-size: 16px; /* Reduced font size */
        margin-top: 10px; /* Reduced margin */
    }

    .error {
        text-align: center;
        color: red;
        font-size: 16px; /* Reduced font size */
        margin-top: 10px; /* Reduced margin */
    }

    .marks-section label {
        font-size: 12px; /* Reduced font size */
    }

    .marks-section {
        max-height: 250px; /* Reduced max height */
        overflow-y: auto;
    }

    .error, .message {
        padding: 8px; /* Reduced padding */
        border-radius: 8px;
        background-color: rgba(0, 0, 0, 0.1);
        margin-top: 15px; /* Reduced margin */
    }

    .marks-section {
        margin-top: 15px;
        padding: 8px; /* Reduced padding */
        background-color: #f9f9f9;
        border-radius: 6px;
        border: 1px solid #ddd;
    }

    .marks-section input {
        padding: 6px; /* Reduced padding */
        font-size: 14px; /* Reduced font size */
        border: 2px solid #ddd;
        border-radius: 6px;
        margin-left: 8px; /* Reduced margin */
        transition: border-color 0.3s ease;
    }

    .marks-section input[type="text"]:focus {
        border-color: #1e90ff;
    }
</style>

</head>
<body>
    <h1>Upload Marks</h1>
    <form action="upload_marks.php" method="POST">
        <!-- Year selection -->
        <label for="year">Select Year:</label>
        <select name="year" id="year" onchange="fetchStudents()" required>
            <option value="">--Select Year--</option>
            <option value="First">1st Year</option>
            <option value="Second">2nd Year</option>
            <option value="Third">3rd Year</option>
            <option value="Fourth">4th Year</option>
        </select><br><br>

        <!-- Subject selection -->
        <label for="subject">Select Subject:</label>
        <select name="subject" id="subject" required>
            <option value="">--Select Subject--</option>
            <?php
            // Populate the subjects dropdown from the $subjects array
            foreach ($subjects as $subject) {
                echo "<option value='$subject'>$subject</option>";
            }
            ?>
        </select><br><br>

        <!-- Exam Type Selection -->
        <label for="exam_type">Exam Type:</label>
        <select name="exam_type" id="exam_type" required>
            <option value="">--Select Exam Type--</option>
            <option value="Sessional 1">Sessional 1</option>
            <option value="Sessional 2">Sessional 2</option>
            <option value="Sessional 3">Sessional 3</option>
            <option value="Internal">Internal</option>
            <option value="External Lab">External Lab</option>
            <option value="Internal Lab">Internal Lab</option>
        </select>

        <!-- Student marks section -->
        <div class="marks-section" id="marks-section"></div>

        <!-- Submit button -->
        <input type="submit" value="Upload Marks">
    </form>

    <div class="form-footer">
        <p>&copy; 2024 CSE Department, IERT</p>
    </div>

    <script>
        // Function to fetch students based on selected year
        function fetchStudents() {
            var year = document.getElementById('year').value;

            if (year === "") {
                document.getElementById('marks-section').innerHTML = "";
                return;
            }

            var xhr = new XMLHttpRequest();
            xhr.open("GET", "upload_marks.php?year=" + year, true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    var students = JSON.parse(xhr.responseText);
                    var marksSection = document.getElementById('marks-section');

                    marksSection.innerHTML = "";  // Clear previous results

                    if (students.length > 0) {
                        students.forEach(function(student) {
                            var studentInput = ` 
                                <label class="student-input">
                                    <span class="student-info">${student.name} (${student.aktu_roll_no})</span>
                                    <input type="text" name="students[${student.id}]" placeholder="Enter Marks" required>
                                </label>`; 
                            marksSection.innerHTML += studentInput;
                        });
                    } else {
                        marksSection.innerHTML = "No students found for this year.";
                    }
                }
            };
            xhr.send();
        }
    </script>
</body>
</html>
<?php
$conn->close();
?>
