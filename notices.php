<?php
require_once 'includes/config.php';
if (!isset($_SESSION['user_id'])) { header('Location: index.php'); exit(); }

$type  = $_SESSION['user_type'];
$uid   = $_SESSION['user_id'];
$msg   = '';

// Faculty: post notice
if ($type === 'faculty' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $title    = trim($_POST['title'] ?? '');
    $content  = trim($_POST['content'] ?? '');
    $category = $_POST['category'] ?? 'General';

    if ($title && $content) {
        $stmt = mysqli_prepare($conn, "INSERT INTO notices (title, content, posted_by, category) VALUES (?,?,?,?)");
        mysqli_stmt_bind_param($stmt, 'ssis', $title, $content, $uid, $category);
        mysqli_stmt_execute($stmt);
        $msg = 'Notice posted successfully!';
    }
}

$filter = $_GET['cat'] ?? 'All';
$where  = $filter !== 'All' ? "WHERE category='$filter' AND is_active=1" : "WHERE is_active=1";
$notices = mysqli_query($conn, "SELECT n.*, f.name AS faculty_name FROM notices n LEFT JOIN faculty f ON n.posted_by=f.id $where ORDER BY n.created_at DESC");
$cats = ['All','General','Exam','Holiday','Event','Assignment'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Notices – LPU eConnect</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="layout">
  <?php include 'includes/sidebar.php'; ?>
  <main class="main">
    <div class="page-header">
      <div>
        <div class="page-title">🔔 Notices</div>
        <div class="page-subtitle">University announcements & updates</div>
      </div>
    </div>

    <?php if ($type === 'faculty'): ?>
    <div class="card" style="margin-bottom:28px;">
      <div class="section-title">Post New Notice</div>
      <?php if ($msg): ?><div class="alert alert-success"><?= $msg ?></div><?php endif; ?>
      <form method="POST">
        <div class="grid grid-2">
          <div class="form-group">
            <label>Title</label>
            <input type="text" name="title" class="form-control" placeholder="Notice title..." required>
          </div>
          <div class="form-group">
            <label>Category</label>
            <select name="category" class="form-control">
              <option>General</option><option>Exam</option>
              <option>Holiday</option><option>Event</option><option>Assignment</option>
            </select>
          </div>
        </div>
        <div class="form-group">
          <label>Content</label>
          <textarea name="content" class="form-control" rows="4" placeholder="Write notice content..." required style="resize:vertical;"></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Post Notice</button>
      </form>
    </div>
    <?php endif; ?>

    <!-- Filter tabs -->
    <div style="display:flex;gap:8px;margin-bottom:20px;flex-wrap:wrap;">
      <?php foreach ($cats as $cat): ?>
        <a href="?cat=<?= $cat ?>" class="btn <?= $filter===$cat?'btn-primary':'btn-outline' ?> btn-sm"><?= $cat ?></a>
      <?php endforeach; ?>
    </div>

    <!-- Notices list -->
    <?php while ($n = mysqli_fetch_assoc($notices)): ?>
    <div class="notice-card <?= strtolower($n['category']) ?>">
      <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:10px;">
        <div class="notice-title"><?= htmlspecialchars($n['title']) ?></div>
        <span class="badge badge-purple"><?= $n['category'] ?></span>
      </div>
      <div class="notice-body"><?= nl2br(htmlspecialchars($n['content'])) ?></div>
      <div class="notice-meta">
        👤 <?= $n['faculty_name'] ?? 'Admin' ?> &nbsp;·&nbsp;
        🕐 <?= date('d M Y, h:i A', strtotime($n['created_at'])) ?>
      </div>
    </div>
    <?php endwhile; ?>
  </main>
</div>
</body>
</html>
