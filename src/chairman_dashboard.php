<?php
session_start();
include 'db.php';
if($_SESSION['role']!='chairman'){ header("Location: login.php"); exit(); }

$res=$conn->query("SELECT forms.id, users.username, forms.title, forms.status 
FROM forms JOIN users ON forms.student_id=users.id");

echo "<h2>Chairman Dashboard</h2>";
echo "<table border='1'><tr><th>Student</th><th>Title</th><th>Status</th><th>Action</th></tr>";
while($row=$res->fetch_assoc()){
  echo "<tr>
        <td>{$row['username']}</td>
        <td>{$row['title']}</td>
        <td>{$row['status']}</td>
        <td>
          <a href='update_status.php?id={$row['id']}&s=Approved'>Approve</a> |
          <a href='update_status.php?id={$row['id']}&s=Rejected'>Reject</a>
        </td>
        </tr>";
}
echo "</table>";
?>
