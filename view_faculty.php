<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "cse_department");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize the search term to an empty string
$search_term = '';

// Check if the search form is submitted
if (isset($_POST['search'])) {
    $search_term = $_POST['search_term'];
    // Query to select faculty members whose name matches the search term
    $sql = "SELECT * FROM Faculty WHERE name LIKE '%$search_term%'";
} else {
    // Default query to select all records if no search term is provided
    $sql = "SELECT * FROM Faculty";
}

$result = $conn->query($sql);

// Fetch the column names, excluding the 'password' column
$columns_result = $conn->query("SHOW COLUMNS FROM Faculty");
$columns = [];
while ($col = $columns_result->fetch_assoc()) {
    if ($col['Field'] != 'password') {
        $columns[] = $col['Field'];
    }
}

// Handle adding a new faculty member
if (isset($_POST['add_faculty'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $subjects_teaching = $_POST['subjects_teaching'];
    $password = $_POST['password'];

    // Insert the new faculty member into the database
    $insert_sql = "INSERT INTO Faculty (name, email, subjects_teaching, password) 
                   VALUES ('$name', '$email', '$subjects_teaching', '$password')";
    if ($conn->query($insert_sql) === TRUE) {
        echo "<script>alert('New faculty member added successfully!');</script>";
    } else {
        echo "<script>alert('Error: " . $conn->error . "');</script>";
    }
}

// Handle deleting a faculty member
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    // Delete the faculty member from the database
    $delete_sql = "DELETE FROM Faculty WHERE id = $delete_id";
    if ($conn->query($delete_sql) === TRUE) {
        echo "<script>alert('Faculty member deleted successfully!');</script>";
    } else {
        echo "<script>alert('Error: " . $conn->error . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty Information</title>
    <style>
      body {
    font-family: Arial, sans-serif;
    background: linear-gradient(135deg, #0077b6, #023e8a);
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
    background-color: #0077b6;
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
    color: #0077b6;
}

.search-form, .add-form {
    display: flex;
    justify-content: center;
    margin-bottom: 20px;
}

.search-form input, .add-form input, .add-form button {
    padding: 8px 12px;
    font-size: 1rem;
    margin-right: 10px;
    border-radius: 5px;
    border: 1px solid #ccc;
}

.search-form button, .add-form button {
    background-color: #0077b6;
    color: white;
    cursor: pointer;
}

.search-form button:hover, .add-form button:hover {
    background-color: #023e8a;
}

.add-form {
    flex-direction: column;
    align-items: center;
}

.add-form input {
    margin-bottom: 10px;
}

/* Style for the red delete button */
.delete-btn {
    background-color: #ff4d4d;
    color: white;
    padding: 8px 16px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    text-decoration: none;
    text-align: center;
}

.delete-btn:hover {
    background-color: #ff1a1a;
}
  </style>
</head>
<body>
    <div class="table-container">
        <h1>Faculty Information</h1>

        <!-- Search Form -->
        <form method="post" class="search-form">
            <input type="text" name="search_term" placeholder="Search by Name" value="<?php echo $search_term; ?>">
            <button type="submit" name="search">Search</button>
        </form>

        <!-- Add Faculty Form -->
        <div class="add-form"> 
            <h1>Add New Member of IERT</h1>
            <a href="faculty_signup.php" style="text-decoration: none;">
                <button type="button">Add Faculty</button>
            </a>
        </div>

        <?php
        // Check if there are any records
        if ($result->num_rows > 0) {
            // Display the results in a table
            echo "<table>";
            echo "<thead>
                    <tr>";
            // Dynamically generate the table header based on columns, excluding 'password'
            foreach ($columns as $column) {
                echo "<th>" . ucfirst(str_replace("_", " ", $column)) . "</th>";
            }
            echo "<th>Action</th></tr>
                  </thead>
                  <tbody>";

            // Display the table rows with data for each column
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                foreach ($columns as $column) {
                    echo "<td>{$row[$column]}</td>";
                }
                // Add Delete button styled as a red button
                echo "<td><a href='?delete_id={$row['faculty_id']}' class='delete-btn'>Delete</a></td>";
                echo "</tr>";
            }
            echo "</tbody></table>";
        } else {
            echo "<p>No records found matching the search term.</p>";
        }

        // Close the connection
        $conn->close();
        ?>
    </div>
</body>
</html>
