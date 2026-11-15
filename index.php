<?php
session_start();
header('Location: '.(!empty($_SESSION['agent_id'])?'dashboard.php':'login.php')); exit;
