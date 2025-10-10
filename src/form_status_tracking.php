<?php
session_start();
include 'db.php';
$user=$_SESSION['username'];
$res=$conn->query("SELECT title,status FROM forms WHERE student_id=(SELECT id FROM users WHERE username='$user')");
echo "<h3>Your Form Status</h3>";
while($row=$res->fetch_assoc()){
  echo "<p><b>{$row['title']}</b> — {$row['status']}</p>";
}
?>
