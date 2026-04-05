<?php
require_once 'includes/config.php';
if (!isset($_SESSION['user_id'])) { header('Location: index.php'); exit(); }
$uid = $_SESSION['user_id'];

// Per-course attendance
$sql = "SELECT c.course_code, c.course_name,
               COUNT(a.id) AS total,
               SUM(a.status='Present') AS present,
               SUM(a.status='Absent') AS absent
        FROM courses c
        LEFT JOIN attendance a ON a.course_id = c.id AND a.student_id = $uid
        GROUP BY c.id";
$courses = mysqli_query($conn, $sql);

// Recent records
$recent = mysqli_query($conn, "SELECT a.date, a.status, c.course_name
        FROM attendance a JOIN courses c ON a.course_id=c.id
        WHERE a.student_id=$uid ORDER BY a.date DESC LIMIT 20");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Attendance – LPU eConnect</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="layout">
  <?php include 'includes/sidebar.php'; ?>
  <main class="main">
    <div class="page-header">
      <div>
        <div class="page-title">📊 Attendance</div>
        <div class="page-subtitle">Course-wise attendance summary</div>
      </div>
    </div>

    <!-- Per course -->
    <div class="section-title">Course-wise Summary</div>
    <div class="grid grid-3" style="margin-bottom:32px;">
      <?php while ($c = mysqli_fetch_assoc($courses)):
        $pct = $c['total'] > 0 ? round(($c['present']/$c['total'])*100) : 0;
        $cls = $pct < 75 ? 'low' : ($pct < 85 ? 'mid' : '');
        $color = $pct < 75 ? '#EF4444' : ($pct < 85 ? '#F59E0B' : '#10B981');
      ?>
      <div class="card">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px;">
          <div>
            <div style="font-weight:700;font-size:15px;"><?= htmlspecialchars($c['course_name']) ?></div>
            <div style="font-size:12px;color:var(--text-muted);"><?= $c['course_code'] ?></div>
          </div>
          <div style="font-size:26px;font-weight:800;color:<?= $color ?>;"><?= $pct ?>%</div>
        </div>
        <div class="progress-bar-wrap">
          <div class="progress-bar <?= $cls ?>" style="width:<?= $pct ?>%"></div>
        </div>
        <div style="display:flex;justify-content:space-between;margin-top:10px;font-size:12px;color:var(--text-muted);">
          <span>✅ <?= $c['present'] ?> Present</span>
          <span>❌ <?= $c['absent'] ?> Absent</span>
          <span>📅 <?= $c['total'] ?> Total</span>
        </div>
        <?php if ($pct < 75): ?>
          <div class="alert alert-error" style="margin-top:12px;padding:8px 12px;font-size:12px;">
            ⚠️ Below 75% – Risk of shortage
          </div>
        <?php endif; ?>
      </div>
      <?php endwhile; ?>
    </div>

    <!-- Recent records -->
    <div class="section-title">Recent Records</div>
    <div class="card table-wrapper">
      <table>
        <thead><tr><th>Date</th><th>Course</th><th>Status</th></tr></thead>
        <tbody>
          <?php while ($r = mysqli_fetch_assoc($recent)): ?>
          <tr>
            <td><?= date('d M Y', strtotime($r['date'])) ?></td>
            <td><?= htmlspecialchars($r['course_name']) ?></td>
            <td>
              <?php
              $badge = match($r['status']) {
                'Present' => 'badge-green',
                'Absent'  => 'badge-red',
                'Late'    => 'badge-amber',
                default   => 'badge-blue'
              };
              ?>
              <span class="badge <?= $badge ?>"><?= $r['status'] ?></span>
            </td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </main>
</div>
</body>
</html>
