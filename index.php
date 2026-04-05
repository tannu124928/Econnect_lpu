<?php
require_once 'includes/config.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $type = $_POST['type'] ?? 'student';
    $id   = trim($_POST['user_id'] ?? '');
    $pass = trim($_POST['password'] ?? '');

    if (empty($id) || empty($pass)) {
        $error = 'Please enter all fields.';
    } else {
        $hashed = md5($pass);

        if ($type === 'student') {
            $stmt = mysqli_prepare($conn, "SELECT id, name, reg_no, course FROM students WHERE reg_no = ? AND password = ?");
            mysqli_stmt_bind_param($stmt, 'ss', $id, $hashed);
        } else {
            $stmt = mysqli_prepare($conn, "SELECT id, name, faculty_id, department FROM faculty WHERE faculty_id = ? AND password = ?");
            mysqli_stmt_bind_param($stmt, 'ss', $id, $hashed);
        }

        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);

        if ($user) {
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_type'] = $type;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login – LPU eConnect</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="login-page">
  <!-- Left Panel -->
  <div class="login-left">
    <div class="brand">
      <div class="brand-icon">🎓</div>
      <div class="brand-name">LPU eConnect</div>
    </div>
    <h1>Your Campus,<br><span>Digitally Connected</span></h1>
    <p>Access everything from attendance to results, notices to timetables — all in one powerful portal.</p>
    <div class="login-features">
      <div class="login-feature"><div class="login-feature-dot"></div>Real-time Attendance Tracking</div>
      <div class="login-feature"><div class="login-feature-dot"></div>Exam Results & Grade Reports</div>
      <div class="login-feature"><div class="login-feature-dot"></div>University Notices & Alerts</div>
      <div class="login-feature"><div class="login-feature-dot"></div>Fee Payment Status</div>
      <div class="login-feature"><div class="login-feature-dot"></div>Class Timetable</div>
    </div>
  </div>

  <!-- Right Panel -->
  <div class="login-right">
    <div class="login-box">
      <h2>Welcome Back 👋</h2>
      <p class="sub">Sign in to your LPU eConnect account</p>

      <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="POST">
      <div class="tabs">
        <button type="button" class="tab-btn active" onclick="setType('student',this)">🎓 Student</button>
        <button type="button" class="tab-btn" onclick="setType('faculty',this)">👨‍🏫 Faculty</button>
        <button type="button" class="tab-btn" onclick="setType('admin',this)">⚙️ Admin</button>
      </div>
        <input type="hidden" name="type" id="typeField" value="student">

        <div class="form-group">
          <label id="idLabel">Registration Number</label>
          <input type="text" name="user_id" class="form-control" id="userIdField"
                 placeholder="e.g. 11907832" value="<?= htmlspecialchars($_POST['user_id'] ?? '') ?>" required>
        </div>

        <div class="form-group">
          <label>Password</label>
          <input type="password" name="password" class="form-control" placeholder="Enter your password" required>
        </div>

        <button type="submit" class="btn-primary">Sign In →</button>

        <p style="text-align:center;margin-top:24px;font-size:13px;color:var(--text-muted);">
          Demo Student: <strong>11907832</strong> / <strong>student123</strong><br>
          Demo Faculty: <strong>FAC001</strong> / <strong>faculty123</strong>
        </p>
      </form>
    </div>
  </div>
</div>

<script>
function setType(type, btn) {
  document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  document.getElementById('typeField').value = type;
  document.getElementById('idLabel').textContent = type === 'student' ? 'Registration Number' : 'Faculty ID';
  document.getElementById('userIdField').placeholder = type === 'student' ? 'e.g. 11907832' : 'e.g. FAC001';
}
</script>
</body>
</html>
