<?php
session_start(); require_once __DIR__.'/config/database.php'; $error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
 $codename=trim($_POST['codename']??''); $password=$_POST['password']??'';
 if(!preg_match('/^[A-Za-z0-9_-]{3,20}$/',$codename)) $error='Use 3–20 letters, numbers, underscores or hyphens.';
 elseif(strlen($password)<6) $error='Password must be at least 6 characters.';
 else { try{$q=$pdo->prepare('INSERT INTO agents(codename,password) VALUES(?,?)');$q->execute([$codename,password_hash($password,PASSWORD_DEFAULT)]);header('Location: login.php?registered=1');exit;}
 catch(PDOException $e){$error=$e->getCode()==='23000'?'That codename is already taken.':'Registration failed.';} }
}
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Register | Vault Breaker</title><link rel="stylesheet" href="assets/css/style.css"></head><body class="auth-page"><main class="auth-card"><div class="brand">VAULT<br>BREAKER</div><p class="eyebrow">CYBER OPERATIONS DIVISION</p><h1>CREATE AGENT ID</h1><?php if($error):?><p class="notice error"><?=htmlspecialchars($error)?></p><?php endif;?><form method="post"><label>CODENAME</label><input name="codename" required minlength="3" maxlength="20" placeholder="Shadow01"><label>PASSWORD</label><input type="password" name="password" required minlength="6" placeholder="At least 6 characters"><button class="btn" type="submit">CREATE IDENTITY</button></form><p class="muted">Already registered? <a href="login.php">Access system</a></p></main></body></html>
