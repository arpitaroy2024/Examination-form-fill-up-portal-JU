<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] == 'student' || $_SESSION['role'] == 'admin') {
    header("Location: login.php");
    exit();
}

$role = $_SESSION['role'];
$sql = "SELECT ef.id, u.full_name, ef.reg_number, ef.submitted_at, ef.bank_name, ef.account_number, ef.branch 
        FROM exam_forms ef
        JOIN users u ON ef.user_id = u.id
        JOIN approvals a ON ef.id = a.form_id
        WHERE a.approver_role = ? AND a.status = 'pending'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $role);
$stmt->execute();
$result = $stmt->get_result();
$forms = $result->fetch_all(MYSQLI_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    $form_id = $_POST['form_id'];
    $status = $_POST['action'] == 'approve' ? 'approved' : 'rejected';
    $comments = sanitize($_POST['comments']);

    $update_sql = "UPDATE approvals SET status = ?, comments = ?, approved_at = NOW() WHERE form_id = ? AND approver_role = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("ssis", $status, $comments, $form_id, $role);
    $update_stmt->execute();

    header("Location: approver_dashboard.php?msg=Action taken");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approver Dashboard - JU Exam Portal</title>
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
                    <li class="nav-item"><a class="nav-link active" href="approver_dashboard.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h2>Approver Dashboard (<?php echo ucfirst($role); ?>)</h2>
        <?php if (isset($_GET['msg'])): ?>
            <div class="alert alert-success"><?php echo urldecode($_GET['msg']); ?></div>
        <?php endif; ?>
        <div class="card card-3d">
            <div class="card-body">
                <h5>Pending Forms</h5>
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Student Name</th>
                            <th>Registration Number</th>
                            <th>Bank Details</th>
                            <th>Submitted On</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($forms as $form): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($form['full_name']); ?></td>
                                <td><?php echo htmlspecialchars($form['reg_number']); ?></td>
                                <td><?php echo htmlspecialchars($form['bank_name'] . ', A/C: ' . $form['account_number'] . ', Branch: ' . $form['branch']); ?></td>
                                <td><?php echo date('Y-m-d', strtotime($form['submitted_at'])); ?></td>
                                <td>
                                    <form method="POST">
                                        <input type="hidden" name="form_id" value="<?php echo $form['id']; ?>">
                                        <textarea name="comments" class="form-control input-3d mb-2" placeholder="Comments"></textarea>
                                        <button name="action" value="approve" class="btn btn-success btn-3d btn-sm">Approve</button>
                                        <button name="action" value="reject" class="btn btn-danger btn-3d btn-sm">Reject</button>
                                    </form>
                                </td>
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