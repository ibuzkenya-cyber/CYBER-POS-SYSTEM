<?php require_once __DIR__.'/../config/config.php'; require_login(); $set=$pdo->query("SELECT * FROM settings WHERE id=1")->fetch(); ?>
<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?=e($set['business_name'])?> POS</title><link rel="stylesheet" href="assets/style.css"></head>
<body><div class="layout"><aside><div class="brand"><?=e($set['business_name'])?><small>POS SYSTEM</small></div>
<nav>
<a href="dashboard.php">🏠 Dashboard</a><a href="pos.php">🛒 POS</a><a href="products.php">📦 Stock</a><a href="customers.php">👥 Customers</a>
<a href="expenses.php">💰 Expenses</a><a href="reports.php">📊 Reports</a><a href="settings.php">⚙ Settings</a><a href="logout.php">🚪 Logout</a>
</nav></aside><main><header class="top"><div><?=e($_SESSION['user']['full_name'])?> <span class="badge"><?=e($_SESSION['user']['role'])?></span></div></header><section class="content">
