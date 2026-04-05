<?php
require_once 'includes/config.php';
if (!isset($_SESSION['user_id'])) { header('Location: index.php'); exit(); }

$days = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
$timetable = mysqli_query($conn, "SELECT t.*, c.course_name, c.course_code, f.name as faculty_name
        FROM timetable t JOIN courses c ON t.course_id=c.id
        LEFT JOIN faculty f ON c.faculty_id=f.id ORDER BY FIELD(t.day,'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'), t.start_time");
$byDay = [];
while ($row = mysqli_fetch_assoc($timetable)) $byDay[$row['day']][] = $row;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Timetable – LPU eConnect</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="layout">
  <?php include 'includes/sidebar.php'; ?>
  <main class="main">
    <div class="page-header">
      <div>
        <div class="page-title">🗓️ Class Timetable</div>
        <div class="page-subtitle">Weekly class schedule</div>
      </div>
      <div class="topbar-date">Today: <?= date('l') ?></div>
    </div>

    <?php foreach ($days as $day):
      $isToday = date('l') === $day;
    ?>
    <div style="margin-bottom:20px;">
      <div class="section-title">
        <?= $isToday ? '📍 ' : '' ?><?= $day ?>
        <?php if ($isToday): ?><span class="badge badge-green" style="margin-left:8px;">Today</span><?php endif; ?>
      </div>

      <?php if (!empty($byDay[$day])): ?>
      <div class="grid grid-3">
        <?php foreach ($byDay[$day] as $slot): ?>
        <div class="card" style="border-left:4px solid var(--primary);padding:18px;">
          <div style="font-size:12px;color:var(--text-muted);margin-bottom:6px;">
            🕐 <?= date('h:i A', strtotime($slot['start_time'])) ?> – <?= date('h:i A', strtotime($slot['end_time'])) ?>
          </div>
          <div style="font-weight:700;font-size:15px;margin-bottom:4px;"><?= htmlspecialchars($slot['course_name']) ?></div>
          <div style="font-size:12px;color:var(--text-muted);">
            📍 <?= $slot['room'] ?? 'TBA' ?> &nbsp;·&nbsp; 👨‍🏫 <?= $slot['faculty_name'] ?? 'TBA' ?>
          </div>
          <div style="margin-top:8px;"><span class="chip"><?= $slot['course_code'] ?></span></div>
        </div>
        <?php endforeach; ?>
      </div>
      <?php else: ?>
        <div class="alert alert-info">No classes scheduled for <?= $day ?>.</div>
      <?php endif; ?>
    </div>
    <?php endforeach; ?>
  </main>
</div>
</body>
</html>
