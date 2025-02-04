<?php
// Include database connection
include('db_connection.php');

// Initialize variables
$year = "";
$date = "";
$subject = "";
$attendance_records = [];
$subjects = [];

// Assuming faculty email is stored in session after login
$faculty_email = isset($_SESSION['faculty_email']) ? $_SESSION['faculty_email'] : '';

// Fetch subjects based on the logged-in faculty's email
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($faculty_email)) {
    // Query to fetch subjects assigned to the faculty
    $subject_query = "SELECT DISTINCT subject FROM Faculty_Subjects 
                      WHERE faculty_email = ?";
    if ($stmt = $conn->prepare($subject_query)) {
        $stmt->bind_param("s", $faculty_email);  // "s" denotes the string type for faculty email
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $subjects[] = $row['subject'];
            }
        }
        $stmt->close();
    }
}

// Handle form submission for viewing attendance records
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $year = $_POST['year'];
    $date = $_POST['date'];
    $subject = $_POST['subject'];

    // Fetch attendance records based on year, date, and subject
    $query = "SELECT s.name, s.aktu_roll_no, a.date, a.status 
              FROM Attendance a 
              INNER JOIN student_signup s ON a.student_id = s.id
              WHERE s.year = ?";

    // Add date filter if selected
    if (!empty($date)) {
        $query .= " AND a.date = ?";
    }

    // Add subject filter if selected
    if (!empty($subject)) {
        $query .= " AND a.subject = ?";
    }

    $query .= " ORDER BY a.date DESC";

    if ($stmt = $conn->prepare($query)) {
        // Bind parameters to the query
        if (!empty($date) && !empty($subject)) {
            $stmt->bind_param("sss", $year, $date, $subject);
        } elseif (!empty($date)) {
            $stmt->bind_param("ss", $year, $date);
        } elseif (!empty($subject)) {
            $stmt->bind_param("ss", $year, $subject);
        } else {
            $stmt->bind_param("s", $year);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $attendance_records[] = $row;
            }
        }
        $stmt->close();
    } else {
        // Handle query preparation failure
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Attendance</title>
    <style>
     body {
    font-family: Arial, sans-serif;
    padding: 20px;
    background: linear-gradient(135deg, #2b6ebf, #4a9bd6);
    color: #fff;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    align-items: center;
}

h1 {
    text-align: center;
    font-size: 2.5rem;
    margin-bottom: 20px;
    text-shadow: 2px 2px 15px rgba(0, 0, 0, 0.4);
    color: #d0f0ff;
}

form {
    background: linear-gradient(145deg, #3579c2, #5aacf0);
    color: #fff;
    padding: 25px 35px;
    border-radius: 12px;
    box-shadow: 0 12px 30px rgba(0, 0, 0, 0.4);
    max-width: 600px;
    width: 100%;
    margin: 20px auto;
}

form label {
    font-size: 1.1rem;
    font-weight: bold;
    color: #e8f8ff;
}

form select, form input[type="date"], form input[type="submit"] {
    width: 100%;
    padding: 14px;
    margin: 12px 0;
    font-size: 1rem;
    border: none;
    border-radius: 8px;
    background: rgba(255, 255, 255, 0.9);
    color: #003b57;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
}

form input[type="submit"] {
    background: linear-gradient(145deg, #ff5c33, #ff784d);
    color: #fff;
    font-weight: bold;
    cursor: pointer;
    transition: transform 0.2s ease, background 0.3s ease;
    box-shadow: 0 8px 15px rgba(255, 92, 51, 0.4);
}

form input[type="submit"]:hover {
    background: linear-gradient(145deg, #e44d29, #ff6440);
    transform: scale(1.05);
    box-shadow: 0 10px 20px rgba(255, 92, 51, 0.6);
}

table {
    width: 100%;
    margin-top: 20px;
    border-collapse: collapse;
    background: rgba(255, 255, 255, 0.95);
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.4);
}

table th, table td {
    padding: 14px;
    text-align: left;
    font-size: 1rem;
    color: #004b74;
}

table th {
    background: linear-gradient(145deg, #3e93d8, #58b4f6);
    color: #fff;
    text-transform: uppercase;
    font-weight: bold;
}

table tr:nth-child(even) {
    background: rgba(220, 240, 255, 0.8);
}

.no-records {
    text-align: center;
    margin: 20px 0;
    font-size: 1.3rem;
    color: #e8f8ff;
    font-weight: bold;
}

footer {
    text-align: center;
    margin-top: 30px;
    color: #d0f0ff;
    font-size: 0.9rem;
}

    </style>
</head>
<body>
    <h1>View Attendance</h1>
    <form method="POST" action="view_attendance.php">
        <label for="year">Select Year:</label>
        <select name="year" id="year" required>
            <option value="">--Select Year--</option>
            <option value="First" <?= $year == 'First' ? 'selected' : '' ?>>1st Year</option>
            <option value="Second" <?= $year == 'Second' ? 'selected' : '' ?>>2nd Year</option>
            <option value="Third" <?= $year == 'Third' ? 'selected' : '' ?>>3rd Year</option>
            <option value="Fourth" <?= $year == 'Fourth' ? 'selected' : '' ?>>4th Year</option>
        </select>

        <label for="subject">Select Subject:</label>
        <select name="subject" id="subject" required>
            <option value="">--Select Subject--</option>
            <option value="Data Structures">Data Structures</option>
            <option value="Algorithms">Algorithms</option>
            <option value="Database Management Systems">Database Management Systems</option>
            <option value="Operating Systems">Operating Systems</option>
            <option value="Computer Networks">Computer Networks</option>
            <option value="Software Engineering">Software Engineering</option>
            <option value="Computer Organization and Architecture">Computer Organization and Architecture</option>
            <option value="Discrete Mathematics">Discrete Mathematics</option>
            <option value="Artificial Intelligence">Artificial Intelligence</option>
            <option value="Web Technologies">Web Technologies</option>
        </select>

        <label for="date">Select Date:</label>
        <input type="date" name="date" id="date" value="<?= htmlspecialchars($date) ?>">
        <input type="submit" value="View Attendance">
    </form>

    <?php if (!empty($attendance_records)) { ?>
        <table>
            <thead>
                <tr>
                    <th>Student Name</th>
                    <th>Roll Number</th>
                    <th>Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($attendance_records as $record) { ?>
                    <tr>
                        <td><?= htmlspecialchars($record['name']) ?></td>
                        <td><?= htmlspecialchars($record['aktu_roll_no']) ?></td>
                        <td><?= htmlspecialchars($record['date']) ?></td>
                        <td><?= htmlspecialchars(ucfirst($record['status'])) ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php } else if ($_SERVER['REQUEST_METHOD'] == 'POST') { ?>
        <div class="no-records">No attendance records found for the selected year, subject, and date.</div>
    <?php } ?>

    <footer>
        <p>&copy; 2024 CSE Department, IERT</p>
    </footer>
</body>
</html>

<?php
$conn->close();
?>
