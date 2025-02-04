<?php
session_start();
if (!isset($_SESSION['admin_username'])) {
    header("Location: admin_login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        /* General Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #6a11cb, #2575fc);
            color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .dashboard-container {
            background: #fff;
            color: #333;
            padding: 30px 40px;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            text-align: center;
            width: 90%;
            max-width: 500px;
        }

        .dashboard-container h1 {
            color: #6a11cb;
            margin-bottom: 20px;
            font-size: 2rem;
        }

        .dashboard-container a {
            display: block;
            margin: 10px 0;
            padding: 10px 15px;
            background: #6a11cb;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            font-size: 1.1rem;
            transition: background 0.3s;
        }

        .dashboard-container a:hover {
            background: #2575fc;
        }

        .dashboard-container .logout {
            background: #ff4b5c;
        }

        .dashboard-container .logout:hover {
            background: #d93744;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <h1>Welcome, Admin</h1>
        <a href="view_students.php">View Students</a>
        <a href="view_faculty.php">View Faculty</a>
        <a href="view_attendance.php">View Attendance</a>
        <a href="view_marks.php">View Marks</a>
        <a href="logout.php" class="logout">Logout</a>
    </div>
</body>
</html>
