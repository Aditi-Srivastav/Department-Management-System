-- Create the database
CREATE DATABASE IF NOT EXISTS CSE_Department;
USE CSE_Department;

-- Student Sign-Up Table
CREATE TABLE student_signup (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    contact_number VARCHAR(10) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    date_of_birth DATE NOT NULL,
    aktu_roll_no VARCHAR(20) NOT NULL,
    password VARCHAR(255) NOT NULL,
    year VARCHAR(10) NOT NULL,
    semester VARCHAR(10) NOT NULL
);

-- Login Table (Students Only)
CREATE TABLE Login (
    login_id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL,
    FOREIGN KEY (email) REFERENCES student_signup(email) ON DELETE CASCADE
);

-- Faculty Table (For Faculty Dashboard Access)
CREATE TABLE Faculty (
    faculty_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    contact_number VARCHAR(15) NOT NULL
);

-- Faculty Subjects Table (with 'name' column referencing Faculty table)
CREATE TABLE Faculty_Subjects (
    faculty_subject_id INT AUTO_INCREMENT PRIMARY KEY,
    faculty_id INT NOT NULL,
    subject VARCHAR(255) NOT NULL,
    name VARCHAR(100) NOT NULL, -- Added name column
    faculty_email VARCHAR(100) NOT NULL, -- Added faculty_email column
    FOREIGN KEY (faculty_id) REFERENCES Faculty(faculty_id) ON DELETE CASCADE
);

-- Attendance Table
CREATE TABLE Attendance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    aktu_roll_no VARCHAR(20) NOT NULL,
    year ENUM('First', 'Second', 'Third', 'Fourth') NOT NULL,
    date DATE NOT NULL,
    status ENUM('present', 'absent') NOT NULL,
    subject VARCHAR(255) NOT NULL,
    FOREIGN KEY (student_id) REFERENCES student_signup(id)
);

CREATE TABLE IF NOT EXISTS Marks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    aktu_roll_no VARCHAR(20) NOT NULL,
    year ENUM('First', 'Second', 'Third', 'Fourth') NOT NULL,
    subject VARCHAR(100) NOT NULL,
    marks DECIMAL(5, 2) NOT NULL,
    exam_type ENUM('Sessional 1', 'Sessional 2', 'Sessional 3', 'Internal', 'External Lab', 'Internal Lab') NOT NULL,
    FOREIGN KEY (student_id) REFERENCES student_signup(id) ON DELETE CASCADE
);


-- Study Materials Table
CREATE TABLE StudyMaterials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    year INT NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(255) NOT NULL
);

-- Admin Table (For Admin Login)
CREATE TABLE admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) UNIQUE,
    password VARCHAR(255)
);

-- Insert initial admin user (hashed password example)
INSERT INTO admin (username, password) 
VALUES ('HOD', '$2y$10$d0c2cUnJgc/UB0qWaZk2SuGjNM7X4FRJ9UcMAjo2G1W.5tKOBzGGm'); -- Example hashed password

-- Create Trigger to automatically insert the name and email in Faculty_Subjects from Faculty
DELIMITER //

CREATE TRIGGER before_insert_faculty_subjects
BEFORE INSERT ON Faculty_Subjects
FOR EACH ROW
BEGIN
    DECLARE faculty_name VARCHAR(100);
    DECLARE faculty_email VARCHAR(100);
    
    -- Get the name and email of the faculty from the Faculty table
    SELECT name, email INTO faculty_name, faculty_email
    FROM Faculty
    WHERE faculty_id = NEW.faculty_id;
    
    -- Assign the name and email to the respective columns in Faculty_Subjects
    SET NEW.name = faculty_name;
    SET NEW.faculty_email = faculty_email;
END//

DELIMITER ;
