<?php

function getConnection() {
    $dbhost = 'localhost';
    $dbuser = 'root';
    $dbpass = '';
    $dbname = 'student_enrollment';

    try{
        // Create connection
        $conn = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $conn->query("CREATE DATABASE IF NOT EXISTS $dbname");
        return $conn;
    } catch (PDOException $ex) {
        die ("Connection failed: " . $ex->getMessage());
    }
}
function setupDatabase() {
     $servername = "localhost";
     $username = "root";
     $password = "";
 
     try {
         $pdo = new PDO("mysql:host=$servername;charset=utf8mb4", $username, $password);
         $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
 
         $pdo->exec("CREATE DATABASE IF NOT EXISTS student_enrollment");
         $pdo->exec("USE student_enrollment");
 
         $sql = "
             DROP TABLE IF EXISTS students;
             DROP TABLE IF EXISTS courses;
 
             CREATE TABLE courses (
                 id VARCHAR(8) PRIMARY KEY,
                 course_name VARCHAR(30) NOT NULL
             );
 
             CREATE TABLE students (
                 id VARCHAR(11) PRIMARY KEY,
                 name VARCHAR(40) NOT NULL,
                 email VARCHAR(25) NOT NULL,
                 course_id VARCHAR(8),
                 FOREIGN KEY (course_id) REFERENCES courses(id)
             );
 
             INSERT INTO courses (id, course_name) VALUES
                 ('COMP001', 'Introduction to Computing'),
                 ('COMP002', 'Computer Programming 1'),
                 ('COMP003', 'Computer Programming 2'),
                 ('COMP004', 'Discrete Structures 1'),
                 ('COMP005', 'Discrete Structures 2'),
                 ('COMP006', 'Data Structures and Algorithms'),
                 ('COMP007', 'Operating Systems'),
                 ('COMP008', 'Data Communications and Networking'),
                 ('COMP009', 'Object Oriented Programming'),
                 ('COMP010', 'Information Management'),
                 ('COMP011', 'Technical Documentation and Presentation Skills in ICT'),
                 ('COMP013', 'Human Computer Interaction'),
                 ('COMP015', 'Fundamentals of Research'),
                 ('COMP016', 'Web Development');
 
             INSERT INTO students (id, name, email, course_id) VALUES
                 ('09001', 'Juan Dela Cruz', 'juan.delacruz@example.com', 'COMP001'),
                 ('09002', 'Maria Clara', 'maria.clara@example.com', 'COMP002'),
                 ('09003', 'Jose Rizal', 'jose.rizal@example.com', 'COMP003'),
                 ('09004', 'Andres Bonifacio', 'andres.bonifacio@example.com', 'COMP004'),
                 ('09005', 'Emilio Aguinaldo', 'emilio.aguinaldo@example.com', 'COMP005'),
                 ('09006', 'Gabriela Silang', 'gabriela.silang@example.com', 'COMP006'),
                 ('09007', 'Lapu-Lapu', 'lapu.lapu@example.com', 'COMP007'),
                 ('09008', 'Antonio Luna', 'antonio.luna@example.com', 'COMP008'),
                 ('09009', 'Melchora Aquino', 'melchora.aquino@example.com', 'COMP009'),
                 ('09010', 'Apolinario Mabini', 'apolinario.mabini@example.com', 'COMP010'),
                 ('09011', 'Gregoria De Jesus', 'gregoria.dejesus@example.com', 'COMP011'),
                 ('09012', 'Heneral Luna', 'heneral.luna@example.com', 'COMP013'),
                 ('09013', 'Francisco Balagtas', 'francisco.balagtas@example.com', 'COMP015'),
                 ('09014', 'Leona Florentino', 'leona.florentino@example.com', 'COMP016');
         ";
         
         foreach (explode(";", $sql) as $statement) {
             if (trim($statement) !== '') {
                 $pdo->exec($statement);
             }
         }
 
         echo "Database and tables created successfully, sample data inserted.<br>";
     } catch (PDOException $e) {
         die("Error setting up database: " . $e->getMessage());
     }
 }
 ?>