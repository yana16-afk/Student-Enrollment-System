<?php
require 'db.php';

setupDatabase();

$conn = getConnection();
if ($conn) {
     $studentCount = $conn->query("SELECT COUNT(*) FROM students")->fetchColumn();
     echo "Students table contains $studentCount record(s).<br>";
 
     // Check courses table
     $courseCount = $conn->query("SELECT COUNT(*) FROM courses")->fetchColumn();
     echo "Courses table contains $courseCount record(s).<br>";
} else {
    echo "Failed to connect to the database.";
}
$conn = null;
?>

?>