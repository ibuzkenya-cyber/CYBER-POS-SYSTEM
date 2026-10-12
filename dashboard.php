<?php require 'includes/header.php';
$today=date('Y-m-d');
$q=$pdo->prepare("SELECT COALESCE(SUM(total),0) v FROM sales WHERE DATE(created_at)=?");$q->execute([$today]);$sales=$q->fetch()['v'];
$q=$pdo->prepare("SELECT COALESCE(SUM(amount_paid),0) v FROM sales WHERE DATE(created_at)=?");$q->execute([$today]);$paid=$q->fetch()['v'];
$q=$pdo->prepare("SELECT COALESCE(SUM(amount),0) v FROM expenses WHERE DATE(created_at)=?");$q->execute([$today]);$expenses=$q->fetch()['v'];
$cy=$pdo->prepare("SELECT COALESCE(SUM(si.line_total),0) v FROM sale_items si JOIN sales s ON s.id=si.sale_id WHERE DATE(s.created_at)=? AND si.item_type='service'");$cy->execute([$today]);$cyber=$cy->fetch()['v'];
$low=$pdo->query("SELECT COUNT(*) c FROM products WHERE active=1 AND stock_qty<=reorder_level")->fetch()['c'];
$bal=$pdo->query("SELECT COALESCE(SUM(balance),0) b FROM sales WHERE balance>0")->fetch()['b'];
?>
<h1>Dashboard</h1><div class="cards">
<div class="card"><h3>Today's Sales</h3><div class="big">KSh <?=money($sales)?></div></div>
<div class="card"><h3>Cyber Income</h3><div class="big">KSh <?=money($cyber)?></div></div>
<div class="card"><h3>Amount Collected</h3><div class="big">KSh <?=money($paid)?></div></div>
<div class="card"><h3>Expenses</h3><div class="big">KSh <?=money($expenses)?></div></div>
<div class="card"><h3>Estimated Profit</h3><div class="big">KSh <?=money($sales-$expenses)?></div></div>
<div class="card"><h3>Low Stock</h3><div class="big low"><?=$low?></div></div>
<div class="card"><h3>Outstanding Balances</h3><div class="big">KSh <?=money($bal)?></div></div></div>
<div class="panel"><h2>Quick Actions</h2><div class="row3"><a class="btn" href="pos.php">Open POS</a><a class="btn" href="products.php">Manage Stock</a><a class="btn" href="reports.php">View Reports</a></div></div>
<?php require 'includes/footer.php';?>