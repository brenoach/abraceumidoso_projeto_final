<?php
require_once __DIR__ . '/../includes/session.php';
logout_user();
header('Location: login.php');
exit;
