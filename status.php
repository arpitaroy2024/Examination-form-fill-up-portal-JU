<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT ef.*, GROUP_CONCAT(a.approver_role, ':', a.status SEPARATOR '|') as approvals 
        FROM exam_forms ef 
        LEFT JOIN approvals a ON ef.id = a.form_id 
        WHERE ef.user_id = ? 
        GROUP BY ef.id 
        ORDER BY ef.submitted_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$forms = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Status - JU Exam Portal</title>
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
                    <li class="nav-item"><a class="nav-link" href="form_submit.php">New Form</a></li>
                    <li class="nav-item"><a class="nav-link active" href="status.php">Status</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h2>Form Status</h2>
        <?php if (isset($_GET['msg'])): ?>
            <div class="alert alert-success"><?php echo urldecode($_GET['msg']); ?></div>
        <?php endif; ?>
        <div class="card card-3d">
            <div class="card-body">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Form ID</th>
                            <th>Submitted</th>
                            <th>Status</th>
                            <th>Approvals</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($forms as $form): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($form['id']); ?></td>
                                <td><?php echo date('Y-m-d', strtotime($form['submitted_at'])); ?></td>
                                <td><span class="badge bg-<?php echo $form['status'] == 'final_approved' ? 'success' : ($form['status'] == 'submitted' ? 'warning' : 'secondary'); ?>"><?php echo ucfirst(str_replace('_', ' ', $form['status'])); ?></span></td>
                                <td><?php echo htmlspecialchars($form['approvals'] ?: 'No approvals yet'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <footer class="bg-dark text-white text-center py-4 mt-5">
        <p>&copy; 2025 Jahangirnagar University Exam Portal. All rights reserved.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>