<?php
session_start();
if(!isset($_SESSION['username']) || $_SESSION['role']!='student'){
  header("Location: login.php");
  exit();
}
?>
<h2>Welcome, <?php echo $_SESSION['username']; ?> (Student)</h2>
<a href="form_fill.php">Fill Form</a> |
<a href="form_status_tracking.php">Track Form</a> |
<a href="logout.php">Logout</a>
