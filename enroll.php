<?php
require_once 'db.php';
include 'header.php';

$pdo = getConnection();
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['enroll'])) {
    $student_id = trim($_POST['student_id']);
    $course_id = trim($_POST['course_id']);

    if (empty($student_id)) {
        $error = "Please select a student.";
    } elseif (empty($course_id)) {
        $error = "Please select a course.";
    } else {
        try {
            $checkStmt = $pdo->prepare("SELECT * FROM student_courses WHERE student_id = ? AND course_id = ?");
            $checkStmt->execute([$student_id, $course_id]);
            
            if ($checkStmt->rowCount() > 0) {
                $error = "Student is already enrolled in this course.";
            } else {

                $stmt = $pdo->prepare("INSERT INTO student_courses (student_id, course_id) VALUES (?, ?)");
                $stmt->execute([$student_id, $course_id]);
                
                if ($stmt->rowCount() > 0) {
                    $message = "Student successfully enrolled in the course!";
                } else {
                    $error = "Failed to enroll student. Please try again.";
                }
            }
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['unenroll'])) {
    $enrollment_id = trim($_POST['enrollment_id']);
    
    if (empty($enrollment_id)) {
        $error = "Invalid enrollment selection.";
    } else {
        try {
   
            $stmt = $pdo->prepare("DELETE FROM student_courses WHERE id = ?");
            $stmt->execute([$enrollment_id]);
            
            if ($stmt->rowCount() > 0) {
                $message = "Student successfully unenrolled from the course!";
            } else {
                $error = "Failed to unenroll student. Please try again.";
            }
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}


$students = $pdo->query("
    SELECT * FROM students
    ORDER BY name
")->fetchAll();


$courses = $pdo->query("
    SELECT * FROM courses 
    ORDER BY course_name
")->fetchAll();

$enrollment_stats = $pdo->query("
    SELECT c.id, c.course_name, COUNT(sc.id) as student_count
    FROM courses c
    LEFT JOIN student_courses sc ON c.id = sc.course_id
    GROUP BY c.id, c.course_name
    ORDER BY c.course_name
")->fetchAll();

$student_enrollments = $pdo->query("
    SELECT sc.id as enrollment_id, s.id as student_id, s.name as student_name, 
           c.id as course_id, c.course_name
    FROM student_courses sc
    JOIN students s ON sc.student_id = s.id
    JOIN courses c ON sc.course_id = c.id
    ORDER BY s.name, c.course_name
")->fetchAll();
?>

<div class="row">
    <div class="col-12">
        <h2 class="mb-4">Student Enrollment</h2>
        
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
    <!-- Enrollment Form -->
    <div class="col-md-6">
        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Enroll Student in Course</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="enroll.php">
                    <div class="mb-3">
                        <label for="student_id" class="form-label">Select Student</label>
                        <select name="student_id" id="student_id" class="form-select" required>
                            <option value="">-- Select Student --</option>
                            <?php foreach ($students as $student): ?>
                                <option value="<?= htmlspecialchars($student['id']) ?>">
                                    <?= htmlspecialchars($student['name']) ?> 
                                    (<?= htmlspecialchars($student['id']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="course_id" class="form-label">Select Course</label>
                        <select name="course_id" id="course_id" class="form-select" required>
                            <option value="">-- Select Course --</option>
                            <?php foreach ($courses as $course): ?>
                                <option value="<?= htmlspecialchars($course['id']) ?>">
                                    <?= htmlspecialchars($course['course_name']) ?> 
                                    (<?= htmlspecialchars($course['id']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <button type="submit" name="enroll" class="btn btn-primary">Enroll Student</button>
                </form>
            </div>
        </div>
        
        <!-- Manage Enrollments -->
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="card-title mb-0">Manage Student Enrollments</h5>
            </div>
            <div class="card-body">
                <?php if (count($student_enrollments) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Student</th>
                                    <th>Course</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($student_enrollments as $enrollment): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($enrollment['student_name']) ?></td>
                                        <td><?= htmlspecialchars($enrollment['course_name']) ?></td>
                                        <td>
                                            <form method="POST" action="enroll.php" onsubmit="return confirm('Are you sure you want to unenroll this student?');">
                                                <input type="hidden" name="enrollment_id" value="<?= htmlspecialchars($enrollment['enrollment_id']) ?>">
                                                <button type="submit" name="unenroll" class="btn btn-danger btn-sm">Unenroll</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted">No enrollments found.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Course Enrollment List -->
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="card-title mb-0">Course Enrollment Summary</h5>
            </div>
            <div class="card-body">
                <div class="accordion" id="courseEnrollmentsAccordion">
                    <?php foreach ($enrollment_stats as $index => $course): ?>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading<?= $index ?>">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" 
                                        data-bs-target="#collapse<?= $index ?>" aria-expanded="false" 
                                        aria-controls="collapse<?= $index ?>">
                                    <?= htmlspecialchars($course['course_name']) ?> 
                                    <span class="badge bg-secondary ms-2"><?= $course['student_count'] ?> student(s)</span>
                                </button>
                            </h2>
                            <div id="collapse<?= $index ?>" class="accordion-collapse collapse" 
                                 aria-labelledby="heading<?= $index ?>" data-bs-parent="#courseEnrollmentsAccordion">
                                <div class="accordion-body">
                                    <?php
                                    $enrolled_students = $pdo->prepare("
                                        SELECT s.* FROM students s
                                        JOIN student_courses sc ON s.id = sc.student_id
                                        WHERE sc.course_id = ? 
                                        ORDER BY s.name
                                    ");
                                    $enrolled_students->execute([$course['id']]);
                                    $students_list = $enrolled_students->fetchAll();
                                    
                                    if (count($students_list) > 0):
                                    ?>
                                        <ul class="list-group">
                                            <?php foreach ($students_list as $student): ?>
                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                    <?= htmlspecialchars($student['name']) ?>
                                                    <span class="text-muted small"><?= htmlspecialchars($student['id']) ?></span>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php else: ?>
                                        <p class="text-muted">No students enrolled in this course yet.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>