<?php
require_once 'includes/config.php';
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'faculty') {
    header('Location: index.php'); exit();
}

$search   = trim($_GET['q'] ?? '');
$where    = $search ? "WHERE name LIKE '%$search%' OR reg_no LIKE '%$search%'" : '';
$students = mysqli_query($conn, "SELECT * FROM students $where ORDER BY name");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Students – LPU eConnect</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="layout">
  <?php include 'includes/sidebar.php'; ?>
  <main class="main">
    <div class="page-header">
      <div>
        <div class="page-title">👨‍🎓 Students</div>
        <div class="page-subtitle">All enrolled students</div>
      </div>
    </div>

    <form method="GET" style="margin-bottom:20px;display:flex;gap:10px;">
      <input type="text" name="q" class="form-control" style="max-width:360px;"
             placeholder="Search by name or reg no..." value="<?= htmlspecialchars($search) ?>">
      <button type="submit" class="btn btn-primary">Search</button>
      <?php if ($search): ?><a href="students.php" class="btn btn-outline">Clear</a><?php endif; ?>
    </form>

    <div class="card table-wrapper">
      <table>
        <thead>
          <tr><th>Reg No</th><th>Name</th><th>Course</th><th>Semester</th><th>Section</th><th>Email</th><th>Phone</th></tr>
        </thead>
        <tbody>
          <?php while ($s = mysqli_fetch_assoc($students)): ?>
          <tr>
            <td><span class="chip"><?= $s['reg_no'] ?></span></td>
            <td style="font-weight:600;"><?= htmlspecialchars($s['name']) ?></td>
            <td><?= htmlspecialchars($s['course']) ?></td>
            <td style="text-align:center;"><?= $s['semester'] ?></td>
            <td><?= $s['section'] ?></td>
            <td><?= $s['email'] ?></td>
            <td><?= $s['phone'] ?? '—' ?></td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </main>
</div>
</body>
</html>
