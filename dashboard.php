<?php
require_once __DIR__ . '/includes/session.php';
require_login_page();
$active = 'dashboard';
$page_title = 'Dashboard';
require __DIR__ . '/includes/layout_top.php';
?>
      <h1 class="page-heading">Bon retour, <?= htmlspecialchars(explode(' ', $username)[0]) ?> <span class="wave">👋</span></h1>

      <div class="dash-grid">

        <!-- ===== To-Do (aujourd'hui) ===== -->
        <section class="panel todo-panel">
          <div class="panel-head">
            <div class="panel-title-group">
              <?= icon_svg('clipboard') ?>
              <h2>To-Do</h2>
            </div>
            <button type="button" class="add-task-link" id="quickAddBtn"><?= icon_svg('plus') ?> Ajouter une tache</button>
          </div>
          <div class="panel-subdate"><?= french_date_short() ?> <span class="dot">•</span> Today</div>

          <div id="todoTodayList" class="task-cards">
            <p class="loading">Chargement…</p>
          </div>
        </section>

        <!-- ===== Task Status + Completed Task ===== -->
        <div class="side-stack">
          <section class="panel status-panel">
            <div class="panel-head">
              <div class="panel-title-group">
                <?= icon_svg('clipboard') ?>
                <h2>Statue des taches</h2>
              </div>
            </div>
            <div id="statusDonuts" class="donuts">
              <p class="loading">Chargement…</p>
            </div>
          </section>

          <section class="panel completed-panel">
            <div class="panel-head">
              <div class="panel-title-group">
                <?= icon_svg('check') ?>
                <h2>Taches complétées</h2>
              </div>
            </div>
            <div id="completedList" class="task-cards task-cards-compact">
              <p class="loading">Chargement…</p>
            </div>
        
        </section>
        </div>

      </div>

      <div class="widgets-row">

        <!-- ===== Répartition par catégorie ===== -->
        <section class="panel category-breakdown-panel">
          <div class="panel-head">
            <div class="panel-title-group">
              <?= icon_svg('clipboard') ?>
              <h2>Par catégorie</h2>
            </div>
          </div>
          <div id="categoryBreakdown" class="category-legend">
            <p class="loading">Chargement…</p>
          </div>
        </section>

        <!-- ===== Échéances à venir ===== -->
        <section class="panel deadlines-panel">
          <div class="panel-head">
            <div class="panel-title-group">
              <?= icon_svg('clipboard') ?>
              <h2>Échéances à venir</h2>
            </div>
          </div>
          <div id="upcomingDeadlines" class="deadline-list">
            <p class="loading">Chargement…</p>
          </div>
        </section>

        <!-- ===== Série active ===== -->
        <section class="panel streak-panel">
          <div class="panel-head">
            <div class="panel-title-group">
              <?= icon_svg('check') ?>
              <h2>Série active</h2>
            </div>
          </div>
          <div id="streakCount">
            <p class="loading">Chargement…</p>
          </div>
        </section>

      </div>
<?php require __DIR__ . '/includes/layout_bottom.php'; ?>