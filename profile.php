<?php
require_once 'includes/config.php';
if (!isset($_SESSION['user_id'])) { header('Location: index.php'); exit(); }

$uid  = $_SESSION['user_id'];
$type = $_SESSION['user_type'];
$msg  = '';

if ($type === 'student') {
    $user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM students WHERE id=$uid"));
} else {
    $user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM faculty WHERE id=$uid"));
}

// Update phone/password
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $phone   = trim($_POST['phone'] ?? '');
    $newpass = trim($_POST['new_password'] ?? '');

    if ($newpass) {
        $hashed = md5($newpass);
        $table  = $type === 'student' ? 'students' : 'faculty';
        mysqli_query($conn, "UPDATE $table SET phone='$phone', password='$hashed' WHERE id=$uid");
    } else {
        $table = $type === 'student' ? 'students' : 'faculty';
        mysqli_query($conn, "UPDATE $table SET phone='$phone' WHERE id=$uid");
    }
    $msg = 'Profile updated successfully!';
    header('Location: profile.php?msg=1');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Profile – LPU eConnect</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="layout">
  <?php include 'includes/sidebar.php'; ?>
  <main class="main">
    <div class="page-header">
      <div>
        <div class="page-title">👤 My Profile</div>
        <div class="page-subtitle">View and update your account details</div>
      </div>
    </div>

    <?php if (isset($_GET['msg'])): ?>
      <div class="alert alert-success">✅ Profile updated successfully!</div>
    <?php endif; ?>

    <div class="grid grid-2">
      <!-- Profile card -->
      <div class="card" style="text-align:center;padding:40px;">
        <div style="width:90px;height:90px;background:linear-gradient(135deg,var(--primary),var(--primary-light));border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:36px;color:white;font-weight:800;margin:0 auto 20px;">
          <?= strtoupper(substr($user['name'],0,1)) ?>
        </div>
        <div style="font-size:22px;font-weight:800;margin-bottom:6px;"><?= htmlspecialchars($user['name']) ?></div>
        <div style="color:var(--text-muted);font-size:14px;margin-bottom:16px;">
          <?= $type === 'student' ? $user['reg_no'] : $user['faculty_id'] ?>
        </div>
        <span class="badge badge-purple" style="font-size:13px;padding:6px 16px;">
          <?= ucfirst($type) ?>
        </span>

        <div style="margin-top:28px;text-align:left;">
          <?php $info = $type === 'student'
            ? [['📚','Course',$user['course']],['📅','Semester',$user['semester']],['🏫','Section',$user['section']]]
            : [['🏛️','Department',$user['department']],['🎓','Designation',$user['designation']]];
          foreach ($info as $i): ?>
          <div style="display:flex;gap:10px;padding:12px 0;border-bottom:1px solid var(--border);">
            <span><?= $i[0] ?></span>
            <div>
              <div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.8px;"><?= $i[1] ?></div>
              <div style="font-weight:600;"><?= htmlspecialchars($i[2] ?? 'N/A') ?></div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Edit card -->
      <div class="card">
        <div class="section-title">Edit Details</div>
        <form method="POST">
          <div class="form-group">
            <label>Full Name</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($user['name']) ?>" disabled style="opacity:0.6;">
          </div>
          <div class="form-group">
            <label>Email</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" disabled style="opacity:0.6;">
          </div>
          <div class="form-group">
            <label>Phone Number</label>
            <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" placeholder="Enter phone number">
          </div>
          <div class="form-group">
            <label>New Password <span style="color:var(--text-muted);font-weight:400;">(leave blank to keep current)</span></label>
            <input type="password" name="new_password" class="form-control" placeholder="Enter new password">
          </div>
          <button type="submit" class="btn btn-primary">Save Changes</button>
        </form>
      </div>
    </div>
  </main>
</div>
</body>
</html>
