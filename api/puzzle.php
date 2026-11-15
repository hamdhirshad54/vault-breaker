<?php
// Banana API Integration - Fetches live mathematical puzzles
require_once __DIR__.'/../includes/auth.php';require_agent();header('Content-Type: application/json; charset=utf-8');
function get_banana(string $url): array {
 if(function_exists('curl_init')){$ch=curl_init($url);curl_setopt_array($ch,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_TIMEOUT=>20,CURLOPT_CONNECTTIMEOUT=>10,CURLOPT_FOLLOWLOCATION=>true,CURLOPT_SSL_VERIFYPEER=>true]);$body=curl_exec($ch);$code=(int)curl_getinfo($ch,CURLINFO_HTTP_CODE);$err=curl_error($ch);curl_close($ch);if($body===false||$code!==200)return [false,$err?:'Banana API HTTP '.$code];return [$body,null];}
 $ctx=stream_context_create(['http'=>['timeout'=>20,'ignore_errors'=>true,'header'=>"User-Agent: VaultBreaker/1.0\r\n"],'ssl'=>['verify_peer'=>true,'verify_peer_name'=>true]]);
 $body=@file_get_contents($url,false,$ctx);if($body===false)return [false,'PHP streams could not reach API. Check allow_url_fopen and internet access.'];return [$body,null];
}
[$body,$err]=get_banana('https://marcconrad.com/uob/banana/api.php?out=json');
if($body===false){http_response_code(502);echo json_encode(['success'=>false,'error'=>$err]);exit;}
$data=json_decode($body,true);$image=is_array($data)?($data['question']??$data['image']??$data['img']??null):null;$answer=is_array($data)?($data['solution']??$data['answer']??$data['result']??null):null;
if(!$image||$answer===null||!is_numeric($answer)){http_response_code(502);echo json_encode(['success'=>false,'error'=>'Unexpected Banana API response.','response'=>$data]);exit;}
$level=max(1,min(10,(int)($_GET['level']??1)));$lock=max(1,min(3,(int)($_GET['lock']??1)));
$_SESSION['active_puzzle']=['answer'=>(string)$answer,'level'=>$level,'lock'=>$lock,'issued'=>time()];
echo json_encode(['success'=>true,'image'=>$image,'level'=>$level,'lock'=>$lock,'source'=>'Banana API']);
