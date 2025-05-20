<?php
require_once 'db.php';
include 'header.php';

$pdo = getConnection();
$error = '';
$success = '';

if (!isset($_GET['id'])) {
    header("Location: students.php");
    exit;
}

$id = intval($_GET['id']);

// Fetch current student data
try {
    $stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
    $stmt->execute([$id]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$student) {
        $error = "Student not found.";
    }
} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate input
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');

    if (empty($name) || empty($email)) {
        $error = "Name and email cannot be empty.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE students SET name = ?, email = ? WHERE id = ?");
            $stmt->execute([$name, $email, $id]);
            $success = "Student updated successfully!";
            // Refresh student data
            $student['name'] = $name;
            $student['email'] = $email;
        } catch (PDOException $e) {
            $error = "Update failed: " . $e->getMessage();
        }
    }
}
?>

<div class="row">
    <div class="col-12">
        <h2>Edit Student</h2>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php elseif ($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <?php if ($student): ?>
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" id="name" name="name" class="form-control" value="<?= htmlspecialchars($student['name']) ?>" required>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" id="email" name="email" class="form-control" value="<?= htmlspecialchars($student['email']) ?>" required>
                </div>

                <button type="submit" class="btn btn-primary">Update</button>
                <a href="students.php" class="btn btn-secondary ms-2">Back to List</a>
            </form>
        <?php endif; ?>
    </div>
</div>

<?php include 'footer.php'; ?>