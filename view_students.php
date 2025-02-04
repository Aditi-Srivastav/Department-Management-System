<?php
$conn = new mysqli("localhost", "root", "", "cse_department");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize the year variable to an empty string
$year = '';

// Check if the search form is submitted
if (isset($_POST['search'])) {
    $year = $_POST['year'];

    // Modify the SQL query to filter the students by year
    $sql = "SELECT * FROM student_signup WHERE year = '$year'";
} else {
    // Default query to fetch all students if no filter is applied
    $sql = "SELECT * FROM student_signup";
}

// Check if the delete request is submitted
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    // Delete the record from the database
    $delete_sql = "DELETE FROM student_signup WHERE id = '$id'";
    if ($conn->query($delete_sql) === TRUE) {
        echo "Record deleted successfully";
    } else {
        echo "Error: " . $conn->error;
    }

    // Redirect back to the page after deletion
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

$result = $conn->query($sql);

// Fetch the column names, excluding the 'password' column
$columns_result = $conn->query("SHOW COLUMNS FROM student_signup");
$columns = [];
while ($col = $columns_result->fetch_assoc()) {
    if ($col['Field'] != 'password') {
        $columns[] = $col['Field'];
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Data</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #6a11cb, #2575fc);
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .table-container {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            padding: 20px;
            overflow-x: auto;
            max-width: 90%;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            margin: 20px 0;
        }

        table th, table td {
            padding: 12px 15px;
            border: 1px solid #ddd;
        }

        table th {
            background-color: #6a11cb;
            color: white;
            text-align: center;
        }

        table tr:nth-child(even) {
            background-color: #f4f4f4;
        }

        table tr:hover {
            background-color: #f1f1f1;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
            color: #6a11cb;
        }

        .search-form {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }

        .search-form select, .search-form button {
            padding: 8px 12px;
            font-size: 1rem;
            margin-right: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .search-form button {
            background-color: #6a11cb;
            color: white;
            cursor: pointer;
        }

        .search-form button:hover {
            background-color: #2575fc;
        }

        /* Styling for the delete button */
        .delete-btn {
            color: white;
            background-color: red;
            padding: 8px 12px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            text-align: center;
        }

        .delete-btn:hover {
            background-color: darkred;
        }
    </style>
</head>
<body>

    <div class="table-container">
        <h1>Student Information</h1>

        <!-- Search Form -->
        <form action="" method="post" class="search-form">
            <label for="year">Select Year:</label>
            <select name="year" id="year">
                <option value="First" <?php echo ($year == 'First') ? 'selected' : ''; ?>>First</option>
                <option value="Second" <?php echo ($year == 'Second') ? 'selected' : ''; ?>>Second</option>
                <option value="Third" <?php echo ($year == 'Third') ? 'selected' : ''; ?>>Third</option>
                <option value="Fourth" <?php echo ($year == 'Fourth') ? 'selected' : ''; ?>>Fourth</option>
            </select>

            <button type="submit" name="search">Search</button>
        </form>

        <!-- Table displaying student data -->
        <table>
            <thead>
                <tr>
                    <?php
                    // Dynamically generate the table header based on column names, excluding 'password'
                    foreach ($columns as $column) {
                        echo "<th>" . ucfirst(str_replace("_", " ", $column)) . "</th>";
                    }
                    echo "<th>Action</th>"; // Column for the delete action
                    ?>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        // Dynamically display data for each column, excluding 'password'
                        foreach ($columns as $column) {
                            echo "<td>{$row[$column]}</td>";
                        }
                        // Add a delete button for each row with red styling
                        echo "<td><a href='?delete={$row['id']}' class='delete-btn' onclick='return confirm(\"Are you sure you want to delete this record?\")'>Delete</a></td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='" . (count($columns) + 1) . "'>No records found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

</body>
</html>

<?php
$conn->close();
?>
