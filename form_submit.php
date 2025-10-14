<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $student_name = sanitize($_POST['student_name']);
    $father_name = sanitize($_POST['father_name']);
    $mother_name = sanitize($_POST['mother_name']);
    $class_roll = sanitize($_POST['class_roll']);
    $reg_number = sanitize($_POST['reg_number']);
    $address = sanitize($_POST['address']);
    $session = sanitize($_POST['session']);
    $department = sanitize($_POST['department']);
    $hall_name = sanitize($_POST['hall_name']);
    $subject_list = sanitize($_POST['subject_list']);
    $failed_exams = sanitize($_POST['failed_exams']);
    $semester = sanitize($_POST['semester']);
    $bank_name = sanitize($_POST['bank_name']);
    $account_number = sanitize($_POST['account_number']);
    $branch = sanitize($_POST['branch']);

    if (empty($student_name) || empty($father_name) || empty($mother_name) || empty($class_roll) || empty($reg_number) || empty($address) || empty($session) || empty($department) || empty($hall_name) || empty($subject_list) || empty($semester) || empty($bank_name) || empty($account_number) || empty($branch)) {
        $error = "All required fields must be filled.";
    } else {
        $upload_dir = 'uploads/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $photo_path = '';
        $receipt_path = '';
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
            $photo_path = $upload_dir . basename($_FILES['photo']['name']);
            move_uploaded_file($_FILES['photo']['tmp_name'], $photo_path);
        }
        if (isset($_FILES['receipt']) && $_FILES['receipt']['error'] == 0) {
            $receipt_path = $upload_dir . basename($_FILES['receipt']['name']);
            move_uploaded_file($_FILES['receipt']['tmp_name'], $receipt_path);
        }

        $sql = "INSERT INTO exam_forms (user_id, student_name, father_name, mother_name, class_roll, reg_number, address, session, department, hall_name, subject_list, failed_exams, semester, photo_path, receipt_path, bank_name, account_number, branch, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'submitted')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isssssssssssssssss", $user_id, $student_name, $father_name, $mother_name, $class_roll, $reg_number, $address, $session, $department, $hall_name, $subject_list, $failed_exams, $semester, $photo_path, $receipt_path, $bank_name, $account_number, $branch);

        if ($stmt->execute()) {
            $form_id = $conn->insert_id;
            $approver_roles = ['chairman', 'provost', 'accountant', 'registrar', 'controller'];
            foreach ($approver_roles as $role) {
                $approval_sql = "INSERT INTO approvals (form_id, approver_role) VALUES (?, ?)";
                $approval_stmt = $conn->prepare($approval_sql);
                $approval_stmt->bind_param("is", $form_id, $role);
                $approval_stmt->execute();
            }
            header("Location: status.php?msg=Form submitted successfully! ID: " . $form_id);
        } else {
            $error = "Error submitting form: " . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Examination Form - JU Exam Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary navbar-3d">
        <div class="container">
            <a class="navbar-brand" href="index.html">JU Exam Portal</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="student_dashboard.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link active" href="form_submit.php">New Form</a></li>
                    <li class="nav-item"><a class="nav-link" href="status.php">Status</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h2>Examination Form</h2>
        <div class="card card-3d">
            <div class="card-body">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                <form method="POST" action="form_submit.php" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="student_name" class="form-label">Student Name</label>
                            <input type="text" class="form-control input-3d" id="student_name" name="student_name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="father_name" class="form-label">Father's Name</label>
                            <input type="text" class="form-control input-3d" id="father_name" name="father_name" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="mother_name" class="form-label">Mother's Name</label>
                            <input type="text" class="form-control input-3d" id="mother_name" name="mother_name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="class_roll" class="form-label">Class Roll</label>
                            <input type="text" class="form-control input-3d" id="class_roll" name="class_roll" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="reg_number" class="form-label">Registration Number</label>
                            <input type="text" class="form-control input-3d" id="reg_number" name="reg_number" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="address" class="form-label">Address</label>
                            <input type="text" class="form-control input-3d" id="address" name="address" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="session" class="form-label">Session</label>
                            <input type="text" class="form-control input-3d" id="session" name="session" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="department" class="form-label">Department</label>
                            <input type="text" class="form-control input-3d" id="department" name="department" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="hall_name" class="form-label">Hall Name</label>
                            <input type="text" class="form-control input-3d" id="hall_name" name="hall_name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="subject_list" class="form-label">Subject List</label>
                            <textarea class="form-control input-3d" id="subject_list" name="subject_list" required></textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="failed_exams" class="form-label">Failed in Previous Exams</label>
                            <input type="text" class="form-control input-3d" id="failed_exams" name="failed_exams">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="semester" class="form-label">Semester/Year</label>
                            <input type="text" class="form-control input-3d" id="semester" name="semester" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="bank_name" class="form-label">Bank Name</label>
                            <input type="text" class="form-control input-3d" id="bank_name" name="bank_name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="account_number" class="form-label">Account Number</label>
                            <input type="text" class="form-control input-3d" id="account_number" name="account_number" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="branch" class="form-label">Branch</label>
                            <input type="text" class="form-control input-3d" id="branch" name="branch" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="photo" class="form-label">Photo Upload</label>
                            <input type="file" class="form-control input-3d" id="photo" name="photo" accept="image/*">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="receipt" class="form-label">Payment Receipt Upload</label>
                            <input type="file" class="form-control input-3d" id="receipt" name="receipt" accept="image/*,application/pdf">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success btn-3d">Submit Form</button>
                    <a href="student_dashboard.php" class="btn btn-secondary btn-3d ms-2">Cancel</a>
                </form>
            </div>
        </div>
    </div>

    <footer class="bg-dark text-white text-center py-4 mt-5">
        <p>&copy; 2025 Jahangirnagar University Exam Portal. All rights reserved.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>