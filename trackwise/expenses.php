<?php
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }
include 'db.php';
$user_id  = $_SESSION['user_id'];
$username = $_SESSION['username'];
if (isset($_GET['delete'])) {
    mysqli_query($conn, "DELETE FROM expenses WHERE id='".$_GET['delete']."' AND user_id='$user_id'");
    header("Location: expenses.php"); exit();
}
$result  = mysqli_query($conn, "SELECT e.*, c.name as category FROM expenses e JOIN categories c ON e.category_id=c.id WHERE e.user_id='$user_id' ORDER BY e.expense_date DESC");
$total_r = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(amount) as t FROM expenses WHERE user_id='$user_id'"));
$total   = $total_r['t'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Expenses — TrackWise</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="bg-scene"></div>
<div class="bg-crystals">
  <div class="crystal-shape"></div><div class="crystal-shape"></div>
  <div class="crystal-shape"></div><div class="crystal-shape"></div>
</div>
<div class="snow"></div>

<nav class="topbar">
  <a href="index.php" class="topbar-brand">
    <div class="t-icon">💰</div>
    <div class="t-name">TRACKWISE</div>
  </a>
  <div class="topbar-nav">
    <a href="add_expense.php" class="btn btn-sm">+ Add Expense</a>
    <a href="analytics.php" class="btn btn-ghost btn-sm">📊 Analytics</a>
    <a href="index.php" class="btn btn-ghost btn-sm">🏠 Dashboard</a>
    <a href="logout.php" class="btn btn-red btn-sm">Logout</a>
  </div>
  <div class="topbar-user">
    <div class="avatar"><?= strtoupper(substr($username,0,1)) ?></div>
    <span><?= htmlspecialchars($username) ?></span>
  </div>
</nav>

<div class="main-content">
  <div class="page-header">
    <h1>All <span>Expenses</span></h1>
    <p>Total spending: ₹<?= number_format($total,2) ?></p>
  </div>
  <div class="panel">
    <div class="panel-header">
      <div class="panel-title">Transaction History</div>
      <a href="add_expense.php" class="btn btn-sm">+ Add New</a>
    </div>
    <?php if(mysqli_num_rows($result)>0): ?>
    <table>
      <thead><tr><th>Date</th><th>Title</th><th>Category</th><th>Amount</th><th>Notes</th><th>Action</th></tr></thead>
      <tbody>
      <?php while($row=mysqli_fetch_assoc($result)): ?>
        <tr>
          <td style="color:var(--text-light);font-style:italic"><?= date('d M Y',strtotime($row['expense_date'])) ?></td>
          <td style="font-weight:500"><?= htmlspecialchars($row['title']) ?></td>
          <td><span class="badge badge-ice"><?= $row['category'] ?></span></td>
          <td><span class="amount">₹<?= number_format($row['amount'],2) ?></span></td>
          <td style="color:var(--text-light);font-size:13px;font-style:italic"><?= htmlspecialchars($row['notes']) ?></td>
          <td><a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Delete this expense?')" class="btn btn-red btn-sm">🗑️</a></td>
        </tr>
      <?php endwhile; ?>
      </tbody>
    </table>
    <?php else: ?>
    <div class="empty">
      <div class="empty-icon">💳</div>
      <h3>No expenses yet</h3>
      <p>Start tracking by adding your first expense</p>
      <br><a href="add_expense.php" class="btn">+ Add First Expense</a>
    </div>
    <?php endif; ?>
  </div>
</div>
</body>
</html>