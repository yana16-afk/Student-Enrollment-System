<?php
require_once 'db.php';
include 'header.php';

$pdo = getConnection();
$message = '';
$error = '';

// Handle adding a new course
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_course'])) {
    $course_name = trim($_POST['course_name']);
    
    if (empty($course_name)) {
        $error = "Course name cannot be empty.";
    } else {
        try {
            // Check duplicate
            $checkStmt = $pdo->prepare("SELECT * FROM courses WHERE course_name = ?");
            $checkStmt->execute([$course_name]);
            if ($checkStmt->rowCount() > 0) {
                $error = "Course already exists.";
            } else {
                $stmt = $pdo->prepare("INSERT INTO courses (course_name) VALUES (?)");
                $stmt->execute([$course_name]);
                if ($stmt->rowCount() > 0) {
                    $message = "Course added successfully!";
                } else {
                    $error = "Failed to add course. Please try again.";
                }
            }
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}

// Handle delete course
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_course'])) {
    $course_id = trim($_POST['course_id']);
    if (empty($course_id)) {
        $error = "Invalid course selection.";
    } else {
        try {
            // First, delete enrollments for this course to maintain referential integrity
            $pdo->prepare("DELETE FROM student_courses WHERE course_id = ?")->execute([$course_id]);
            $stmt = $pdo->prepare("DELETE FROM courses WHERE id = ?");
            $stmt->execute([$course_id]);
            if ($stmt->rowCount() > 0) {
                $message = "Course deleted successfully!";
            } else {
                $error = "Failed to delete course. Please try again.";
            }
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}

$courses = $pdo->query("SELECT * FROM courses ORDER BY course_name")->fetchAll();
?>

<div class="row">
    <div class="col-12">
        <h2 class="mb-4">Courses Management</h2>
        
        <?php if ($message): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($message) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($error) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="row">
    <!-- Add Course Form -->
    <div class="col-md-5">
        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Add New Course</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="courses.php">
                    <div class="mb-3">
                        <label for="course_name" class="form-label">Course Name</label>
                        <input type="text" name="course_name" id="course_name" class="form-control" required>
                    </div>
                    <button type="submit" name="add_course" class="btn btn-primary">Add Course</button>
                </form>
            </div>
        </div>
    </div>
    
    <!-- List Courses -->
    <div class="col-md-7">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="card-title mb-0">Existing Courses</h5>
            </div>
            <div class="card-body">
                <?php if (count($courses) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Course Name</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($courses as $course): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($course['id']) ?></td>
                                        <td><?= htmlspecialchars($course['course_name']) ?></td>
                                        <td>
                                            <form method="POST" action="courses.php" 
                                                  onsubmit="return confirm('Are you sure you want to delete this course?');" 
                                                  style="display:inline;">
                                                <input type="hidden" name="course_id" value="<?= htmlspecialchars($course['id']) ?>">
                                                <button type="submit" name="delete_course" class="btn btn-danger btn-sm">Delete</button>
                                            </form>
                                            <!-- For edit functionality, you could link to edit_course.php?id= -->
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted">No courses found.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
