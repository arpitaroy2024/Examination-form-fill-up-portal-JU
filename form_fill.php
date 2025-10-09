<?php
session_start();
include 'db.php'; // নিশ্চিত করো এই ফাইলে $conn আছে (mysqli)

// Option: তুমি চাইলে এখানে login page redirect করতে পারো,
// কিন্তু তুমি বলেছিলে redirect না হবে — তাই আমরা redirect নয়, friendly message দেব।

$success = '';
$errors = [];

// Simple CSRF token (optional but useful)
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(24));
}
$csrf_token = $_SESSION['csrf_token'];

// Handle POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // check CSRF
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $csrf_token) {
        $errors[] = "Invalid request (CSRF).";
    } else {
        // Check login & role
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student' || empty($_SESSION['username'])) {
            // Don't redirect — show message so user knows to login first
            $errors[] = "You must be logged in as a student to submit the form. Please <a href='login.php'>login</a> first.";
        } else {
            // Get and validate fields
            $name = trim($_POST['name'] ?? '');
            $roll = trim($_POST['roll'] ?? '');
            $reg  = trim($_POST['reg'] ?? '');
            $session_field = trim($_POST['session'] ?? '');
            $dept = trim($_POST['dept'] ?? '');
            $semester = trim($_POST['semester'] ?? '');
            $courses = trim($_POST['courses'] ?? '');
            $center = trim($_POST['center'] ?? '');

            if ($name === '') $errors[] = "Student name is required.";
            if ($roll === '') $errors[] = "Roll number is required.";
            if ($reg === '') $errors[] = "Registration number is required.";
            // add more validation as you like

            if (empty($errors)) {
                // get student id from users table using username from session
                $username = $_SESSION['username'];

                $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
                $stmt->bind_param("s", $username);
                $stmt->execute();
                $res = $stmt->get_result();
                if ($row = $res->fetch_assoc()) {
                    $student_id = (int)$row['id'];
                    $stmt->close();

                    // Insert into students (or forms) table safely
                    // Adjust table/columns as per your DB schema
                    $ins = $conn->prepare("INSERT INTO students (name, roll, reg, session, dept, semester, courses, center, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
                    $ins->bind_param("ssssssss", $name, $roll, $reg, $session_field, $dept, $semester, $courses, $center);

                    if ($ins->execute()) {
                        $success = "✅ Form submitted successfully!";
                        // optionally clear POST values to clear the form
                        $_POST = [];
                    } else {
                        $errors[] = "Database insert failed. Try again later.";
                        error_log("Insert error: " . $ins->error);
                    }
                    $ins->close();
                } else {
                    $stmt->close();
                    $errors[] = "Logged in user not found in database. Please login again.";
                }
            }
        }
    }
}
?>
<!doctype html>
<html lang="bn">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Exam Form Fill-up</title>
  <style>
    body{font-family:Arial, sans-serif;background:#f1f8e9;padding:20px}
    .box{max-width:700px;margin:20px auto;background:#fff;padding:20px;border-radius:8px;box-shadow:0 0 8px rgba(0,0,0,0.1)}
    label{display:block;margin-top:10px;font-weight:bold}
    input,select,textarea{width:100%;padding:8px;margin-top:5px;border:1px solid #ccc;border-radius:5px}
    button{margin-top:15px;background:#2e7d32;color:#fff;border:none;padding:10px 15px;border-radius:5px;cursor:pointer}
    .err{background:#ffe6e6;border:1px solid #ffb3b3;padding:10px;margin-bottom:10px;border-radius:4px;color:#900}
    .ok{background:#e8ffe8;border:1px solid #b3ffb3;padding:10px;margin-bottom:10px;border-radius:4px;color:#060}
  </style>
</head>
<body>
  <div class="box">
    <h2>Exam Form Fill-up</h2>

    <?php if(!empty($errors)): ?>
      <div class="err">
        <?php foreach($errors as $e) echo "<div>". $e ."</div>"; ?>
      </div>
    <?php endif; ?>

    <?php if($success): ?>
      <div class="ok"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <!-- Form (fields retained after submit when errors) -->
    <form method="POST" novalidate>
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
      <label>Student Name</label>
      <input type="text" name="name" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>

      <label>Roll Number</label>
      <input type="text" name="roll" value="<?= htmlspecialchars($_POST['roll'] ?? '') ?>" required>

      <label>Registration Number</label>
      <input type="text" name="reg" value="<?= htmlspecialchars($_POST['reg'] ?? '') ?>" required>

      <label>Session</label>
      <input type="text" name="session" value="<?= htmlspecialchars($_POST['session'] ?? '') ?>" placeholder="e.g. 2020-21" required>

      <label>Department</label>
      <select name="dept" required>
        <option value="">Select Department</option>
        <?php
          $depts = ['Computer Science and Engineering','Physics','Chemistry','Mathematics','Statistics'];
          foreach($depts as $d) {
            $sel = (isset($_POST['dept']) && $_POST['dept']==$d) ? 'selected' : '';
            echo "<option $sel>".htmlspecialchars($d)."</option>";
          }
        ?>
      </select>

      <label>Semester</label>
      <select name="semester" required>
        <option value="">Select Semester</option>
        <?php for($i=1;$i<=8;$i++): 
            $opt = $i . 'st Semester';
            if ($i==2) $opt = '2nd Semester';
            if ($i==3) $opt = '3rd Semester';
            $sel = (isset($_POST['semester']) && $_POST['semester']==$opt) ? 'selected' : '';
        ?>
          <option <?= $sel ?>><?= htmlspecialchars($opt) ?></option>
        <?php endfor; ?>
      </select>

      <label>Courses</label>
      <textarea name="courses" rows="3" required><?= htmlspecialchars($_POST['courses'] ?? '') ?></textarea>

      <label>Exam Center</label>
      <input type="text" name="center" value="<?= htmlspecialchars($_POST['center'] ?? '') ?>" required>

      <button type="submit">Submit Form</button>
    </form>
  </div>
</body>
</html>

