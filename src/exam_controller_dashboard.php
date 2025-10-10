<?php
require_once 'config.php';
require_once 'auth.php';
require_once 'functions.php';

requireLogin();
if (!hasRole('exam_controller')) {
    redirectTo('../public/login.html');
}

// Fetch stats
$totalForms = $pdo->query("SELECT COUNT(*) FROM exam_forms")->fetchColumn();
$pendingForms = $pdo->query("SELECT COUNT(*) FROM exam_forms WHERE status='pending'")->fetchColumn();
$approvedForms = $pdo->query("SELECT COUNT(*) FROM exam_forms WHERE status='approved'")->fetchColumn();
$admitGenerated = $pdo->query("SELECT COUNT(*) FROM exam_forms WHERE admit_generated=1")->fetchColumn();

// Fetch recent forms
$stmt = $pdo->query("
    SELECT ef.*, u.full_name, u.department, h.hall_name
    FROM exam_forms ef
    JOIN users u ON ef.student_id = u.id
    LEFT JOIN halls h ON u.hall_id = h.id
    ORDER BY ef.created_at DESC
    LIMIT 10
");
$forms = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle Approve / Reject
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formId = $_POST['form_id'] ?? null;
    $action = $_POST['action'] ?? null;

    if ($formId && $action) {
        if ($action === 'approve') {
            $update = $pdo->prepare("UPDATE exam_forms SET status='approved' WHERE id=?");
            $update->execute([$formId]);
        } elseif ($action === 'reject') {
            $update = $pdo->prepare("UPDATE exam_forms SET status='rejected' WHERE id=?");
            $update->execute([$formId]);
        }
        header("Location: exam_controller_dashboard.php");
        exit;
    }
}

// Handle Admit Card Generation
if (isset($_POST['generate_admit'])) {
    $dept = $_POST['department'];
    $session = $_POST['session'];
    $semester = $_POST['semester'];

    $update = $pdo->prepare("
        UPDATE exam_forms 
        SET admit_generated=1 
        WHERE department=? AND session_year=? AND semester=? AND status='approved'
    ");
    $update->execute([$dept, $session, $semester]);

    $message = "Admit cards generated successfully for $dept ($session - $semester)";
}
?>
