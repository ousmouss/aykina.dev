    </main>
  </div>
</div>

<!-- Modal partagée : ajout / édition de tâche (utilisée sur Dashboard, Vital Task, My Task) -->
<div class="modal-overlay" id="taskModalOverlay">
  <div class="modal" role="dialog" aria-modal="true" aria-labelledby="taskModalTitle">
    <div class="modal-head">
      <h3 id="taskModalTitle">Nouvelle tâche</h3>
      <button type="button" class="modal-close" id="taskModalClose" aria-label="Fermer">✕</button>
    </div>
    <form id="taskForm">
      <input type="hidden" id="taskId">

      <label for="taskTitle">Titre</label>
      <input type="text" id="taskTitle" required maxlength="150">

      <label for="taskDescription">Description</label>
      <textarea id="taskDescription" rows="3" maxlength="1000"></textarea>

      <div class="form-row">
        <div>
          <label for="taskPriority">Priorité</label>
          <select id="taskPriority">
            <option value="faible">Faible</option>
            <option value="moderee" selected>Modérée</option>
            <option value="haute">Haute</option>
          </select>
        </div>
        <div>
          <label for="taskStatus">Statut</label>
          <select id="taskStatus">
            <option value="non_commence">Non commencé</option>
            <option value="en_cours">En cours</option>
            <option value="termine">Terminé</option>
          </select>
        </div>
      </div>

      <div class="form-row">
        <div>
          <label for="taskDueDate">Échéance</label>
          <input type="date" id="taskDueDate">
        </div>
        <div>
          <label for="taskCategory">Catégorie</label>
          <select id="taskCategory">
            <option value="">Aucune</option>
          </select>
        </div>
      </div>

      <label for="taskImage">Image (URL, optionnel)</label>
      <input type="url" id="taskImage" placeholder="https://…">

      <button type="submit" class="modal-submit">Enregistrer</button>
    </form>
  </div>
</div>

<script src="assets/js/app.js"></script>
<script src="assets/js/widgets.js" defer></script>
</body>
</html>
</body>
</html>
