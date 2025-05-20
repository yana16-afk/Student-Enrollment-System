<?php
require_once 'db.php';
include 'header.php';

$pdo = getConnection();
$error = '';
$message = '';

// Handle delete action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $delete_id = intval($_POST['delete_id']);
    try {
        $delStmt = $pdo->prepare("DELETE FROM students WHERE id = ?");
        $delStmt->execute([$delete_id]);

        if ($delStmt->rowCount() > 0) {
            $message = "Student deleted successfully.";
        } else {
            $error = "Failed to delete student or student not found.";
        }
    } catch (PDOException $e) {
        $error = "Database error: " . $e->getMessage();
    }
}

// Fetch all students with course names
try {
    $stmt = $pdo->query("
        SELECT s.id, s.name, s.email, c.course_name 
        FROM students s
        LEFT JOIN courses c ON s.course_id = c.id
        ORDER BY s.name ASC
    ");
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
}
?>

<div class="row">
    <div class="col-12">
        <h2>Students List</h2>

        <?php if ($message): ?>
            <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if (count($students) === 0): ?>
            <p>No students found.</p>
        <?php else: ?>
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Course</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($students as $student): ?>
                        <tr>
                            <td><?= htmlspecialchars($student['name']) ?></td>
                            <td><?= htmlspecialchars($student['email']) ?></td>
                            <td><?= htmlspecialchars($student['course_name'] ?? 'Not enrolled') ?></td>
                            <td>
                                <form method="POST" onsubmit="return confirm('Are you sure you want to delete this student?');">
                                    <input type="hidden" name="delete_id" value="<?= $student['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <a href="add_student.php" class="btn btn-primary">Add New Student</a>
    </div>
</div>

<?php include 'footer.php'; ?>
