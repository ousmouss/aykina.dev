<?php
require_once __DIR__ . '/includes/session.php';
require_login_page();
$active = 'categories';
$page_title = 'Task Categories';
require __DIR__ . '/includes/layout_top.php';
?>
      <h1 class="page-heading">Task Categories</h1>
      <p class="page-sub">Organise tes tâches par catégorie (ex. Travail, Perso, Études…).</p>

      <section class="panel">
        <div class="panel-head">
          <div class="panel-title-group">
            <?= icon_svg('list') ?>
            <h2>Nouvelle catégorie</h2>
          </div>
        </div> 

        <form id="categoryForm" class="composer-row">
          <input type="text" id="categoryName" placeholder="Nom de la catégorie…" maxlength="80" required>
          <input type="color" id="categoryColor" value="#F4645C" title="Couleur">
          <button type="submit">Ajouter</button>
        </form>
      </section>

      <section class="panel">
        <div class="panel-head">
          <div class="panel-title-group">
            <h2>Mes catégories</h2>
          </div>
        </div>
        <div id="categoriesList" class="category-grid">
          <p class="loading">Chargement…</p>
        </div>
      </section>
<?php require __DIR__ . '/includes/layout_bottom.php'; ?>
