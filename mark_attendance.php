<?php
require_once 'includes/config.php';
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'faculty') {
    header('Location: index.php'); exit();
}
$uid = $_SESSION['user_id'];
$msg = '';

// Fetch faculty courses
$courses = mysqli_query($conn, "SELECT * FROM courses WHERE faculty_id=$uid");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['attendance'])) {
    $course_id = intval($_POST['course_id']);
    $date      = $_POST['date'];
    foreach ($_POST['attendance'] as $student_id => $status) {
        $student_id = intval($student_id);
        // Upsert
        $check = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id FROM attendance WHERE student_id=$student_id AND course_id=$course_id AND date='$date'"));
        if ($check) {
            mysqli_query($conn, "UPDATE attendance SET status='$status' WHERE student_id=$student_id AND course_id=$course_id AND date='$date'");
        } else {
            mysqli_query($conn, "INSERT INTO attendance (student_id,course_id,date,status,marked_by) VALUES ($student_id,$course_id,'$date','$status',$uid)");
        }
    }
    $msg = 'Attendance saved!';
}

$sel_course = intval($_GET['course'] ?? 0);
$students   = $sel_course ? mysqli_query($conn, "SELECT * FROM students ORDER BY name") : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Mark Attendance – LPU eConnect</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="layout">
  <?php include 'includes/sidebar.php'; ?>
  <main class="main">
    <div class="page-header">
      <div>
        <div class="page-title">✅ Mark Attendance</div>
        <div class="page-subtitle">Record student attendance</div>
      </div>
    </div>

    <?php if ($msg): ?><div class="alert alert-success"><?= $msg ?></div><?php endif; ?>

    <!-- Select course -->
    <form method="GET" class="card" style="margin-bottom:24px;">
      <div class="grid grid-2" style="align-items:flex-end;gap:16px;">
        <div class="form-group" style="margin:0;">
          <label>Select Course</label>
          <select name="course" class="form-control" onchange="this.form.submit()">
            <option value="">-- Select Course --</option>
            <?php $courses_arr = []; while ($c = mysqli_fetch_assoc($courses)) $courses_arr[] = $c; ?>
            <?php foreach ($courses_arr as $c): ?>
              <option value="<?= $c['id'] ?>" <?= $sel_course==$c['id']?'selected':'' ?>>
                <?= $c['course_code'] ?> – <?= $c['course_name'] ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
    </form>

    <?php if ($sel_course && $students): ?>
    <form method="POST">
      <input type="hidden" name="course_id" value="<?= $sel_course ?>">
      <div class="card">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
          <div class="section-title" style="margin:0;">Student List</div>
          <div class="form-group" style="margin:0;display:flex;align-items:center;gap:10px;">
            <label style="white-space:nowrap;">Date:</label>
            <input type="date" name="date" class="form-control" value="<?= date('Y-m-d') ?>" style="width:auto;" required>
          </div>
        </div>

        <div style="margin-bottom:16px;display:flex;gap:10px;">
          <button type="button" onclick="markAll('Present')" class="btn btn-outline btn-sm">✅ All Present</button>
          <button type="button" onclick="markAll('Absent')" class="btn btn-outline btn-sm">❌ All Absent</button>
        </div>

        <table>
          <thead><tr><th>#</th><th>Reg No</th><th>Name</th><th>Course</th><th style="width:250px;">Status</th></tr></thead>
          <tbody>
            <?php $i = 1; while ($s = mysqli_fetch_assoc($students)): ?>
            <tr>
              <td><?= $i++ ?></td>
              <td><span class="chip"><?= $s['reg_no'] ?></span></td>
              <td style="font-weight:600;"><?= htmlspecialchars($s['name']) ?></td>
              <td><?= $s['course'] ?></td>
              <td>
                <select name="attendance[<?= $s['id'] ?>]" class="form-control att-select" style="padding:8px;" data-sid="<?= $s['id'] ?>">
                  <option value="Present">✅ Present</option>
                  <option value="Absent">❌ Absent</option>
                  <option value="Late">⏰ Late</option>
                </select>
              </td>
            </tr>
            <?php endwhile; ?>
          </tbody>
        </table>

        <div style="margin-top:20px;">
          <button type="submit" class="btn btn-primary">💾 Save Attendance</button>
        </div>
      </div>
    </form>
    <?php endif; ?>
  </main>
</div>
<script>
function markAll(status) {
  document.querySelectorAll('.att-select').forEach(s => s.value = status);
}
</script>
</body>
</html>
