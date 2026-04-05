<?php
require_once 'includes/config.php';
if (!isset($_SESSION['user_id'])) { header('Location: index.php'); exit(); }
$uid  = $_SESSION['user_id'];
$fees = mysqli_query($conn, "SELECT * FROM fees WHERE student_id=$uid ORDER BY semester DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Fee Status – LPU eConnect</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="layout">
  <?php include 'includes/sidebar.php'; ?>
  <main class="main">
    <div class="page-header">
      <div>
        <div class="page-title">💳 Fee Status</div>
        <div class="page-subtitle">Semester-wise fee payment details</div>
      </div>
    </div>

    <div class="card table-wrapper">
      <table>
        <thead>
          <tr><th>Semester</th><th>Total Amount</th><th>Paid</th><th>Balance</th><th>Due Date</th><th>Status</th></tr>
        </thead>
        <tbody>
          <?php while ($f = mysqli_fetch_assoc($fees)):
            $balance = $f['amount'] - $f['paid_amount'];
            $badge = match($f['status']) {
              'Paid'    => 'badge-green',
              'Partial' => 'badge-amber',
              default   => 'badge-red'
            };
          ?>
          <tr>
            <td><strong>Semester <?= $f['semester'] ?></strong></td>
            <td>₹<?= number_format($f['amount']) ?></td>
            <td style="color:var(--accent2);font-weight:600;">₹<?= number_format($f['paid_amount']) ?></td>
            <td style="color:<?= $balance>0?'var(--danger)':'var(--accent2)' ?>;font-weight:600;">
              ₹<?= number_format($balance) ?>
            </td>
            <td><?= $f['due_date'] ? date('d M Y', strtotime($f['due_date'])) : '—' ?></td>
            <td><span class="badge <?= $badge ?>"><?= $f['status'] ?></span></td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>

    <div class="alert alert-info" style="margin-top:20px;">
      💡 For fee-related queries, contact the Finance Department or email <strong>fees@lpu.in</strong>
    </div>
  </main>
</div>
</body>
</html>
