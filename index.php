<?php
require_once __DIR__ . '/includes/session.php';

header('Location: ' . (isset($_SESSION['user_id']) ? 'dashboard.php' : 'login.php'));
exit;
 