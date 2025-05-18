<?php 
require_once 'db_connection.php';
include 'header.php';
include 'footer.php';

$stmt = $pdo->query("
    SELECT students.id, students.name, students.email, courses.course_name FROM students
    LEFT JOIN courses ON students.course_id = courses.id
");
$students = $stmt->fetchAll();
?>

