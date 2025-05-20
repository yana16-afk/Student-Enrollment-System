<?php
require_once 'db.php';
include 'header.php';

$pdo = getConnection();
$error = '';

// Handle deletion if POST with id is received
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);

    try {
        $pdo->beginTransaction();

        // Delete from student_courses first
        $stmt1 = $pdo->prepare("DELETE FROM student_courses WHERE student_id = ?");
        $stmt1->execute([$id]);

        // Delete from students table
        $stmt2 = $pdo->prepare("DELETE FROM students WHERE id = ?");
        $stmt2->execute([$id]);

        $pdo->commit();

        // Redirect to avoid resubmission
        header("Location: students.php");
        exit;
    } catch (PDOException $e) {
        $pdo->rollBack();
        $error = "Error deleting student: " . $e->getMessage();
    }
}

try {
    // Fetch all students
    $stmt = $pdo->query("SELECT id, name, email FROM students ORDER BY name ASC");
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch enrolled courses grouped by student_id
    $stmt = $pdo->query("
        SELECT sc.student_id, GROUP_CONCAT(CONCAT(c.id, ' (', c.course_name, ')') SEPARATOR ', ') AS courses
        FROM student_courses sc
        JOIN courses c ON sc.course_id = c.id
        GROUP BY sc.student_id
    ");
    $studentCourses = $stmt->fetchAll(PDO::FETCH_KEY_PAIR); // student_id => courses string
} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
}
?>

<div class="row">
    <div class="col-12">
        <h2>Students List</h2>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if (count($students) === 0): ?>
            <p>No students found.</p>
        <?php else: ?>
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Courses Enrolled</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($students as $student): ?>
                        <tr>
                            <td><?= htmlspecialchars($student['id']) ?></td>
                            <td><?= htmlspecialchars($student['name']) ?></td>
                            <td><?= htmlspecialchars($student['email']) ?></td>
                            <td><?= htmlspecialchars($studentCourses[$student['id']] ?? 'Not enrolled') ?></td>

                            <td>
                            <div class="d-flex gap-2">
                                <a href="edit_student.php?id=<?= htmlspecialchars($student['id']) ?>" class="btn btn-sm btn-warning">Edit</a>
                                <form action="students.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this student?');">
                                <input type="hidden" name="id" value="<?= htmlspecialchars($student['id']) ?>">
                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                </form>
                            </div>
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