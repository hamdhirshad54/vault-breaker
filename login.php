<?php
session_start(); require_once __DIR__.'/config/database.php'; $error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
 $codename=trim($_POST['codename']??''); $password=$_POST['password']??'';
 $q=$pdo->prepare('SELECT id,codename,password,clearance FROM agents WHERE codename=?');$q->execute([$codename]);$agent=$q->fetch();
 if($agent && password_verify($password,$agent['password'])){
  session_regenerate_id(true);$_SESSION['agent_id']=(int)$agent['id'];$_SESSION['codename']=$agent['codename'];$_SESSION['clearance']=$agent['clearance'];header('Location: dashboard.php');exit;
 } $error='Invalid codename or password.';
}
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Login | Vault Breaker</title><link rel="stylesheet" href="assets/css/style.css"></head><body class="auth-page"><main class="auth-card"><div class="brand">VAULT<br>BREAKER</div><p class="eyebrow">CYBER OPERATIONS DIVISION</p><h1>AGENT AUTHENTICATION</h1><?php if(isset($_GET['registered'])):?><p class="notice success">Identity created. Please log in.</p><?php endif;?><?php if($error):?><p class="notice error"><?=htmlspecialchars($error)?></p><?php endif;?><form method="post"><label>CODENAME</label><input name="codename" required placeholder="Agent codename"><label>PASSWORD</label><input type="password" name="password" required placeholder="Access password"><button class="btn" type="submit">ACCESS SYSTEM</button></form><p class="muted">New agent? <a href="register.php">Create identity</a></p></main></body></html>
