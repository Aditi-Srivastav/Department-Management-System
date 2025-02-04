<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "CSE_Department");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize search parameters
$selected_year = '';
$selected_subject = '';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get selected year and subject from the form
    $selected_year = $_POST['year'];
    $selected_subject = $_POST['subject'];

    // Modify query to filter based on the selected criteria
    $query = "SELECT s.name, s.aktu_roll_no, m.marks
              FROM student_signup s
              JOIN Marks m ON s.id = m.student_id
              WHERE s.year = '$selected_year' AND m.subject = '$selected_subject'";

    $result = $conn->query($query);
    $marks_data = [];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $marks_data[] = $row;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Marks</title>
    <style>
 body {
    font-family: 'Arial', sans-serif;
    background: linear-gradient(135deg, #0072ff, #00c6ff); /* Blue gradient */
    color: #fff;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

h1 {
    font-size: 2.5rem;
    margin-bottom: 20px;
    text-align: center;
    text-transform: uppercase;
    color: #fdfdfd;
    text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.3);
}

form {
    background: rgba(255, 255, 255, 0.2); /* Transparent white */
    padding: 25px 35px;
    border-radius: 15px;
    backdrop-filter: blur(8px);
    box-shadow: 0 12px 24px rgba(0, 0, 0, 0.3);
    width: 100%;
    max-width: 700px;
}

label {
    font-weight: bold;
    font-size: 1rem;
    color: #f5f5f5;
}

select, input[type="submit"] {
    width: 100%;
    padding: 12px;
    margin: 10px 0;
    font-size: 1rem;
    border: none;
    border-radius: 8px;
    box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
}

select {
    background: rgba(255, 255, 255, 0.95);
    color: #333;
}

input[type="submit"] {
    background: linear-gradient(145deg, #ff7e5f, #feb47b); /* Orange gradient */
    color: #fff;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.3s ease-in-out;
    box-shadow: 0 6px 14px rgba(255, 126, 95, 0.4);
}

input[type="submit"]:hover {
    background: linear-gradient(145deg, #e76f4d, #ff8c6c);
    box-shadow: 0 8px 20px rgba(255, 126, 95, 0.6);
}

.marks-table {
    width: 90%;
    max-width: 800px;
    margin: 20px auto;
    border-collapse: collapse;
    border: 1px solid #fff;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
    background: rgba(255, 255, 255, 0.95); /* White background for table */
}

.marks-table th, .marks-table td {
    padding: 12px;
    border: 1px solid #ddd;
    text-align: center;
    font-size: 1rem;
}

.marks-table th {
    background: #0072ff; /* Blue header */
    color: white;
    text-transform: uppercase;
}

.marks-table td {
    color: #333;
}

.marks-table tr:nth-child(even) {
    background: rgba(240, 240, 240, 0.8); /* Light gray for even rows */
}

.error {
    text-align: center;
    color: #ff4d4d; /* Red error */
    font-size: 1.2rem;
    font-weight: bold;
    margin-top: 20px;
}

.form-footer {
    margin-top: 20px;
    text-align: center;
    font-size: 0.9rem;
    color: #e0e0e0;
}

    </style>
</head>
<body>
    <h1>View Marks</h1>
    <form action="view_marks.php" method="POST">
        <!-- Year selection -->
        <label for="year">Select Year:</label>
        <select name="year" id="year" required>
            <option value="">--Select Year--</option>
            <option value="First" <?php echo $selected_year == 'First' ? 'selected' : ''; ?>>1st Year</option>
            <option value="Second" <?php echo $selected_year == 'Second' ? 'selected' : ''; ?>>2nd Year</option>
            <option value="Third" <?php echo $selected_year == 'Third' ? 'selected' : ''; ?>>3rd Year</option>
            <option value="Fourth" <?php echo $selected_year == 'Fourth' ? 'selected' : ''; ?>>4th Year</option>
        </select><br><br>

        <!-- Subject selection -->
        <label for="subject">Select Subject:</label>
        <select name="subject" id="subject" required>
            <option value="">--Select Subject--</option>
            <!-- List of subjects -->
            <option value="Database Management Systems" <?php echo $selected_subject == 'Database Management Systems' ? 'selected' : ''; ?>>DBMS</option>
            <option value="Basic Electrical Engineering" <?php echo $selected_subject == 'Basic Electrical Engineering' ? 'selected' : ''; ?>>Basic Electrical Engineering</option>
            <option value="Computer Programming" <?php echo $selected_subject == 'Computer Programming' ? 'selected' : ''; ?>>Computer Programming</option>
            <option value="Engineering Physics" <?php echo $selected_subject == 'Engineering Physics' ? 'selected' : ''; ?>>Engineering Physics</option>
            <!-- Add other subjects as needed -->
        </select><br><br>

        <input type="submit" value="View Marks">
    </form>

    <?php if (isset($marks_data) && !empty($marks_data)) : ?>
        <table class="marks-table">
            <thead>
                <tr>
                    <th>Student Name</th>
                    <th>Roll Number</th>
                    <th>Marks</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($marks_data as $data) : ?>
                    <tr>
                        <td><?php echo $data['name']; ?></td>
                        <td><?php echo $data['aktu_roll_no']; ?></td>
                        <td><?php echo $data['marks']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php elseif (isset($marks_data)) : ?>
        <div class="error">No marks found for the selected year and subject.</div>
    <?php endif; ?>

    <div class="form-footer">
        <p>&copy; 2024 CSE Department, IERT</p>
    </div>
</body>
</html>

<?php
$conn->close();
?>
