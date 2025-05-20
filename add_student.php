<?php
require_once 'db.php';
include 'header.php';

$pdo = getConnection();
$message = '';
$error = '';

// Fetch courses for dropdown
try {
    $coursesStmt = $pdo->query("SELECT id, course_name FROM courses");
    $courses = $coursesStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Could not fetch courses: " . $e->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $course_id = $_POST['course_id'] ?? null;

    if (empty($name)) {
        $error = "Please enter the student name.";
    } elseif (empty($email)) {
        $error = "Please enter the student email.";
    } else {
        try {
            // Insert with course_id as NULL if no course selected
            $stmt = $pdo->prepare("INSERT INTO students (name, email, course_id) VALUES (?, ?, ?)");
            $stmt->execute([$name, $email, $course_id ?: null]);

            if ($stmt->rowCount() > 0) {
                $message = "Student successfully added!";
                // Clear form fields if you want
                $name = $email = '';
                $course_id = null;
            } else {
                $error = "Failed to add student. Please try again.";
            }
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}
?>

<div class="row">
    <div class="col-12">
        <h2>Add New Student</h2>

        <?php if ($message): ?>
            <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="add_student.php">
            <div class="mb-3">
                <label for="name" class="form-label">Student Name</label>
                <input type="text" name="name" id="name" class="form-control" required value="<?= isset($name) ? htmlspecialchars($name) : '' ?>">
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" id="email" class="form-control" required value="<?= isset($email) ? htmlspecialchars($email) : '' ?>">
            </div>

            <!-- <div class="mb-3">
                <label for="course_id" class="form-label">Course</label>
                <select name="course_id" id="course_id" class="form-select">
                    <option value="">-- Select Course (optional) --</option>
                    <?php foreach ($courses as $course): ?>
                        <option value="<?= htmlspecialchars($course['id']) ?>" <?= (isset($course_id) && $course_id === $course['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($course['course_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div> -->

            <button type="submit" class="btn btn-primary">Add Student</button>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>
