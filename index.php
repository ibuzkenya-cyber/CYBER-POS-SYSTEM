<?php require_once 'config/config.php'; if(logged_in()){header('Location: dashboard.php');exit;} $err='';
if($_SERVER['REQUEST_METHOD']==='POST'){ $u=trim($_POST['username']);$p=hash('sha256',$_POST['password']);
$s=$pdo->prepare("SELECT * FROM users WHERE username=? AND password_hash=? AND active=1");$s->execute([$u,$p]);$x=$s->fetch();
if($x){$_SESSION['user']=$x;header('Location: dashboard.php');exit;} $err='Invalid username or password';}
?><!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>IBUZKENYA POS Login</title><link rel="stylesheet" href="assets/style.css"></head><body>
<div class="login"><h1>IBUZKENYA POS</h1><p>Point of Sale & Cyber Services</p><?php if($err):?><div class="notice" style="background:#fdecea"><?=$err?></div><?php endif;?>
<form method="post"><label>Username</label><input name="username" required><br><br><label>Password</label><input type="password" name="password" required><br><br><button>Login</button></form>
<p><small>Default: admin / admin123</small></p></div></body></html>