<?php
if(session_status()!==PHP_SESSION_ACTIVE) session_start();
function require_agent(): void {
 if(empty($_SESSION['agent_id'])) { header('Location: login.php'); exit; }
}
