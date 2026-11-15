<?php
require_once __DIR__.'/../includes/auth.php';require_agent();require_once __DIR__.'/../config/database.php';header('Content-Type: application/json; charset=utf-8');
if($_SERVER['REQUEST_METHOD']!=='POST'){http_response_code(405);echo json_encode(['success'=>false,'error'=>'POST required']);exit;}
$input=json_decode(file_get_contents('php://input'),true)?:[];$guess=trim((string)($input['answer']??''));$p=$_SESSION['active_puzzle']??null;
if(!$p||time()-(int)$p['issued']>180){http_response_code(400);echo json_encode(['success'=>false,'error'=>'Puzzle expired. Request a new puzzle.']);exit;}
if($guess===''||!hash_equals((string)$p['answer'],$guess)){echo json_encode(['success'=>true,'correct'=>false,'message'=>'Incorrect solution. Security alert increased.']);exit;}
$agent=(int)$_SESSION['agent_id'];$level=(int)$p['level'];$lock=(int)$p['lock'];$xp=$lock===3?100:25;
try{$pdo->beginTransaction();$q=$pdo->prepare('INSERT INTO progress(agent_id,level_number,completed_locks,completed) VALUES(?,?,?,?) ON DUPLICATE KEY UPDATE completed_locks=GREATEST(completed_locks,VALUES(completed_locks)),completed=GREATEST(completed,VALUES(completed))');$q->execute([$agent,$level,$lock,$lock===3?1:0]);$q=$pdo->prepare('UPDATE agents SET xp=xp+?,clearance=CASE WHEN xp+?>=700 THEN "Ghost" WHEN xp+?>=250 THEN "Operator" ELSE "Rookie" END WHERE id=?');$q->execute([$xp,$xp,$xp,$agent]);$pdo->commit();}catch(Throwable $e){$pdo->rollBack();http_response_code(500);echo json_encode(['success'=>false,'error'=>'Could not save progress.']);exit;}
unset($_SESSION['active_puzzle']);echo json_encode(['success'=>true,'correct'=>true,'message'=>$lock===3?'VAULT CLEARED!':'LOCK CRACKED!','xp'=>$xp,'vaultComplete'=>$lock===3]);
