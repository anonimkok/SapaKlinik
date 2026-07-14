<?php
require_once '../config/database.php';

$_SESSION = [];
session_destroy();
redirect('login.php');
?>
