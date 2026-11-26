<?php
/**
 * À inclure en haut de chaque page protégée, APRÈS avoir défini :
 *   $active      -> clé du lien actif dans le menu ('dashboard', 'vital', 'mytask', 'categories', 'settings', 'help')
 *   $page_title  -> titre de la page (optionnel)
 * et après require_login_page().
 */
require_once __DIR__ . '/helpers.php';

$username = $_SESSION['username'] ?? '';
$email    = $_SESSION['email'] ?? '';

$navItems = [
    'dashboard'  => ['label' => 'Dashboard',       'href' => 'dashboard.php',       'icon' => 'grid'],
    'vital'      => ['label' => 'Vital Task',      'href' => 'vital-task.php',      'icon' => 'alert'],
    'mytask'     => ['label' => 'My Task',         'href' => 'my-task.php',         'icon' => 'check'],
    'categories' => ['label' => 'Task Categories', 'href' => 'task-categories.php', 'icon' => 'list'],
    'settings'   => ['label' => 'Settings',        'href' => 'settings.php',        'icon' => 'gear'],
    'help'       => ['label' => 'Help',            'href' => 'help.php',            'icon' => 'help'],
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= isset($page_title) ? htmlspecialchars($page_title) . ' — ' : '' ?>Dashboard</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="shell">

  <aside class="sidebar">
    <div class="profile-block">
      <div class="avatar-circle avatar-lg"><?= htmlspecialchars(initials($username)) ?></div>
      <div class="profile-name"><?= htmlspecialchars($username) ?></div>
      <?php if ($email): ?><div class="profile-email"><?= htmlspecialchars($email) ?></div><?php endif; ?>
    </div>

    <nav class="side-nav">
      <?php foreach ($navItems as $key => $item): ?>
        <a href="<?= htmlspecialchars($item['href']) ?>" class="side-link<?= ($active ?? '') === $key ? ' active' : '' ?>">
          <?= icon_svg($item['icon']) ?>
          <span><?= htmlspecialchars($item['label']) ?></span>
        </a>
      <?php endforeach; ?>
    </nav>

    <a href="logout.php" class="side-link side-logout">
      <?= icon_svg('logout') ?>
      <span>Logout</span>
    </a>
  </aside>

  <div class="main-col">
    <header class="topbar">
      <div class="brand">Dash<span>board</span></div>

      <form class="search-form" method="get" action="my-task.php">
        <input type="search" name="q" placeholder="Search your task here…" value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
        <button type="submit" aria-label="Rechercher"><?= icon_svg('search') ?></button>
      </form>

      <div class="topbar-actions">
        <button class="icon-btn" type="button" title="Notifications"><?= icon_svg('bell') ?></button>
        <button class="icon-btn" type="button" title="Calendrier"><?= icon_svg('calendar') ?></button>
        <div class="date-block">
          <div class="date-day"><?= french_day() ?></div>
          <div class="date-full"><?= date('d/m/Y') ?></div>
        </div>
      </div>
    </header>

    <main class="content">
