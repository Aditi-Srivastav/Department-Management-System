<?php
// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['student_name'])) {
    header("Location: login.php"); // Redirect to login if not logged in
    exit();
}

// Database connection
$conn = new mysqli("localhost", "root", "", "cse_department");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get student ID
$student_id = $_SESSION['student_id'];

// Fetch the attendance data for the student
$sql = "SELECT * FROM Attendance WHERE year = (SELECT year FROM student_signup WHERE id = ?) ORDER BY date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Department of CSE, IERT</title>
    <style>
        /* General Body Styling */
        body {
    font-family: 'Arial', sans-serif;
    background-color: #2c3e50;
    background-image: url('images/slide1.jpg'); /* Your image path */
    background-size: cover; /* Ensures the image covers the entire body */
    background-position: center; /* Centers the background image */
    background-repeat: no-repeat; /* Prevents the image from repeating */
    color: #333;
    margin: 0;
    padding: 0;
}

/* Header Styling */
header {
    background-color:rgb(10, 40, 70);
    color: #fff;
    padding: 20px;
    text-align: center;
}

header h1 {
    font-size: 2.5em;
    margin-bottom: 10px;
}

header div p {
    font-size: 1.2em;
}

nav ul {
    list-style-type: none;
    padding: 0;
    margin-top: 10px;
}

nav ul li {
    display: inline;
    margin-right: 20px;
}

nav ul li a {
    color: #fff;
    text-decoration: none;
    font-size: 1.1em;
}

nav ul li a:hover {
    text-decoration: underline;
}

nav .button {
    background-color: #e74c3c;
    color: white;
    padding: 10px 20px;
    text-decoration: none;
    border-radius: 5px;
}

nav .button:hover {
    background-color: #c0392b;
}

/* Main Section Styling */
main {
    margin: 20px;
    padding: 20px;
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

main h2 {
    font-size: 2em;
    color:  #c0392b ;
    margin-bottom: 20px;
}

/* Table Styling */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

table th, table td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

table th {
    background-color: white;
    color: white;
}

table tr:hover {
    background-color: #ecf0f1;
}

table td {
    font-size: 1.1em;
}

table td, table th {
    text-align: center;
}

/* Button Styling */
.button {
    background-color: #3498db;
    color: white;
    padding: 10px 20px;
    border-radius: 5px;
    text-decoration: none;
    font-size: 1em;
}

.button:hover {
    background-color: #2980b9;
}

/* Media Queries for Responsive Design */
@media screen and (max-width: 768px) {
    nav ul li {
        display: block;
        margin-right: 0;
        margin-bottom: 10px;
    }

    nav .button {
        margin-top: 10px;
        text-align: center;
    }

    table {
        font-size: 0.9em;
    }

    header h1 {
        font-size: 2em;
    }

    .course-container {
        flex-direction: column;
        align-items: center;
    }

    .course-card {
        flex: 1 1 100%;
        margin-bottom: 20px;
    }
}

/* Course Container Styling */
.course-container {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    margin-top: 30px;
}

/* Course Card Styling */
.course-card {
    background: #8e44ad;
    border: 1px solid #ddd;
    border-radius: 10px;
    margin: 15px;
    padding: 15px;
    flex: 1 1 calc(22% - 20px);
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease-in-out;
    max-width: 250px;
    text-align: center;
}

.course-card:hover {
    transform: scale(1.05);
}

.course-card img {
    width: 100%;
    height: auto;
    border-radius: 10px;
}

.course-card h3 {
    font-size: 1.6em;
    margin: 10px 0;
    color: #fff;
}

.stars {
    color: gold;
    font-size: 1.5em;
}

.star {
    margin-right: 3px;
}

/* Footer Styling */


/* Modal Styling */
.modal {
    display: none;
    position: fixed;
    z-index: 1;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0, 0, 0, 0.4);
    padding-top: 60px;
}

.modal-content {
    background-color: #fefefe;
    margin: 5% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 60%;
    border-radius: 5px;
}

.close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
}

.close:hover,
.close:focus {
    color: black;
    text-decoration: none;
    cursor: pointer;
}

/* Section Styling */
#contact {
    margin: 20px 0;
    color: white;
}

/* Year Section Color Change */
.year-section {
    color: white; /* Apply white color to year sections */
}



    </style>
</head>
<body>
    <header>
    
        <h1>Department of CSE IERT</h1>
        <div>
            <p>Welcome, <strong><?php echo $_SESSION['student_name']; ?></strong>! You are logged in.</p>
        </div>
        <nav>
            <ul>
                <li><a href="#">Home</a></li>
                <li><a href="aboutus.html">About</a></li>
                <li><a href="#courses">Year</a></li>
                <li><a href="https://www.iertentrance.in/">Contact Us</a></li>
                <li><a href="https://erp.aktu.ac.in/webpages/oneview/oneview.aspx">View Result</a></li>
                <li><a href="viewmarks_student.php">View Marks</a></li>
                 <li><a href="viewattendance_student.php">View Attendance</a></li> <!-- Added View Attendance Link -->
                <a href="logout.php" class="button">Logout</a>
            </ul>
        </nav>
    </header>



    <section id="courses">
            <h2>Years</h2>
            <div class="course-container">
                <class class="course-card">
                    <img src="images/c.jpg" alt="First Year">
                    <h3>First Year</h3>
                    <div class="stars">
                        <span class="star">&#9733;</span>
                        <span class="star">&#9733;</span>
                        <span class="star">&#9733;</span>
                        <span class="star">&#9733;</span>
                        <span class="star">&#9733;</span>
                    </div>
<!-- First Year -->
<div class="year" id="first-year">
    <a href="first-year-syllabus.html">
        <button class="button">Explore</button>
    </a>
</div>
</class>

                

                <div class="course-card">
                    <img src="images/DSA.jpg" alt="C Programming Course">
                    <h3>Second Year</h3>
                    <div class="stars">
                        <span class="star">&#9733;</span>
                        <span class="star">&#9733;</span>
                        <span class="star">&#9733;</span>
                        <span class="star">&#9733;</span>
                        <span class="star">&#9733;</span>
                    </div>
                    <div class="year" id="second-year">
                        
                        <a href="second-year-syllabus.html">
                            <button class="button">Explore</button>
                        </a>
                    </div>
                </div>

                <div class="course-card">
                    <img src="images/3RD YEAR.jpg" alt="DSA Course">
                    <h3>Third Year</h3>
                    <div class="stars">
                        <span class="star">&#9733;</span>
                        <span class="star">&#9733;</span>
                        <span class="star">&#9733;</span>
                        <span class="star">&#9733;</span>
                        <span class="star">&#9733;</span>
                    </div>
                    <!-- Third Year -->
        <div class="year" id="third-year">
            
            <a href="third-year-syllabus.html">
                <button class="button">Explore</button>
            </a>
        </div>
                </div>

                <div class="course-card">
                    <img src="images/4TH YEAR.jpg" alt="CG Course">
                    <h3>Fourth Year</h3>
                    <div class="stars">
                        <span class="star">&#9733;</span>
                        <span class="star">&#9733;</span>
                        <span class="star">&#9733;</span>
                        <span class="star">&#9733;</span>
                        <span class="star">&#9733;</span>
                    </div>
                   <!-- Fourth Year-->
                    <div class="year" id="first-year">
                        <a href="fourth-year-syllabus.html">
                            <button class="button">Explore</button>
                        </a>
                    </div>
                </div>
            </div>
        </section>

        
    </main>

    

    <script src="script.js"></script>
</body>
</html>
</body>
</html>

<?php
// Close the connection
$stmt->close();
$conn->close();
?>
