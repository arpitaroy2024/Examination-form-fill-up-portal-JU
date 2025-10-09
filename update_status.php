<?php
include 'db.php';
$id=$_GET['id'];
$status=$_GET['s'];
$conn->query("UPDATE forms SET status='$status' WHERE id=$id");
header("Location: chairman_dashboard.php");
?>
