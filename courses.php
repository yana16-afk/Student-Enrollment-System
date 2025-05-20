<?php
require_once 'db.php';
$conn = getConnection();
//DI KO PA NACHECHECK IF GUMAGANAAA AHHHHHHHHH!!!!!!!!
// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $id = strtoupper(trim($_POST['id']));
        $course_name = trim($_POST['course_name']);

        if ($id && $course_name) {
            $stmt = $conn->prepare("INSERT INTO courses (id, course_name) VALUES (?, ?)");
            $stmt->execute([$id, $course_name]);
        }
    }

    if ($action === 'update') {
        $id = trim($_POST['id']);
        $course_name = trim($_POST['course_name']);

        if ($id && $course_name) {
            $stmt = $conn->prepare("UPDATE courses SET course_name = ? WHERE id = ?");
            $stmt->execute([$course_name, $id]);
        }
    }

    if ($action === 'delete') {
        $id = trim($_POST['id']);

        if ($id) {
            $stmt = $conn->prepare("DELETE FROM courses WHERE id = ?");
            $stmt->execute([$id]);
        }
    }
}

// Fetch all courses
$stmt = $conn->query("SELECT * FROM courses");
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
