<?php
require_once 'includes/config.php';

if (!isset($_SESSION['user_id'])) { header('Location: index.php'); exit(); }

$type = $_SESSION['user_type'];
$uid  = $_SESSION['user_id'];
$name = $_SESSION['user_name'];

// Fetch notices
$notices = mysqli_query($conn, "SELECT * FROM notices WHERE is_active=1 ORDER BY created_at DESC LIMIT 5");

if ($type === 'student') {
    // Student stats
    $total_att  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM attendance WHERE student_id=$uid"))['c'];
    $present    = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM attendance WHERE student_id=$uid AND status='Present'"))['c'];
    $att_pct    = $total_att > 0 ? round(($present/$total_att)*100) : 0;
    $total_courses = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(DISTINCT course_id) as c FROM marks WHERE student_id=$uid"))['c'];
    $fee = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM fees WHERE student_id=$uid ORDER BY id DESC LIMIT 1"));
} else {
    // Faculty stats
    $total_students = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM students"))['c'];
    $total_courses  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM courses WHERE faculty_id=$uid"))['c'];
    $total_notices  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM notices WHERE posted_by=$uid"))['c'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard – LPU eConnect</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="layout">
  <?php include 'includes/sidebar.php'; ?>
  <main class="main">
    <div class="page-header">
      <div>
        <div class="page-title">Good <?= date('H')<12?'Morning':( date('H')<17?'Afternoon':'Evening') ?>, <?= explode(' ',$name)[0] ?> 👋</div>
        <div class="page-subtitle">Welcome to LPU eConnect – Your academic hub</div>
      </div>
      <div class="topbar-date"><?= date('D, d M Y') ?></div>
    </div>

    <?php if ($type === 'student'): ?>
    <!-- Student Stats -->
    <div class="grid grid-4" style="margin-bottom:28px;">
      <div class="card stat-card">
        <div class="stat-icon purple">📊</div>
        <div>
          <div class="stat-value"><?= $att_pct ?>%</div>
          <div class="stat-label">Attendance</div>
          <div style="margin-top:10px;">
            <div class="progress-bar-wrap">
              <div class="progress-bar <?= $att_pct<75?'low':($att_pct<85?'mid':'') ?>" style="width:<?= $att_pct ?>%"></div>
            </div>
          </div>
        </div>
      </div>
      <div class="card stat-card">
        <div class="stat-icon amber">📚</div>
        <div>
          <div class="stat-value"><?= $total_courses ?></div>
          <div class="stat-label">Enrolled Courses</div>
          <div class="stat-change">This Semester</div>
        </div>
      </div>
      <div class="card stat-card">
        <div class="stat-icon green">✅</div>
        <div>
          <div class="stat-value"><?= $present ?>/<?= $total_att ?></div>
          <div class="stat-label">Classes Attended</div>
        </div>
      </div>
      <div class="card stat-card">
        <div class="stat-icon <?= (!$fee||$fee['status']==='Paid')?'green':($fee['status']==='Partial'?'amber':'red') ?>">💳</div>
        <div>
          <div class="stat-value"><?= $fee ? $fee['status'] : 'N/A' ?></div>
          <div class="stat-label">Fee Status</div>
          <?php if ($fee && $fee['status'] !== 'Paid'): ?>
            <div style="color:var(--danger);font-size:12px;font-weight:600;margin-top:4px;">
              Due: ₹<?= number_format($fee['amount'] - $fee['paid_amount']) ?>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <?php else: ?>
    <!-- Faculty Stats -->
    <div class="grid grid-3" style="margin-bottom:28px;">
      <div class="card stat-card">
        <div class="stat-icon purple">👨‍🎓</div>
        <div>
          <div class="stat-value"><?= $total_students ?></div>
          <div class="stat-label">Total Students</div>
        </div>
      </div>
      <div class="card stat-card">
        <div class="stat-icon amber">📚</div>
        <div>
          <div class="stat-value"><?= $total_courses ?></div>
          <div class="stat-label">Your Courses</div>
        </div>
      </div>
      <div class="card stat-card">
        <div class="stat-icon green">🔔</div>
        <div>
          <div class="stat-value"><?= $total_notices ?></div>
          <div class="stat-label">Notices Posted</div>
        </div>
      </div>
    </div>
    <?php endif; ?>

    <!-- Notices Section -->
    <div class="grid grid-2">
      <div>
        <div class="section-title">📢 Latest Notices</div>
        <?php while($n = mysqli_fetch_assoc($notices)): ?>
          <div class="notice-card <?= strtolower($n['category']) ?>">
            <div style="display:flex;justify-content:space-between;align-items:flex-start;">
              <div class="notice-title"><?= htmlspecialchars($n['title']) ?></div>
              <span class="badge badge-purple"><?= $n['category'] ?></span>
            </div>
            <div class="notice-body"><?= htmlspecialchars(substr($n['content'],0,120)) ?>...</div>
            <div class="notice-meta">🕐 <?= date('d M Y', strtotime($n['created_at'])) ?></div>
          </div>
        <?php endwhile; ?>
        <a href="notices.php" class="btn btn-outline btn-sm">View All Notices</a>
      </div>

      <!-- Quick Links -->
      <div>
        <div class="section-title">⚡ Quick Access</div>
        <div class="grid grid-2" style="gap:14px;">
          <?php
          $links = $type === 'student'
            ? [
                ['📊','Attendance','attendance.php','Check your class attendance'],
                ['📝','Results','results.php','View exam marks'],
                ['🗓️','Timetable','timetable.php','Class schedule'],
                ['💳','Fee Status','fees.php','Payment info'],
              ]
            : [
                ['👨‍🎓','Students','students.php','View student list'],
                ['✅','Attendance','mark_attendance.php','Mark attendance'],
                ['📝','Add Marks','add_marks.php','Enter exam marks'],
                ['🔔','Notices','notices.php','Post notices'],
              ];
          foreach ($links as $l):
          ?>
          <a href="<?= $l[2] ?>" style="text-decoration:none;">
            <div class="card" style="cursor:pointer;transition:transform 0.2s,box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-3px)';this.style.boxShadow='0 8px 30px rgba(108,43,217,0.15)'" onmouseout="this.style.transform='';this.style.boxShadow=''">
              <div style="font-size:28px;margin-bottom:10px;"><?= $l[0] ?></div>
              <div style="font-weight:700;font-size:15px;margin-bottom:4px;"><?= $l[1] ?></div>
              <div style="font-size:12px;color:var(--text-muted);"><?= $l[3] ?></div>
            </div>
          </a>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </main>
</div>
</body>
</html>
