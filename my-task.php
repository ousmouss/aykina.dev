<?php
require_once __DIR__ . '/includes/session.php';
require_login_page();
$active = 'mytask';
$page_title = 'My Task';
require __DIR__ . '/includes/layout_top.php';
?>
      <h1 class="page-heading">My Task</h1>
      <p class="page-sub">Toutes tes tâches, avec filtres et recherche.</p>

      <section class="panel">
        <div class="panel-head">
          <div class="panel-title-group">
            <?= icon_svg('check') ?>
            <h2>Mes tâches</h2>
          </div>
          <button type="button" class="add-task-link" id="quickAddBtnMyTask"><?= icon_svg('plus') ?> Add task</button>
        </div>

        <div class="filters-row">
          <div class="status-filters" role="group" aria-label="Filtrer par statut">
            <button type="button" class="chip active" data-status="">Toutes</button>
            <button type="button" class="chip" data-status="non_commence">Non commencé</button>
            <button type="button" class="chip" data-status="en_cours">En cours</button>
            <button type="button" class="chip" data-status="termine">Terminé</button>
          </div>
          <select id="categoryFilter" class="category-filter">
            <option value="">Toutes les catégories</option>
          </select>
        </div>

        <div id="myTaskList" class="task-cards task-cards-wide">
          <p class="loading">Chargement…</p>
        </div>
      </section>
<?php require __DIR__ . '/includes/layout_bottom.php'; ?>
 