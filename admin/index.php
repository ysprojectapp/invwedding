<?php
require_once __DIR__ . '/../includes/functions.php';
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
if (empty($_SESSION['admin_id'])) { header('Location: /admin/login'); exit; }
require __DIR__ . '/dashboard.php';
