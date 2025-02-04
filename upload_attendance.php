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

// Handle form submission for attendance
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $selected_year = $_POST['year'];
    $attendance_date = $_POST['date'];
    $selected_subject = $_POST['subject'];  // Get selected subject from form
    $selected_students = $_POST['students'] ?? [];

    // Validate the form input
    if (empty($selected_year) || empty($attendance_date) || empty($selected_subject)) {
        echo "<div class='error'>Please select year, date, and subject.</div>";
    } elseif (!empty($selected_students)) {
        foreach ($selected_students as $student_id => $status) {
            // Fetch aktu_roll_no and year for the student
            $student_query = "SELECT aktu_roll_no, year FROM student_signup WHERE id = '$student_id'";
            $student_result = $conn->query($student_query);

            if ($student_result->num_rows > 0) {
                $student_data = $student_result->fetch_assoc();
                $aktu_roll_no = $student_data['aktu_roll_no'];
                $year = $student_data['year'];

                // Insert attendance into the database for the selected students
                $query = "INSERT INTO Attendance (student_id, aktu_roll_no, year, date, subject, status) 
                          VALUES ('$student_id', '$aktu_roll_no', '$year', '$attendance_date', '$selected_subject', '$status')";
                $conn->query($query);
            }
        }

        // Success message after processing
        echo "<div class='message'>Attendance uploaded successfully!</div>";
    } else {
        echo "<div class='error'>Please select at least one student.</div>";
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
    <title>Upload Attendance</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
    body {
        font-family: 'Arial', sans-serif;
        background: linear-gradient(to right,rgb(25, 50, 71),rgb(129, 230, 235)); /* Gradient from light blue to sky blue */
        color: #333;
        margin: 0;
        padding: 0;
    }

    h1 {
        text-align: center;
        color: #fff;
        background-color: #1e90ff; /* Blue header */
        padding: 15px;
        font-size: 2em;
        margin-bottom: 30px;
        border-bottom: 3px solid #1c75d1;
    }

    form {
        background-color: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
        max-width: 600px;  /* Smaller width */
        margin: 30px auto;
    }

    label {
        font-weight: bold;
        font-size: 14px;
        color: #333;
        margin-bottom: 8px;
        display: block;
    }

    select, input[type="date"], input[type="submit"], .checkbox-container input[type="checkbox"] {
        width: 100%;
        padding: 8px;
        margin: 8px 0;
        font-size: 14px;
        border: 2px solid #ddd;
        border-radius: 5px;
        transition: all 0.3s ease;
    }

    select:focus, input[type="date"]:focus, input[type="submit"]:focus, .checkbox-container input[type="checkbox"]:focus {
        border-color: #1e90ff;
        outline: none;
    }

    .attendance-section {
        margin-top: 15px;
    }

    .student-radio {
        display: flex;
        justify-content: space-between;
        margin-bottom: 12px;
        padding: 8px;
        background-color: #f9f9f9;
        border-radius: 5px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        transition: background-color 0.3s ease;
    }

    .student-radio:hover {
        background-color: #e0f7ff; /* Light blue hover effect */
    }

    .student-info {
        font-size: 14px;
        color: #333;
    }

    input[type="radio"] {
        margin-right: 8px;
    }

    input[type="submit"] {
        background-color: #1e90ff;
        color: white;
        border: none;
        cursor: pointer;
        font-size: 16px;
        font-weight: bold;
        padding: 12px;
        border-radius: 5px;
        transition: background-color 0.3s ease;
    }

    input[type="submit"]:hover {
        background-color: #1c75d1;
    }

    .form-footer {
        text-align: center;
        font-size: 14px;
        color: #555;
        margin-top: 30px;
    }

    .message {
        text-align: center;
        color: #1e90ff; /* Blue color for success message */
        font-size: 16px;
        margin-top: 15px;
    }

    .error {
        text-align: center;
        color: #ff4d4d; /* Red color for error message */
        font-size: 16px;
        margin-top: 15px;
    }

    .checkbox-container {
        margin-bottom: 15px;
        text-align: center;
        font-size: 14px;
    }

    .checkbox-container label {
        font-size: 14px;
        font-weight: normal;
        margin-right: 15px;
    }

    .attendance-section label {
        font-size: 14px;
        font-weight: normal;
    }

    .attendance-section {
        max-height: 300px;
        overflow-y: auto;
    }

    .checkbox-container input[type="checkbox"] {
        margin-right: 8px;
    }
</style>



</head>
<body>
    <h1>Upload Attendance</h1>
    <div class="form-container">
        <form action="upload_attendance.php" method="POST">
            <!-- Year selection -->
            <label for="year">Select Year:</label>
            <select name="year" id="year" onchange="fetchStudents()" required>
                <option value="">--Select Year--</option>
                <option value="First">1st Year</option>
                <option value="Second">2nd Year</option>
                <option value="Third">3rd Year</option>
                <option value="Fourth">4th Year</option>
            </select><br>

            <!-- Date selection -->
            <label for="date">Date:</label>
            <input type="date" name="date" id="date" required><br>

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
            </select><br>

            <!-- Mark All Present/Absent checkboxes -->
            <div class="checkbox-container">
                <label>
                    <input type="checkbox" id="mark-all-present" onclick="markAll('present')"> Mark All Present
                </label>
                <label>
                    <input type="checkbox" id="mark-all-absent" onclick="markAll('absent')"> Mark All Absent
                </label>
            </div>

            <!-- Student attendance section -->
            <div class="attendance-section" id="attendance-section"></div>

            <!-- Submit button -->
            <input type="submit" value="Upload Attendance">
        </form>

        <div class="form-footer">
            <p>&copy; 2024 CSE Department, IERT</p>
        </div>
    </div>

    <script>
        // Function to fetch students based on selected year
        function fetchStudents() {
            var year = document.getElementById('year').value;

            if (year === "") {
                document.getElementById('attendance-section').innerHTML = "";
                return;
            }

            var xhr = new XMLHttpRequest();
            xhr.open("GET", "upload_attendance.php?year=" + year, true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    var students = JSON.parse(xhr.responseText);
                    var attendanceSection = document.getElementById('attendance-section');

                    attendanceSection.innerHTML = "";  // Clear previous results

                    if (students.length > 0) {
                        students.forEach(function(student) {
                            var studentRadio = ` 
                                <label class="student-radio">
                                    <span class="student-info">${student.name} (${student.aktu_roll_no})</span>
                                    <label>
                                        <input type="radio" name="students[${student.id}]" value="present" required> Present
                                    </label>
                                    <label>
                                        <input type="radio" name="students[${student.id}]" value="absent" required> Absent
                                    </label>
                                </label>`; 
                            attendanceSection.innerHTML += studentRadio;
                        });
                    } else {
                        attendanceSection.innerHTML = "No students found for this year.";
                    }
                }
            };
            xhr.send();
        }

        // Function to mark all students as present or absent
        function markAll(status) {
            var radios = document.querySelectorAll(`#attendance-section input[type='radio'][value='${status}']`);
            radios.forEach(function(radio) {
                radio.checked = true;
            });
        }
    </script>
</body>
</html>

<?php
$conn->close();
?>
