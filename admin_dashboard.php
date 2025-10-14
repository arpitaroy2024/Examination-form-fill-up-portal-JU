<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";     // XAMPP default
$password = "";         // XAMPP default
$database = "ju_exam_portal";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard - JU Exam Portal</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<style>
body {
    background: linear-gradient(to right, #f5f7fa, #c3cfe2);
    font-family: 'Poppins', sans-serif;
}
h2 {
    color: #4b0082;
    margin-bottom: 30px;
    text-align: center;
}
.table th {
    background-color: #6a1b9a;
    color: white;
    text-align: center;
    font-size: 1rem;
}
.table td {
    text-align: center;
    font-weight: 500;
    vertical-align: middle;
}
.table-striped tbody tr:nth-of-type(odd) {
    background-color: #e1bee7;
}
.table-hover tbody tr:hover {
    background-color: #d1c4e9;
    cursor: pointer;
}
.generate-btn {
    background: linear-gradient(to right, #00c853, #b2ff59);
    border: none;
    color: #fff;
    font-weight: 600;
}
.generate-btn:hover {
    background: linear-gradient(to right, #b2ff59, #00c853);
    color: #000;
    transition: 0.3s;
}
.pending-btn {
    background: linear-gradient(to right, #f44336, #ff7961);
    border: none;
    color: #fff;
    font-weight: 600;
}
.pending-btn:hover {
    background: linear-gradient(to right, #ff7961, #f44336);
    color: #000;
    transition: 0.3s;
}
.modal-header {
    background: #6a1b9a;
    color: white;
}
</style>
</head>
<body>

<div class="container mt-5">
    <h2>Student Approvals</h2>
    <table class="table table-bordered table-striped table-hover mt-3">
        <thead>
            <tr>
                <th>Roll No</th>
                <th>Name</th>
                <th>Chairman</th>
                <th>Provost</th>
                <th>Accountant</th>
                <th>Registrar</th>
                <th>Controller</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Fetch all exam forms
            $sql = "SELECT id AS form_id, student_name, class_roll FROM exam_forms ORDER BY submitted_at DESC";
            $result = $conn->query($sql);

            if (!$result) {
                die("SQL Error: " . $conn->error);
            }

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $form_id = $row['form_id'];

                    // Get approvals for this form
                    $approval_sql = "SELECT approver_role, status FROM approvals WHERE form_id = $form_id";
                    $approval_result = $conn->query($approval_sql);

                    $statuses = [
                        'chairman' => 'pending',
                        'provost' => 'pending',
                        'accountant' => 'pending',
                        'registrar' => 'pending',
                        'controller' => 'pending'
                    ];

                    if ($approval_result && $approval_result->num_rows > 0) {
                        while ($appr = $approval_result->fetch_assoc()) {
                            $statuses[$appr['approver_role']] = $appr['status'];
                        }
                    }

                    $all_approved = array_reduce($statuses, function($carry, $s){
                        return $carry && ($s === 'approved');
                    }, true);
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['class_roll']); ?></td>
                        <td><?php echo htmlspecialchars($row['student_name']); ?></td>
                        <td><?php echo $statuses['chairman']==='approved'?"✅":($statuses['chairman']==='rejected'?"❌":"⏳"); ?></td>
                        <td><?php echo $statuses['provost']==='approved'?"✅":($statuses['provost']==='rejected'?"❌":"⏳"); ?></td>
                        <td><?php echo $statuses['accountant']==='approved'?"✅":($statuses['accountant']==='rejected'?"❌":"⏳"); ?></td>
                        <td><?php echo $statuses['registrar']==='approved'?"✅":($statuses['registrar']==='rejected'?"❌":"⏳"); ?></td>
                        <td><?php echo $statuses['controller']==='approved'?"✅":($statuses['controller']==='rejected'?"❌":"⏳"); ?></td>
                        <td>
                            <?php
                            if ($all_approved) {
                                echo "<button class='generate-btn btn btn-sm' data-id='".$form_id."' data-name='".$row['student_name']."'>Generate Admit Card</button>";
                            } else {
                                echo "<button class='pending-btn btn btn-sm' disabled>Pending</button>";
                            }
                            ?>
                        </td>
                    </tr>
                    <?php
                }
            } else {
                echo "<tr><td colspan='8' class='text-center'>No students found.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<!-- Modal -->
<div class="modal fade" id="admitModal" tabindex="-1" aria-labelledby="admitModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="admitModalLabel">Admit Card</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Generating admit card...
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(document).ready(function(){
    $('.generate-btn').click(function(){
        var student_id = $(this).data('id');
        var student_name = $(this).data('name');

        $('#admitModal .modal-title').text("Admit Card - " + student_name);
        $('#admitModal .modal-body').html("Generating admit card...");

        var modal = new bootstrap.Modal(document.getElementById('admitModal'));
        modal.show();

        $.ajax({
            url: 'generate_admit_card.php',
            type: 'GET',
            data: { student_id: student_id },
            success: function(response){
                $('#admitModal .modal-body').html(response);
            },
            error: function(){
                $('#admitModal .modal-body').html("Error generating admit card.");
            }
        });
    });
});
</script>
</body>
</html>
