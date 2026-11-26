<?php
require_once __DIR__ . '/includes/session.php';
require_login_page();
$active = 'vital';
$page_title = 'Vital Task';
require __DIR__ . '/includes/layout_top.php';
?>
      <h1 class="page-heading">Vital Tasks <span class="wave">🔥</span></h1>
      <p class="page-sub">Les tâches marquées en priorité <strong>Haute</strong> apparaissent ici.</p>

      <section class="panel">
        <div class="panel-head">
          <div class="panel-title-group">
            <?= icon_svg('alert') ?>
            <h2>Priorité haute</h2>
          </div>
          <button type="button" class="add-task-link" id="quickAddBtnVital"><?= icon_svg('plus') ?> Add task</button>
        </div>

        <div id="vitalList" class="task-cards task-cards-wide">
          <p class="loading">Chargement…</p>
        </div>
      </section>
<?php require __DIR__ . '/includes/layout_bottom.php'; ?>
 