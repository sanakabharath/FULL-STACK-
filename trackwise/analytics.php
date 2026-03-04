<?php
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }
include 'db.php';
$user_id  = $_SESSION['user_id'];
$username = $_SESSION['username'];

$cat_data = mysqli_query($conn, "SELECT c.name, SUM(e.amount) as total FROM expenses e JOIN categories c ON e.category_id=c.id WHERE e.user_id='$user_id' GROUP BY c.name ORDER BY total DESC");
$labels=[]; $amounts=[];
while($row=mysqli_fetch_assoc($cat_data)){ $labels[]=$row['name']; $amounts[]=(float)$row['total']; }

$monthly = mysqli_query($conn, "SELECT DATE_FORMAT(expense_date,'%b %Y') as month, SUM(amount) as total FROM expenses WHERE user_id='$user_id' GROUP BY YEAR(expense_date),MONTH(expense_date) ORDER BY expense_date ASC");
$months=[]; $mtotals=[];
while($row=mysqli_fetch_assoc($monthly)){ $months[]=$row['month']; $mtotals[]=(float)$row['total']; }

$grand_r = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(amount) as t, COUNT(*) as c FROM expenses WHERE user_id='$user_id'"));
$grand=$grand_r['t']??0; $count=$grand_r['c']??0;
$month_r = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(amount) as t FROM expenses WHERE user_id='$user_id' AND MONTH(expense_date)=MONTH(NOW()) AND YEAR(expense_date)=YEAR(NOW())"));
$mspend=$month_r['t']??0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Analytics — TrackWise</title>
<link rel="stylesheet" href="css/style.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
    <a href="expenses.php" class="btn btn-ghost btn-sm">📋 Expenses</a>
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
    <h1>Expense <span>Analytics</span></h1>
    <p>Crystal-clear insights into your spending patterns</p>
  </div>

  <div class="stats-grid" style="margin-bottom:25px;">
    <div class="stat-card card-blue">
      <div class="card-icon">💰</div>
      <div class="card-label">Total Spent</div>
      <div class="card-value">₹<?= number_format($grand,0) ?></div>
    </div>
    <div class="stat-card card-teal">
      <div class="card-icon">📅</div>
      <div class="card-label">This Month</div>
      <div class="card-value">₹<?= number_format($mspend,0) ?></div>
    </div>
    <div class="stat-card card-ice">
      <div class="card-icon">🔢</div>
      <div class="card-label">Transactions</div>
      <div class="card-value"><?= $count ?></div>
    </div>
    <div class="stat-card card-deep">
      <div class="card-icon">📈</div>
      <div class="card-label">Avg Transaction</div>
      <div class="card-value">₹<?= $count>0?number_format($grand/$count,0):0 ?></div>
    </div>
  </div>

  <div class="charts-grid">
    <div class="chart-panel">
      <h3>🍩 Spending by Category</h3>
      <canvas id="pieChart" height="260"></canvas>
    </div>
    <div class="chart-panel">
      <h3>📊 Monthly Trend</h3>
      <canvas id="barChart" height="260"></canvas>
    </div>
  </div>

  <?php if(count($labels)>0): ?>
  <div class="panel">
    <div class="panel-header">
      <div class="panel-title">Category Breakdown</div>
    </div>
    <table>
      <thead><tr><th>Category</th><th>Amount</th><th>% of Total</th><th>Bar</th></tr></thead>
      <tbody>
      <?php foreach($labels as $i=>$label):
        $pct=$grand>0?round(($amounts[$i]/$grand)*100,1):0; ?>
        <tr>
          <td><span class="badge badge-ice"><?= $label ?></span></td>
          <td><span class="amount">₹<?= number_format($amounts[$i],2) ?></span></td>
          <td style="color:var(--text-light)"><?= $pct ?>%</td>
          <td style="width:200px;">
            <div style="background:var(--ice-2);border-radius:4px;height:8px;overflow:hidden;">
              <div style="width:<?= $pct ?>%;height:100%;background:linear-gradient(90deg,var(--accent),var(--teal));border-radius:4px;transition:width 1s ease;"></div>
            </div>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</div>

<script>
const iceColors = ['#2196f3','#00bcd4','#64b5f6','#80deea','#1565c0','#00838f','#42a5f5','#26c6da'];

new Chart(document.getElementById('pieChart'),{
  type:'doughnut',
  data:{
    labels:<?= json_encode($labels) ?>,
    datasets:[{data:<?= json_encode($amounts) ?>,backgroundColor:iceColors,borderColor:'#ffffff',borderWidth:3,hoverOffset:8}]
  },
  options:{responsive:true,cutout:'65%',plugins:{legend:{position:'bottom',labels:{color:'#2c4a6e',padding:16,font:{family:'Literata',size:12}}}}}
});

new Chart(document.getElementById('barChart'),{
  type:'bar',
  data:{
    labels:<?= json_encode($months) ?>,
    datasets:[{label:'Monthly Expenses (₹)',data:<?= json_encode($mtotals) ?>,backgroundColor:'rgba(33,150,243,0.15)',borderColor:'#2196f3',borderWidth:2,borderRadius:8,hoverBackgroundColor:'rgba(33,150,243,0.3)'}]
  },
  options:{responsive:true,plugins:{legend:{labels:{color:'#2c4a6e',font:{family:'Literata'}}}},scales:{x:{ticks:{color:'#6b8cad'},grid:{color:'rgba(168,212,237,0.3)'}},y:{ticks:{color:'#6b8cad'},grid:{color:'rgba(168,212,237,0.3)'}}}}
});
</script>
</body>
</html>