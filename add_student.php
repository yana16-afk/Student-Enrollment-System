<?php
require 'includes/db.php';
include 'includes/header.php';

$errors = [];
$name = $email = $course_id = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars(trim($_POST['name']));
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $course_id = intval($_POST['course_id']);

    if (empty($name)) $errors[] = "Name Field must be filled.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid Email.";

    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO students (name, email, course_id) VALUES (?, ?, ?)");
        $stmt->execute([$name, $email, $course_id ?: null]);
        header("Location: students.php");
    }
}

$courses = $pdo->query("SELECT * FROM courses")->fetchAll();
?>

