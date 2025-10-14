<?php
require 'config.php'; // DB connection

if(isset($_GET['student_id'])){
    $student_id = $_GET['student_id'];

    // Check all approvals
    $stmt = $conn->prepare("SELECT * FROM approvals WHERE student_id=?");
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $approval = $result->fetch_assoc();

    if($approval['chairman_approved'] && $approval['provost_approved'] &&
       $approval['accountant_approved'] && $approval['register_approved'] &&
       $approval['controller_approved']) {

        // Generate admit card PDF (simple text file example)
        $stmt2 = $conn->prepare("SELECT * FROM students WHERE id=?");
        $stmt2->bind_param("i", $student_id);
        $stmt2->execute();
        $student = $stmt2->get_result()->fetch_assoc();

        $file_name = 'admit_card_'.$student['roll_no'].'.txt';
        $file_path = 'admit_cards/'.$file_name;
        file_put_contents($file_path, "Admit Card\nName: ".$student['name']."\nRoll: ".$student['roll_no']);

        // Save admit card info
        $stmt3 = $conn->prepare("INSERT INTO admit_cards (student_id, card_path) VALUES (?, ?)");
        $stmt3->bind_param("is", $student_id, $file_path);
        $stmt3->execute();

        echo "Admit card generated! <a href='$file_path' target='_blank'>Download</a>";

    } else {
        echo "Student approvals not complete yet!";
    }
} else {
    echo "Student ID missing!";
}
?>
