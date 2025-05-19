<?php 
require_once 'db.php';
include 'header.php';
include 'footer.php';

$pdo = getConnection();
$stmt = $pdo->query("
    SELECT students.id, students.name, students.email, courses.course_name FROM students
    LEFT JOIN courses ON students.course_id = courses.id
");
$students = $stmt->fetchAll();
?>

<!-- INITIAL PA LANG ITO PARA MAKITA KO IF GAGANA BAAAAAAAAAAAAAA - clang -->
<!DOCTYPE html>
<html>
<head>
    <title>Student List</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Student List</h1>
    <table border="1" cellpadding="10">
        <thead>
            <tr>
                <th>Student ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Course</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($students as $student): ?>
                <tr>
                    <td><?= $student['id'] ?></td>
                    <td><?= $student['name'] ?></td>
                    <td><?= $student['email'] ?></td>
                    <td><?= $student['course_name'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
