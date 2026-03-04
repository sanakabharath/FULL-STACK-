<?php
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }
include 'db.php';
$user_id  = $_SESSION['user_id'];
$username = $_SESSION['username'];

$total_r  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(amount) as t FROM expenses WHERE user_id='$user_id'"));
$total    = $total_r['t'] ?? 0;
$month_r  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(amount) as t FROM expenses WHERE user_id='$user_id' AND MONTH(expense_date)=MONTH(NOW()) AND YEAR(expense_date)=YEAR(NOW())"));
$month    = $month_r['t'] ?? 0;
$count_r  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM expenses WHERE user_id='$user_id'"));
$count    = $count_r['c'] ?? 0;
$top_r    = mysqli_fetch_assoc(mysqli_query($conn, "SELECT c.name, SUM(e.amount) as t FROM expenses e JOIN categories c ON e.category_id=c.id WHERE e.user_id='$user_id' GROUP BY c.name ORDER BY t DESC LIMIT 1"));
$top_cat  = $top_r['name'] ?? '—';
$recent   = mysqli_query($conn, "SELECT e.*, c.name as category FROM expenses e JOIN categories c ON e.category_id=c.id WHERE e.user_id='$user_id' ORDER BY e.created_at DESC LIMIT 6");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Dashboard — TrackWise</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="bg-scene"></div>
<div class="bg-crystals">
  <div class="crystal-shape"></div>
  <div class="crystal-shape"></div>
  <div class="crystal-shape"></div>
  <div class="crystal-shape"></div>
  <div class="crystal-shape"></div>
  <div class="crystal-shape"></div>
</div>
<div class="snow"></div>

<nav class="topbar">
  <a href="index.php" class="topbar-brand">
    <div class="t-icon">💰</div>
    <div class="t-name">TRACKWISE</div>
  </a>
  <div class="topbar-nav">
    <a href="add_expense.php" class="btn btn-sm">+ Add Expense</a>
    <a href="expenses.php" class="btn btn-ghost btn-sm">📋 Expenses</a>
    <a href="analytics.php" class="btn btn-ghost btn-sm">📊 Analytics</a>
    <a href="logout.php" class="btn btn-red btn-sm">Logout</a>
  </div>
  <div class="topbar-user">
    <div class="avatar"><?= strtoupper(substr($username,0,1)) ?></div>
    <span><?= htmlspecialchars($username) ?></span>
  </div>
</nav>

<div class="main-content">
  <div class="page-header">
    <h1>Good <?= date('H')<12?'Morning':(date('H')<17?'Afternoon':'Evening') ?>, <span><?= htmlspecialchars($username) ?></span> 🧊</h1>
    <p>Your financial overview for <?= date('l, F j, Y') ?></p>
  </div>

  <!-- HERO BANNER -->
  <div style="
    border-radius:20px; padding:40px 50px; margin-bottom:30px;
    position:relative; overflow:hidden;
    background: linear-gradient(135deg, #0a1628 0%, #112447 60%, #0d1f3c 100%);
    border:1.5px solid rgba(168,212,237,0.2);
    animation:fadeUp 0.5s ease;
    box-shadow:0 20px 60px rgba(10,22,40,0.2);
  ">
    <div style="position:absolute;inset:0;background:url('https://images.unsplash.com/photo-1491555103944-7c647fd857e6?w=1200&q=60') center/cover;opacity:0.08;"></div>
    <div style="position:absolute;top:0;left:0;right:0;height:2px;background:linear-gradient(90deg,transparent,#64b5f6,#80deea,transparent);"></div>
    <div style="position:relative;z-index:1;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:20px;">
      <div>
        <div style="font-family:'Syne',sans-serif;font-size:11px;color:#80deea;text-transform:uppercase;letter-spacing:3px;margin-bottom:10px;font-weight:700;">Total Lifetime Spending</div>
        <div style="font-family:'Syne',sans-serif;font-size:52px;font-weight:800;color:white;line-height:1;">₹<?= number_format($total,2) ?></div>
        <div style="font-size:13px;color:rgba(255,255,255,0.4);margin-top:8px;font-style:italic;"><?= $count ?> transactions recorded</div>
      </div>
      <a href="add_expense.php" class="btn" style="padding:16px 35px;font-size:15px;">+ New Expense</a>
    </div>
  </div>

  <!-- STATS -->
  <div class="stats-grid">
    <div class="stat-card card-blue" style="animation-delay:0.1s">
      <div class="card-icon">💸</div>
      <div class="card-label">This Month</div>
      <div class="card-value">₹<?= number_format($month,0) ?></div>
      <div class="card-sub"><?= date('F Y') ?></div>
    </div>
    <div class="stat-card card-teal" style="animation-delay:0.2s">
      <div class="card-icon">📊</div>
      <div class="card-label">Transactions</div>
      <div class="card-value"><?= $count ?></div>
      <div class="card-sub">Total recorded</div>
    </div>
    <div class="stat-card card-ice" style="animation-delay:0.3s">
      <div class="card-icon">🏆</div>
      <div class="card-label">Top Category</div>
      <div class="card-value" style="font-size:20px;margin-top:4px;"><?= $top_cat ?></div>
      <div class="card-sub">Highest spending</div>
    </div>
    <div class="stat-card card-deep" style="animation-delay:0.4s">
      <div class="card-icon">📅</div>
      <div class="card-label">Avg Per Month</div>
      <div class="card-value">₹<?= $count>0?number_format($total/max(1,(int)date('n')),0):0 ?></div>
      <div class="card-sub">Estimated average</div>
    </div>
  </div>

  <!-- RECENT -->
  <div class="panel">
    <div class="panel-header">
      <div class="panel-title">Recent Transactions</div>
      <a href="expenses.php" class="btn btn-ghost btn-sm">View All →</a>
    </div>
    <?php if(mysqli_num_rows($recent)>0): ?>
    <table>
      <thead><tr><th>Date</th><th>Title</th><th>Category</th><th>Amount</th></tr></thead>
      <tbody>
      <?php while($row=mysqli_fetch_assoc($recent)): ?>
        <tr>
          <td style="color:var(--text-light);font-style:italic"><?= date('d M Y',strtotime($row['expense_date'])) ?></td>
          <td style="font-weight:500"><?= htmlspecialchars($row['title']) ?></td>
          <td><span class="badge badge-ice"><?= $row['category'] ?></span></td>
          <td><span class="amount">₹<?= number_format($row['amount'],2) ?></span></td>
        </tr>
      <?php endwhile; ?>
      </tbody>
    </table>
    <?php else: ?>
    <div class="empty">
      <div class="empty-icon">💳</div>
      <h3>No expenses yet</h3>
      <p>Add your first expense to get started</p>
      <br><a href="add_expense.php" class="btn">+ Add Expense</a>
    </div>
    <?php endif; ?>
  </div>
</div>
</body>
</html>