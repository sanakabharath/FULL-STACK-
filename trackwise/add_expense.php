<?php
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }
include 'db.php';
$user_id  = $_SESSION['user_id'];
$username = $_SESSION['username'];
$cats = mysqli_query($conn, "SELECT * FROM categories");
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title=$_POST['title']; $amount=$_POST['amount'];
    $category_id=$_POST['category_id']; $date=$_POST['expense_date']; $notes=$_POST['notes'];
    mysqli_query($conn, "INSERT INTO expenses (user_id,category_id,title,amount,expense_date,notes) VALUES ('$user_id','$category_id','$title','$amount','$date','$notes')");
    header("Location: expenses.php"); exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Add Expense — TrackWise</title>
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
    <a href="expenses.php" class="btn btn-ghost btn-sm">📋 Expenses</a>
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
    <h1>Add <span>New Expense</span></h1>
    <p>Record a new transaction to keep your finances crystal clear</p>
  </div>

  <div style="display:grid;grid-template-columns:1fr 1fr;gap:25px;align-items:start;">
    <!-- FORM -->
    <div class="panel">
      <div class="panel-header">
        <div class="panel-title">Expense Details</div>
      </div>
      <form method="POST">
        <div class="input-group">
          <label>Expense Title</label>
          <input type="text" name="title" placeholder="e.g. Lunch at restaurant" required>
        </div>
        <div class="input-group">
          <label>Amount (₹)</label>
          <input type="number" step="0.01" name="amount" placeholder="0.00" required>
        </div>
        <div class="input-group">
          <label>Category</label>
          <select name="category_id" required>
            <option value="">Select a category</option>
            <?php while($cat=mysqli_fetch_assoc($cats)): ?>
              <option value="<?= $cat['id'] ?>"><?= $cat['name'] ?></option>
            <?php endwhile; ?>
          </select>
        </div>
        <div class="input-group">
          <label>Date</label>
          <input type="date" name="expense_date" value="<?= date('Y-m-d') ?>" required>
        </div>
        <div class="input-group">
          <label>Notes (Optional)</label>
          <textarea name="notes" placeholder="Any additional details..."></textarea>
        </div>
        <button type="submit" class="btn btn-full">Save Expense →</button>
      </form>
    </div>

    <!-- INFO SIDE -->
    <div>
      <div style="border-radius:16px;overflow:hidden;margin-bottom:20px;height:220px;background:url('https://images.unsplash.com/photo-1554224155-6726b3ff858f?w=600&q=80') center/cover;position:relative;">
        <div style="position:absolute;inset:0;background:linear-gradient(to top,rgba(10,22,40,0.85),rgba(10,22,40,0.2));"></div>
        <div style="position:absolute;bottom:20px;left:20px;right:20px;">
          <div style="font-family:'Syne',sans-serif;font-size:20px;font-weight:800;color:white;">Every Rupee Counts</div>
          <div style="font-size:13px;color:rgba(255,255,255,0.5);margin-top:4px;font-style:italic;">Crystal clear finances start here</div>
        </div>
      </div>

      <div class="panel">
        <div class="panel-title" style="margin-bottom:16px;">💡 Categories</div>
        <div style="display:flex;flex-direction:column;gap:12px;">
          <?php
          $tips = [['🍽️','Food & Dining','Meals, groceries, snacks','card-blue'],['🚗','Transport','Fuel, cab, bus, metro','card-teal'],['🏥','Health','Medicine, doctor, gym','card-ice'],['🛍️','Shopping','Clothes, gadgets, misc','card-deep']];
          foreach($tips as $t):
          ?>
          <div style="display:flex;gap:12px;align-items:center;">
            <div style="width:36px;height:36px;background:var(--ice-2);border:1px solid var(--ice-3);border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:16px;flex-shrink:0;"><?= $t[0] ?></div>
            <div>
              <div style="font-family:'Syne',sans-serif;font-size:13px;font-weight:700;color:var(--text-dark)"><?= $t[1] ?></div>
              <div style="font-size:12px;color:var(--text-light);font-style:italic"><?= $t[2] ?></div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>
</div>
</body>
</html>