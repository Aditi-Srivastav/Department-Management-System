<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "CSE_Department");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables
$email = '';
$password = '';
$attendance_data = [];
$error_message = '';
$search_subject = '';
$search_date = '';
$total_classes = 0;
$attended_classes = 0;
$attendance_percentage = 0;

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get email, password, and search parameters from the form
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $search_subject = $_POST['search_subject'] ?? '';
    $search_date = $_POST['search_date'] ?? '';

    // Validate credentials
    $query = "SELECT * FROM student_signup WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $student = $result->fetch_assoc();

        // Verify password
        if (password_verify($password, $student['password'])) {
            // Base query to fetch attendance
            $attendance_query = "SELECT a.subject, a.date, a.status FROM Attendance a WHERE a.student_id = ?";
            
            // Add filters if search parameters are provided
            $conditions = [];
            if (!empty($search_subject)) {
                $conditions[] = "a.subject LIKE ?";
            }
            if (!empty($search_date)) {
                $conditions[] = "a.date LIKE ?";
            }
            
            if (!empty($conditions)) {
                $attendance_query .= " AND " . implode(" AND ", $conditions);
            }

            $stmt = $conn->prepare($attendance_query);

            // Bind parameters dynamically based on conditions
            if (empty($conditions)) {
                $stmt->bind_param("i", $student['id']);
            } else {
                $params = [$student['id']];
                $types = "i";
                if (!empty($search_subject)) {
                    $params[] = "%" . $search_subject . "%";
                    $types .= "s";
                }
                if (!empty($search_date)) {
                    $params[] = "%" . $search_date . "%";
                    $types .= "s";
                }
                $stmt->bind_param($types, ...$params);
            }

            $stmt->execute();
            $attendance_result = $stmt->get_result();

            if ($attendance_result->num_rows > 0) {
                while ($row = $attendance_result->fetch_assoc()) {
                    $attendance_data[] = $row;
                }
            }

            // Query to calculate attendance statistics
            if (!empty($search_subject)) {
                $stats_query = "SELECT 
                                    COUNT(*) AS total_classes,
                                    SUM(CASE WHEN a.status = 'Present' THEN 1 ELSE 0 END) AS attended_classes
                                FROM Attendance a
                                WHERE a.student_id = ? AND a.subject LIKE ?";
                $stmt = $conn->prepare($stats_query);
                $stmt->bind_param("is", $student['id'], $search_subject);
                $stmt->execute();
                $stats_result = $stmt->get_result();

                if ($stats_result->num_rows > 0) {
                    $stats = $stats_result->fetch_assoc();
                    $total_classes = $stats['total_classes'];
                    $attended_classes = $stats['attended_classes'];
                    $attendance_percentage = $total_classes > 0 ? ($attended_classes / $total_classes) * 100 : 0;
                }
            }
        } else {
            $error_message = "Invalid password.";
        }
    } else {
        $error_message = "No account found with the given email.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Your Attendance</title>
    <style>
    /* Body styles */
body {
    font-family: Arial, sans-serif;
    background: linear-gradient(to right, #4facfe, #00f2fe);
    padding: 20px;
    color: #333;
    margin: 0;
    height: 100vh;
}

/* Heading */
h1 {
    text-align: center;
    color: #fff;
    margin-top: 20px;
}

/* Form styles */
form {
    background-color: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    max-width: 400px; /* Smaller form size */
    margin: 30px auto;
}

/* Label styles */
label {
    font-weight: bold;
    color: #333;
}

/* Input field styles */
input[type="email"],
input[type="password"],
input[type="text"],
input[type="submit"] {
    width: 100%;
    padding: 12px;
    margin: 8px 0;
    border: 1px solid #ddd;
    border-radius: 4px;
    box-sizing: border-box;
    font-size: 14px;
}

/* Submit button styles */
input[type="submit"] {
    background-color: #4facfe;
    color: white;
    border: none;
    cursor: pointer;
}

input[type="submit"]:hover {
    background-color: #00f2fe;
}

/* Table styles */
.attendance-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

.attendance-table th,
.attendance-table td {
    border: 1px solid #ddd;
    padding: 10px;
    text-align: center;
}

.attendance-table th {
    background-color: #f2f2f2;
}

/* Error message styles */
.error {
    color: red;
    text-align: center;
    font-weight: bold;
    margin-top: 10px;
}

/* Stats section styles */
.stats {
    margin-top: 20px;
    text-align: center;
    background-color: #fff;
    padding: 10px;
    border-radius: 6px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

.stats p {
    margin: 5px 0;
    font-weight: bold;
    font-size: 16px;
}
</style>
</head>
<body>
    <h1>View Your Attendance</h1>
    <form action="" method="POST">
        <!-- Email input -->
        <label for="email">Email:</label>
        <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($email); ?>" required>

        <!-- Password input -->
        <label for="password">Password:</label>
        <input type="password" name="password" id="password" required>

        <!-- Search by subject -->
        <label for="search_subject">Search by Subject:</label>
        <input type="text" name="search_subject" id="search_subject" value="<?php echo htmlspecialchars($search_subject); ?>">

        <!-- Search by date -->
        <label for="search_date">Search by Date:</label>
        <input type="text" name="search_date" id="search_date" placeholder="YYYY-MM-DD" value="<?php echo htmlspecialchars($search_date); ?>">

        <input type="submit" value="View Attendance">
    </form>

    <?php if (!empty($error_message)) : ?>
        <div class="error"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <?php if (!empty($attendance_data)) : ?>
        <table class="attendance-table">
            <thead>
                <tr>
                    <th>Subject</th>
                    <th>Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($attendance_data as $data) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($data['subject']); ?></td>
                        <td><?php echo htmlspecialchars($data['date']); ?></td>
                        <td><?php echo htmlspecialchars($data['status']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <?php if ($total_classes > 0) : ?>
        <div class="stats">
            <p>Total Classes: <?php echo $total_classes; ?></p>
            <p>Classes Attended: <?php echo $attended_classes; ?></p>
            <p>Attendance Percentage: <?php echo number_format($attendance_percentage, 2); ?>%</p>
        </div>
    <?php endif; ?>
</body>
</html>

<?php
$conn->close();
?>
