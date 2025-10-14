<?php
require_once 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = sanitize($_POST['full_name']);
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $student_id = sanitize($_POST['student_id']);
    $session = sanitize($_POST['session']);
    $department = sanitize($_POST['department']);
    $hall = sanitize($_POST['hall']);
    $phone = sanitize($_POST['phone']);
    $bank_name = sanitize($_POST['bank_name']);
    $account_number = sanitize($_POST['account_number']);
    $branch = sanitize($_POST['branch']);

    // Validate inputs
    if ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } elseif (empty($full_name) || empty($email) || empty($password) || empty($student_id) || empty($session) || empty($department) || empty($hall) || empty($phone) || empty($bank_name) || empty($account_number) || empty($branch)) {
        $error = "All fields are required.";
    } else {
        // Check if email already exists
        $check_sql = "SELECT id FROM users WHERE email = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("s", $email);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            $error = "This email is already registered. Please use a different email.";
        } else {
            // Proceed with registration
            $password = hashPassword($password);
            $sql = "INSERT INTO users (full_name, email, password, role, student_id, session, department, hall, phone, bank_name, account_number, branch) VALUES (?, ?, ?, 'student', ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssssssssss", $full_name, $email, $password, $student_id, $session, $department, $hall, $phone, $bank_name, $account_number, $branch);

            if ($stmt->execute()) {
                header("Location: login.php?msg=Registration successful!");
                exit();
            } else {
                $error = "Error: " . $stmt->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registration - JU Exam Portal</title>
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
                    <li class="nav-item"><a class="nav-link" href="index.html">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
                    <li class="nav-item"><a class="nav-link active" href="register_student.php">Register</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card card-3d">
                    <div class="card-header">
                        <h3>Student Registration</h3>
                    </div>
                    <div class="card-body">
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                        <?php endif; ?>
                        <form method="POST" action="register_student.php">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="full_name" class="form-label">Full Name</label>
                                    <input type="text" class="form-control input-3d" id="full_name" name="full_name" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email Address</label>
                                    <input type="email" class="form-control input-3d" id="email" name="email" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" class="form-control input-3d" id="password" name="password" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="confirm_password" class="form-label">Confirm Password</label>
                                    <input type="password" class="form-control input-3d" id="confirm_password" name="confirm_password" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="student_id" class="form-label">Student ID / Registration Number</label>
                                    <input type="text" class="form-control input-3d" id="student_id" name="student_id" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="session" class="form-label">Session</label>
                                    <input type="text" class="form-control input-3d" id="session" name="session" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="department" class="form-label">Department</label>
                                    <input type="text" class="form-control input-3d" id="department" name="department" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="hall" class="form-label">Hall</label>
                                    <input type="text" class="form-control input-3d" id="hall" name="hall" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label">Phone Number</label>
                                    <input type="tel" class="form-control input-3d" id="phone" name="phone" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="bank_name" class="form-label">Bank Name</label>
                                    <input type="text" class="form-control input-3d" id="bank_name" name="bank_name" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="account_number" class="form-label">Account Number</label>
                                    <input type="text" class="form-control input-3d" id="account_number" name="account_number" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="branch" class="form-label">Branch</label>
                                    <input type="text" class="form-control input-3d" id="branch" name="branch" required>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary btn-3d w-100">Register</button>
                        </form>
                        <p class="mt-3 text-center">Already have an account? <a href="login.php">Login</a> | <a href="register_staff.php">Register as Staff</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="bg-dark text-white text-center py-4 mt-5">
        <p>&copy; 2025 Jahangirnagar University Exam Portal. All rights reserved.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>