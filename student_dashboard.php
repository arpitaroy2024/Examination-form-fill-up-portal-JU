<?php
session_start();
require 'config.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - JU Exam Portal</title>
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
                    <li class="nav-item"><a class="nav-link active" href="student_dashboard.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="form_submit.php">New Form</a></li>
                    <li class="nav-item"><a class="nav-link" href="status.php">Status</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h2>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h2>
        <div class="row">
            <div class="col-md-4">
                <div class="card card-3d">
                    <div class="card-body text-center">
                        <h5 class="card-title">Start New Form</h5>
                        <p class="card-text">Fill out a new examination form.</p>
                        <a href="form_submit.php" class="btn btn-primary btn-3d">Go</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-3d">
                    <div class="card-body text-center">
                        <h5 class="card-title">View Drafts</h5>
                        <p class="card-text">Edit or submit saved drafts.</p>
                        <a href="status.php" class="btn btn-primary btn-3d">Go</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-3d">
                    <div class="card-body text-center">
                        <h5 class="card-title">Track Progress</h5>
                        <p class="card-text">Check the status of your forms.</p>
                        <a href="status.php" class="btn btn-primary btn-3d">Go</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

             <div class="col-md-4">
                <div class="card card-3d">
                    <div class="card-body text-center">
                        <h5 class="card-title">Download Admit Card</h5>
                        <p class="card-text">Check the status of your admit.</p>
                        <a href="download.php" class="btn btn-primary btn-3d">Go</a>
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