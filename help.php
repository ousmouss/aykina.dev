<?php
require_once __DIR__ . '/includes/session.php';
require_login_page();
$active = 'help';
$page_title = 'Help';
require __DIR__ . '/includes/layout_top.php';
?>
      <h1 class="page-heading">Aide <span class="wave">💡</span></h1>
      <p class="page-sub">Petit guide d'utilisation du dashboard.</p>

      <section class="panel help-panel">
        <div class="faq-item">
          <h3>Comment ajouter une tâche ?</h3>
          <p>Clique sur <strong>+ Add task</strong> (Dashboard, Vital Task ou My Task). Renseigne un titre, une priorité, un statut, et éventuellement une description, une échéance, une catégorie ou une image.</p>
        </div>
        <div class="faq-item">
          <h3>Quelle différence entre "Vital Task" et "My Task" ?</h3>
          <p><strong>Vital Task</strong> affiche uniquement les tâches de priorité <em>Haute</em>. <strong>My Task</strong> liste toutes tes tâches, avec des filtres par statut, catégorie et une recherche.</p>
        </div>
        <div class="faq-item">
          <h3>À quoi servent les catégories ?</h3>
          <p>Elles permettent de regrouper tes tâches (ex. Travail, Perso, Études) et de filtrer la liste dans <strong>My Task</strong>. Gère-les depuis <strong>Task Categories</strong>.</p>
        </div>
        <div class="faq-item">
          <h3>Comment sont calculés les donuts "Task Status" ?</h3>
          <p>Ils reflètent la répartition de toutes tes tâches entre <span class="tag tag-green">Terminé</span>, <span class="tag tag-blue">En cours</span> et <span class="tag tag-red">Non commencé</span>, en pourcentage.</p>
        </div>
        <div class="faq-item">
          <h3>Mes données sont-elles privées ?</h3>
          <p>Oui. Chaque compte ne voit et ne modifie que ses propres tâches et catégories, grâce à l'authentification par session.</p>
        </div>
      </section>
<?php require __DIR__ . '/includes/layout_bottom.php'; ?>
 