<?php
session_start();
require 'config.php';

// Check student session
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    die("Access denied");
}

$student_id = $_SESSION['user_id'];

// Get student's exam form
$sql_form = "SELECT * FROM exam_forms WHERE user_id = ?";
$stmt = $conn->prepare($sql_form);
if(!$stmt){
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result_form = $stmt->get_result();

if($result_form->num_rows == 0){
    die("No exam form found.");
}

$form = $result_form->fetch_assoc();

// Check approvals
$sql_approval = "SELECT approver_role, status FROM approvals WHERE form_id = ?";
$stmt2 = $conn->prepare($sql_approval);
if(!$stmt2){
    die("Prepare failed: " . $conn->error);
}
$stmt2->bind_param("i", $form['id']);
$stmt2->execute();
$result_approval = $stmt2->get_result();

$approved_roles = [];
while($row = $result_approval->fetch_assoc()){
    if($row['status'] === 'approved'){
        $approved_roles[] = $row['approver_role'];
    }
}

// Define required approvers
$required_roles = ['chairman','provost','accountant','registrar','controller'];

// Check if fully approved
$fully_approved = count(array_diff($required_roles, $approved_roles)) === 0;

if(!$fully_approved){
    die("Your form is not fully approved yet.");
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Admit Card</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .admit-card {
            border: 2px solid #4b0082;
            padding: 20px;
            max-width: 600px;
            margin: 50px auto;
            border-radius: 10px;
            background: #f9f9ff;
        }
        .admit-card h2 {
            text-align: center;
            color: #4b0082;
            margin-bottom: 30px;
        }
        .admit-card p {
            font-size: 16px;
            margin: 5px 0;
        }
        .download-btn {
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div class="admit-card">
    <h2>Admit Card</h2>
    <p><b>Name:</b> <?= htmlspecialchars($form['student_name']) ?></p>
    <p><b>Roll No:</b> <?= htmlspecialchars($form['class_roll']) ?></p>
    <p><b>Session:</b> <?= htmlspecialchars($form['session']) ?></p>
    <p><b>Department:</b> <?= htmlspecialchars($form['department']) ?></p>
    <p><b>Hall:</b> <?= htmlspecialchars($form['hall_name']) ?></p>
    <p><b>Bank:</b> <?= htmlspecialchars($form['bank_name']) ?></p>
    <p><b>Account No:</b> <?= htmlspecialchars($form['account_number']) ?></p>
    <p><b>Branch:</b> <?= htmlspecialchars($form['branch']) ?></p>

    <div class="download-btn">
        <button onclick="window.print()" class="btn btn-success">Download / Print PDF</button>
    </div>
</div>

</body>
</html>
