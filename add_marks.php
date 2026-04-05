<?php
require_once 'includes/config.php';
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'faculty') {
    header('Location: index.php'); exit();
}
$uid = $_SESSION['user_id'];
$msg = '';

$courses = mysqli_query($conn, "SELECT * FROM courses WHERE faculty_id=$uid");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['marks'])) {
    $course_id  = intval($_POST['course_id']);
    $exam_type  = $_POST['exam_type'];
    $total      = floatval($_POST['total_marks']);
    foreach ($_POST['marks'] as $student_id => $obtained) {
        $student_id = intval($student_id);
        $obtained   = floatval($obtained);
        $check = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id FROM marks WHERE student_id=$student_id AND course_id=$course_id AND exam_type='$exam_type'"));
        if ($check) {
            mysqli_query($conn, "UPDATE marks SET marks_obtained=$obtained, total_marks=$total WHERE student_id=$student_id AND course_id=$course_id AND exam_type='$exam_type'");
        } else {
            mysqli_query($conn, "INSERT INTO marks (student_id,course_id,exam_type,marks_obtained,total_marks) VALUES ($student_id,$course_id,'$exam_type',$obtained,$total)");
        }
    }
    $msg = 'Marks saved successfully!';
}

$sel_course = intval($_GET['course'] ?? 0);
$students   = $sel_course ? mysqli_query($conn, "SELECT * FROM students ORDER BY name") : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Add Marks – LPU eConnect</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="layout">
  <?php include 'includes/sidebar.php'; ?>
  <main class="main">
    <div class="page-header">
      <div>
        <div class="page-title">📝 Add Marks</div>
        <div class="page-subtitle">Enter exam marks for students</div>
      </div>
    </div>

    <?php if ($msg): ?><div class="alert alert-success"><?= $msg ?></div><?php endif; ?>

    <form method="GET" class="card" style="margin-bottom:24px;">
      <div class="form-group" style="margin:0;max-width:400px;">
        <label>Select Course</label>
        <select name="course" class="form-control" onchange="this.form.submit()">
          <option value="">-- Select Course --</option>
          <?php while ($c = mysqli_fetch_assoc($courses)): ?>
            <option value="<?= $c['id'] ?>" <?= $sel_course==$c['id']?'selected':'' ?>>
              <?= $c['course_code'] ?> – <?= $c['course_name'] ?>
            </option>
          <?php endwhile; ?>
        </select>
      </div>
    </form>

    <?php if ($sel_course && $students): ?>
    <form method="POST">
      <input type="hidden" name="course_id" value="<?= $sel_course ?>">
      <div class="card">
        <div class="grid grid-2" style="margin-bottom:20px;">
          <div class="form-group" style="margin:0;">
            <label>Exam Type</label>
            <select name="exam_type" class="form-control">
              <option>Minor1</option><option>Minor2</option>
              <option>Major</option><option>Assignment</option><option>Practical</option>
            </select>
          </div>
          <div class="form-group" style="margin:0;">
            <label>Total Marks</label>
            <input type="number" name="total_marks" class="form-control" value="35" min="1" max="200" required>
          </div>
        </div>

        <table>
          <thead><tr><th>#</th><th>Reg No</th><th>Student Name</th><th style="width:180px;">Marks Obtained</th></tr></thead>
          <tbody>
            <?php $i = 1; while ($s = mysqli_fetch_assoc($students)): ?>
            <tr>
              <td><?= $i++ ?></td>
              <td><span class="chip"><?= $s['reg_no'] ?></span></td>
              <td style="font-weight:600;"><?= htmlspecialchars($s['name']) ?></td>
              <td>
                <input type="number" name="marks[<?= $s['id'] ?>]" class="form-control"
                       placeholder="0" min="0" step="0.5" style="padding:8px;">
              </td>
            </tr>
            <?php endwhile; ?>
          </tbody>
        </table>

        <div style="margin-top:20px;">
          <button type="submit" class="btn btn-primary">💾 Save Marks</button>
        </div>
      </div>
    </form>
    <?php endif; ?>
  </main>
</div>
</body>
</html>
