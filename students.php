<?php 
require_once 'db.php';
include 'header.php';
include 'footer.php';

setupDatabase();

$stmt = $pdo->query("
    SELECT students.id, students.name, students.email, courses.course_name FROM students
    LEFT JOIN courses ON students.course_id = courses.id
");
$students = $stmt->fetchAll();
?>

