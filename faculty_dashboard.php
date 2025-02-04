<?php
// Start session
session_start();

// Check if the faculty is logged in
if (!isset($_SESSION['faculty_id'])) {
    // Redirect to login if not logged in
    header("Location: faculty_login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty Dashboard</title>
    <style>
        body {
            
            font-family: 'Arial', sans-serif;
            background-color: #005566 ;
            margin: 0;
            padding: 0;
        }

        .dashboard-container {
            max-width: 700px;
            margin: 50px auto;
            background: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            border-top: 4px solid rgb(74, 184, 206);
        }

        h2 {
            color:rgb(11, 68, 114);
            font-size: 2em;
            margin-bottom: 10px;
        }

        p {
            font-size: 1.1em;
            color: #333;
        }

        .actions a {
            display: inline-block;
            margin: 15px 10px;
            padding: 12px 25px;
            background: rgb(74, 184, 206);
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-size: 1.1em;
            transition: background 0.3s ease;
        }

        .actions a:hover {
            background: #003d46;
        }

        .actions a:active {
            background: #002830;
        }

        .actions a.logout {
            background: #d9534f;
        }

        .actions a.logout:hover {
            background: #c9302c;
        }

        .dashboard-container p {
            font-weight: bold;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <h2>Welcome, <?php echo htmlspecialchars($_SESSION['faculty_name']); ?></h2>
        <p>Email: <?php echo htmlspecialchars($_SESSION['faculty_email']); ?></p>
        
        <h3>Dashboard Actions</h3>
        <div class="actions">
            <a href="upload_attendance.php">Upload Attendance</a>
            <a href="upload_marks.php">Upload Marks</a>
            <a href="logout.php" class="logout">Logout</a>
        </div>
    </div>
</body>
</html>
