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
$marks_data = [];
$error_message = '';
$search_subject = '';
$search_exam_type = '';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get email, password, and search parameters from the form
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $search_subject = $_POST['search_subject'] ?? '';
    $search_exam_type = $_POST['search_exam_type'] ?? '';

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
            // Base query to fetch marks
            $marks_query = "SELECT m.subject, m.marks, m.exam_type FROM Marks m WHERE m.student_id = ?";
            
            // Add filters if search parameters are provided
            $conditions = [];
            if (!empty($search_subject)) {
                $conditions[] = "m.subject LIKE ?";
            }
            if (!empty($search_exam_type)) {
                $conditions[] = "m.exam_type LIKE ?";
            }
            
            if (!empty($conditions)) {
                $marks_query .= " AND " . implode(" AND ", $conditions);
            }

            $stmt = $conn->prepare($marks_query);

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
                if (!empty($search_exam_type)) {
                    $params[] = "%" . $search_exam_type . "%";
                    $types .= "s";
                }
                $stmt->bind_param($types, ...$params);
            }

            $stmt->execute();
            $marks_result = $stmt->get_result();

            if ($marks_result->num_rows > 0) {
                while ($row = $marks_result->fetch_assoc()) {
                    $marks_data[] = $row;
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
    <title>View Your Marks</title>
    <style>
    /* General body styling */
    body {
        font-family: 'Arial', sans-serif;
        background: linear-gradient(135deg, #1e90ff, #32cd32); /* Gradient from blue to green */
        color: #333;
        padding: 40px;
        margin: 0;
    }

    h1 {
        text-align: center;
        color: #fff; /* White color for better contrast */
        font-size: 1.5em;
        margin-bottom: 20px;
    }

    /* Form styling */
    form {
        background-color: #fff;
        padding: 15px;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        max-width: 400px;
        margin: 0 auto 20px;
    }

    label {
        font-size: 12px;
        font-weight: bold;
        color: #555;
        display: block;
        margin-bottom: 6px;
    }

    input[type="email"], input[type="password"], input[type="text"], input[type="submit"] {
        width: 100%;
        padding: 8px;
        margin: 8px 0;
        font-size: 14px;
        border: 1px solid #ddd;
        border-radius: 6px;
        box-sizing: border-box;
        background-color: #fafafa;
        transition: border 0.3s ease;
    }

    input[type="email"]:focus, input[type="password"]:focus, input[type="text"]:focus {
        border-color: #1e90ff;
        outline: none;
    }

    input[type="submit"] {
        background-color: #1e90ff;
        color: white;
        font-weight: bold;
        border: none;
        cursor: pointer;
        font-size: 16px;
        padding: 10px;
        border-radius: 8px;
        transition: background-color 0.3s ease;
    }

    input[type="submit"]:hover {
        background-color: #0077cc;
    }

    /* Error message styling */
    .error {
        color: red;
        text-align: center;
        font-size: 14px;
        margin-top: 15px;
        padding: 10px;
        background-color: #ffe6e6;
        border: 2px solid #ff4d4d;
        border-radius: 8px;
    }

    /* Marks table styling */
    .marks-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    .marks-table th, .marks-table td {
        padding: 10px;
        border: 1px solid #ddd;
        text-align: center;
        font-size: 14px;
    }

    .marks-table th {
        background-color: #f0f0f0;
        color: #333;
    }

    .marks-table tr:nth-child(even) {
        background-color: #fafafa;
    }

    .marks-table tr:hover {
        background-color: #e6f7ff;
    }

    .marks-table td {
        font-weight: 500;
    }
</style>

</head>
<body>
    <h1>View Your Marks</h1>
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

        <!-- Search by exam type -->
        <label for="search_exam_type">Search by Exam Type:</label>
        <input type="text" name="search_exam_type" id="search_exam_type" value="<?php echo htmlspecialchars($search_exam_type); ?>">

        <input type="submit" value="View Marks">
    </form>

    <?php if (!empty($error_message)) : ?>
        <div class="error"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <?php if (!empty($marks_data)) : ?>
        <table class="marks-table">
            <thead>
                <tr>
                    <th>Subject</th>
                    <th>Marks</th>
                    <th>Exam Type</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($marks_data as $data) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($data['subject']); ?></td>
                        <td><?php echo htmlspecialchars($data['marks']); ?></td>
                        <td><?php echo htmlspecialchars($data['exam_type']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</body>
</html>

<?php
$conn->close();
?> 