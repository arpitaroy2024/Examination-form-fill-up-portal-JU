<?php
require 'config.php';

if (!isset($_GET['student_id'])) {
    die("Student ID not provided");
}

$student_id = $_GET['student_id'];

// Make sure SQL is correct
$sql = "SELECT f.student_name, f.class_roll, f.session, f.department, f.hall_name, 
               u.bank_name, u.account_number, u.branch
        FROM exam_forms f
        LEFT JOIN users u ON f.user_id = u.id
        WHERE f.id = ?";

// Prepare statement
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo "<h4>Admit Card</h4>";
    echo "Name: " . htmlspecialchars($row['student_name']) . "<br>";
    echo "Roll No: " . htmlspecialchars($row['class_roll']) . "<br>";
    echo "Session: " . htmlspecialchars($row['session']) . "<br>";
    echo "Department: " . htmlspecialchars($row['department']) . "<br>";
    echo "Hall: " . htmlspecialchars($row['hall_name']) . "<br>";
    echo "Bank: " . htmlspecialchars($row['bank_name']) . "<br>";
    echo "Account No: " . htmlspecialchars($row['account_number']) . "<br>";
    echo "Branch: " . htmlspecialchars($row['branch']) . "<br>";
} else {
    echo "No data found for this student.";
}

$stmt->close();
$conn->close();
?>
