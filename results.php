<?php
require_once 'includes/config.php';
if (!isset($_SESSION['user_id'])) { header('Location: index.php'); exit(); }
$uid = $_SESSION['user_id'];

$marks = mysqli_query($conn, "SELECT m.*, c.course_name, c.course_code
        FROM marks m JOIN courses c ON m.course_id=c.id
        WHERE m.student_id=$uid ORDER BY c.course_code, m.exam_type");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Results – LPU eConnect</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="layout">
  <?php include 'includes/sidebar.php'; ?>
  <main class="main">
    <div class="page-header">
      <div>
        <div class="page-title">📝 Results & Marks</div>
        <div class="page-subtitle">Exam-wise marks report</div>
      </div>
    </div>

    <div class="card table-wrapper">
      <table>
        <thead>
          <tr>
            <th>Course</th>
            <th>Exam Type</th>
            <th>Marks Obtained</th>
            <th>Total</th>
            <th>Percentage</th>
            <th>Grade</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($m = mysqli_fetch_assoc($marks)):
            $pct = $m['total_marks'] > 0 ? round(($m['marks_obtained']/$m['total_marks'])*100) : 0;
            $grade = $pct>=90?'A+':($pct>=80?'A':($pct>=70?'B+':($pct>=60?'B':($pct>=50?'C':'F'))));
            $gbadge = in_array($grade,['A+','A'])?'badge-green':($grade==='F'?'badge-red':'badge-amber');
          ?>
          <tr>
            <td>
              <div style="font-weight:600;"><?= htmlspecialchars($m['course_name']) ?></div>
              <div style="font-size:12px;color:var(--text-muted);"><?= $m['course_code'] ?></div>
            </td>
            <td><span class="chip"><?= $m['exam_type'] ?></span></td>
            <td style="font-weight:700;font-size:18px;"><?= $m['marks_obtained'] ?></td>
            <td><?= $m['total_marks'] ?></td>
            <td>
              <div style="display:flex;align-items:center;gap:10px;">
                <div><?= $pct ?>%</div>
                <div style="flex:1;min-width:80px;" class="progress-bar-wrap">
                  <div class="progress-bar <?= $pct<50?'low':($pct<70?'mid':'') ?>" style="width:<?= $pct ?>%"></div>
                </div>
              </div>
            </td>
            <td><span class="badge <?= $gbadge ?>"><?= $grade ?></span></td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </main>
</div>
</body>
</html>
