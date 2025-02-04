<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "CSE_Department";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $year = $_POST['year'];
    $fileName = $_FILES['study_material']['name'];
    $tempName = $_FILES['study_material']['tmp_name'];
    $uploadDir = "uploads/study_materials/";

    // Ensure the uploads directory exists
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $filePath = $uploadDir . basename($fileName);

    if (move_uploaded_file($tempName, $filePath)) {
        $sql = "INSERT INTO StudyMaterials (year, file_name, file_path) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iss", $year, $fileName, $filePath);

        if ($stmt->execute()) {
            echo "<script>alert('Study material uploaded successfully'); window.location.href='faculty_dashboard.php';</script>";
        } else {
            echo "<script>alert('Error saving file to database'); window.location.href='faculty_dashboard.php';</script>";
        }

        $stmt->close();
    } else {
        echo "<script>alert('Error uploading file'); window.location.href='faculty_dashboard.php';</script>";
    }
}

$conn->close();
?>
