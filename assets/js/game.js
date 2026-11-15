(()=>{const $=id=>document.getElementById(id);let level=1,lock=1,alert=0,seconds=60,timer=null,pressure=null,busy=false,frozen=false;
function log(t){let li=document.createElement('li');li.textContent=t;$('eventLog').prepend(li);while($('eventLog').children.length>6)$('eventLog').lastChild.remove();}
function setAlert(n){
const previous=alert;
alert=Math.min(100,Math.max(0,n));
$('alertValue').textContent=alert;
$('alertFill').style.width=alert+'%';
const warning=$('alertWarning');
const monitor=$('alertState');
$('alertFill').style.background=alert>=75?'#ff5555':alert>=40?'#ffd447':'#00f58a';
monitor.className=alert>=75?'status-danger':alert>=40?'status-warning':'status-ok';
monitor.textContent=alert>=75?'SECURITY BREACH IMMINENT':alert>=40?'SUSPICIOUS ACTIVITY':'SYSTEM QUIET';
if(alert>=75&&alert<100){
warning.hidden=false;
warning.textContent=alert>=90?'CRITICAL: TRACE NEAR COMPLETE — LOCKOUT AT 100%.':'WARNING: SECURITY TRACE DETECTED — DISABLE THE TRACE BEFORE IT REACHES 100%.';
}else{
warning.hidden=true;
warning.textContent='';
}
if(previous<75&&alert>=75)log('Warning: security trace reached 75%.');
if(previous<90&&alert>=90)log('Critical: security trace reached 90%.');
if(alert>=100)lockout();
}
function lockout(){clearInterval(timer);clearInterval(pressure);$('answer').disabled=true;$('submitAnswer').disabled=true;$('puzzleArea').innerHTML='<h2 class="danger">AGENT LOCKED OUT</h2><a class="btn" href="dashboard.php">EXIT MISSION</a>';log('LOCKOUT: alert reached 100%.');}
function event(){if(busy||alert>=100||Math.random()>.25)return;let n=Math.floor(Math.random()*3);if(n===0){frozen=true;$('answer').disabled=true;$('submitAnswer').disabled=true;$('gameMessage').textContent='GUARD PATROL — INPUT FROZEN';log('Guard patrol: input frozen for 3 seconds.');setTimeout(()=>{frozen=false;if(alert<100){$('answer').disabled=false;$('submitAnswer').disabled=false;$('gameMessage').textContent='Guard passed. Continue.';}},3000);}else if(n===1){seconds=Math.max(5,seconds-10);$('timer').textContent=seconds;log('Firewall: 10 seconds removed.');}else{seconds+=10;$('timer').textContent=seconds;log('Power surge: 10 bonus seconds.');}}
function clocks(){clearInterval(timer);clearInterval(pressure);seconds=60;$('timer').textContent=seconds;timer=setInterval(()=>{seconds--;$('timer').textContent=seconds;if(seconds<=0){clearInterval(timer);setAlert(alert+8);log('Timeout: +8 alert.');loadPuzzle();}},1000);pressure=setInterval(()=>setAlert(alert+1),12000);}
async function loadPuzzle(){if(alert>=100)return;busy=true;$('answer').disabled=true;$('submitAnswer').disabled=true;$('lockCount').textContent=`LOCK ${lock} / 3`;$('puzzleArea').textContent='CONTACTING BANANA API...';if($('feedStatus'))$('feedStatus').textContent='REQUESTING...';try{let r=await fetch(`api/puzzle.php?level=${level}&lock=${lock}`,{cache:'no-store'});let d=await r.json();if(!r.ok||!d.success)throw Error(d.error||'API request failed');let img=document.createElement('img');img.src=d.image;img.alt='Banana API puzzle';img.className='puzzle-image';$('puzzleArea').replaceChildren(img);if($('feedStatus'))$('feedStatus').textContent='SIGNAL LOCKED';$('answer').value='';$('answer').disabled=false;$('submitAnswer').disabled=false;$('gameMessage').textContent='Puzzle received. Crack the lock.';log(`Banana API puzzle loaded: Vault ${level}, Lock ${lock}.`);clocks();event();}catch(e){$('puzzleArea').textContent=e.message;if($('feedStatus'))$('feedStatus').textContent='FEED ERROR';let b=document.createElement('button');b.textContent='RETRY API';b.onclick=loadPuzzle;$('puzzleArea').append(b);log('API error: '+e.message);}finally{busy=false;}}
$('answerForm').addEventListener('submit',async e=>{e.preventDefault();if(busy||frozen||alert>=100)return;busy=true;$('answer').disabled=true;$('submitAnswer').disabled=true;try{let r=await fetch('api/answer.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({answer:$('answer').value})});let d=await r.json();if(!r.ok||!d.success)throw Error(d.error||'Answer check failed');if(d.correct){clearInterval(timer);$('gameMessage').textContent=d.message+' +'+d.xp+' XP';log(d.message);if(d.vaultComplete){$('puzzleArea').innerHTML='<h2 class="status-ok">VAULT CLEARED</h2><p>All three locks completed. Return to command center.</p><a class="btn" href="dashboard.php">COMMAND CENTER</a>';return;}lock++;setTimeout(loadPuzzle,700);}else{setAlert(alert+12);$('gameMessage').textContent=d.message;$('answer').disabled=alert>=100;$('submitAnswer').disabled=alert>=100;log('Wrong answer: +12 alert.');}}catch(err){$('gameMessage').textContent=err.message;$('answer').disabled=false;$('submitAnswer').disabled=false;}finally{busy=false;}});
$('levelSelect').addEventListener('change',()=>{level=Number($('levelSelect').value);lock=1;setAlert(0);loadPuzzle();});loadPuzzle();})();
