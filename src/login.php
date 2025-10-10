<?php
session_start();
include 'db.php';

if($_SERVER['REQUEST_METHOD']=='POST'){
  $username=$_POST['username'];
  $password=$_POST['password'];

  $res=$conn->query("SELECT * FROM users WHERE username='$username'");
  if($res->num_rows>0){
    $row=$res->fetch_assoc();
    if(password_verify($password,$row['password'])){
      $_SESSION['username']=$row['username'];
      $_SESSION['role']=$row['role'];
      if($row['role']=='chairman'){
        header("Location: chairman_dashboard.php");
      } else {
        header("Location: student_dashboard.php");
      }
      exit();
    } else {
      echo "❌ Wrong password!";
    }
  } else {
    echo "❌ User not found!";
  }
}
?>
<form method="POST">
  <h3>Login</h3>
  Username: <input type="text" name="username" required><br>
  Password: <input type="password" name="password" required><br>
  <button type="submit">Login</button>
</form>
