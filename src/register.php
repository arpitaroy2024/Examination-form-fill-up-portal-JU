<?php
include 'db.php';
if($_SERVER['REQUEST_METHOD']=='POST'){
  $fullname = $_POST['fullname'];
  $email = $_POST['email'];
  $student_id = $_POST['student_id'];
  $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
  $role = $_POST['role'];

  $sql = "INSERT INTO users(fullname, email, student_id, password, role) 
          VALUES('$fullname', '$email', '$student_id', '$password', '$role')";
          
  if($conn->query($sql)){
    echo "✅ Registration Successful! <a href='login.html'>Login Now</a>";
  } else {
    echo "❌ Error: " . $conn->error;
  }
}
?>
