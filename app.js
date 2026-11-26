// ============================================================
// Front-end : AJAX vers api/tasks.php et api/categories.php
// Toutes les fonctions se branchent uniquement si les éléments
// de la page courante existent (une seule page les charge).
// ============================================================

const priorityLabels = { faible: 'Faible', moderee: 'Modérée', haute: 'Haute' };
const statusLabels = { non_commence: 'Non commencé', en_cours: 'En cours', termine: 'Terminé' };
const statusCycle = { non_commence: 'en_cours', en_cours: 'termine', termine: 'non_commence' };

function escapeHtml(str){
  const div = document.createElement('div');
  div.textContent = str ?? '';
  return div.innerHTML;
}

function timeAgoFallback(dateStr){
  if(!dateStr) return '';
  const diffMs = Date.now() - new Date(dateStr.replace(' ', 'T')).getTime();
  const days = Math.floor(diffMs / 86400000);
  if(days <= 0) return "aujourd'hui";
  if(days === 1) return 'il y a 1 jour';
  return `il y a ${days} jours`;
}

/* ---------------------- Rendu d'une carte de tâche ---------------------- */
function renderTaskCard(t, { showCompletedTime = false } = {}){
  const done = t.status === 'termine';
  const thumb = t.image_url
    ? `<img class="task-thumb" src="${escapeHtml(t.image_url)}" alt="" onerror="this.style.display='none'">`
    : `<div class="task-thumb-placeholder">📌</div>`;

  const categoryTag = t.category_name
    ? `<span><b style="color:${escapeHtml(t.category_color || '#F4645C')}">${escapeHtml(t.category_name)}</b></span>`
    : '';

  return `
    <div class="task-card${done ? ' done' : ''}" data-status="${t.status}" data-id="${t.id}">
      <button class="status-dot" data-id="${t.id}" data-status="${t.status}" aria-label="Changer le statut"></button>
      <div class="task-body">
        <div class="task-title">${escapeHtml(t.title)}</div>
        ${t.description ? `<div class="task-desc">${escapeHtml(t.description)}</div>` : ''}
        <div class="task-meta">
          <span>Priority: <span class="meta-priority ${t.priority}">${priorityLabels[t.priority] || t.priority}</span></span>
          <span>Status: <span class="meta-status ${t.status}">${statusLabels[t.status] || t.status}</span></span>
          ${categoryTag}
          <span>Created on: ${escapeHtml((t.created_at || '').slice(0,10).split('-').reverse().join('/'))}</span>
        </div>
        ${showCompletedTime && t.completed_at ? `<div class="completed-time">Completed ${timeAgoFallback(t.completed_at)}</div>` : ''}
      </div>
      ${thumb}
      <div class="task-actions">
        <div class="task-actions-row">
          <button class="edit-btn" data-id="${t.id}" aria-label="Modifier" title="Modifier">${iconSvg('edit')}</button>
          <button class="delete-btn" data-id="${t.id}" aria-label="Supprimer" title="Supprimer">${iconSvg('trash')}</button>
        </div>
      </div>
    </div>
  `;
}

function iconSvg(name){
  const icons = {
    edit: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12.5 5.5 16 3l3 3-2.5 3.5-9 9-4 1 1-4Z"/></svg>',
    trash: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polyline points="4 7 6 7 20 7"/><path d="M9 7V4.5A1.5 1.5 0 0 1 10.5 3h3A1.5 1.5 0 0 1 15 4.5V7"/><path d="M18 7l-.8 12.5A2 2 0 0 1 15.2 21H8.8a2 2 0 0 1-2-1.9L6 7"/></svg>',
  };
  return icons[name] || '';
}

function attachTaskCardEvents(container, onChange){
  container.querySelectorAll('.status-dot').forEach(btn=>{
    btn.addEventListener('click', async ()=>{
      const next = statusCycle[btn.dataset.status] || 'non_commence';
      await fetch('api/tasks.php', {
        method:'PUT', headers:{'Content-Type':'application/json'},
        body: JSON.stringify({ id: parseInt(btn.dataset.id,10), status: next })
      });
      onChange();
    });
  });
  container.querySelectorAll('.delete-btn').forEach(btn=>{
    btn.addEventListener('click', async ()=>{
      if(!confirm('Supprimer cette tâche ?')) return;
      await fetch(`api/tasks.php?id=${btn.dataset.id}`, { method:'DELETE' });
      onChange();
    });
  });
  // Les boutons "modifier" sont branchés séparément par wireEditButtons()
  // (ils ont besoin du cache local rempli par le loader de chaque page).
}

/* ---------------------- Modal ajout/édition ---------------------- */
const modalOverlay = document.getElementById('taskModalOverlay');
let editingTaskCache = {}; // id -> task object, filled by each page's loader

function openTaskModal(task = null, defaults = {}){
  if(!modalOverlay) return;
  document.getElementById('taskModalTitle').textContent = task ? 'Modifier la tâche' : 'Nouvelle tâche';
  document.getElementById('taskId').value = task ? task.id : '';
  document.getElementById('taskTitle').value = task ? task.title : '';
  document.getElementById('taskDescription').value = task ? (task.description || '') : '';
  document.getElementById('taskPriority').value = task ? task.priority : (defaults.priority || 'moderee');
  document.getElementById('taskStatus').value = task ? task.status : (defaults.status || 'non_commence');
  document.getElementById('taskDueDate').value = task && task.due_date ? task.due_date : '';
  document.getElementById('taskImage').value = task ? (task.image_url || '') : '';
  document.getElementById('taskCategory').value = task && task.category_id ? task.category_id : '';
  modalOverlay.classList.add('open');
  document.getElementById('taskTitle').focus();
}

function closeTaskModal(){
  if(!modalOverlay) return;
  modalOverlay.classList.remove('open');
}

if(modalOverlay){
  document.getElementById('taskModalClose').addEventListener('click', closeTaskModal);
  modalOverlay.addEventListener('click', (e)=>{ if(e.target === modalOverlay) closeTaskModal(); });

  document.getElementById('taskForm').addEventListener('submit', async (e)=>{
    e.preventDefault();
    const id = document.getElementById('taskId').value;
    const payload = {
      title: document.getElementById('taskTitle').value.trim(),
      description: document.getElementById('taskDescription').value.trim(),
      priority: document.getElementById('taskPriority').value,
      status: document.getElementById('taskStatus').value,
      due_date: document.getElementById('taskDueDate').value,
      image_url: document.getElementById('taskImage').value.trim(),
      category_id: document.getElementById('taskCategory').value || null,
    };
    if(!payload.title) return;

    if(id){
      payload.id = parseInt(id, 10);
      await fetch('api/tasks.php', { method:'PUT', headers:{'Content-Type':'application/json'}, body: JSON.stringify(payload) });
    } else {
      await fetch('api/tasks.php', { method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify(payload) });
    }
    closeTaskModal();
    if(typeof window.refreshCurrentPage === 'function') window.refreshCurrentPage();
  });
}

async function populateCategorySelect(selectEl, selectedId = ''){
  if(!selectEl) return;
  try{
    const res = await fetch('api/categories.php');
    const cats = await res.json();
    selectEl.innerHTML = '<option value="">Aucune</option>' +
      cats.map(c => `<option value="${c.id}" ${String(selectedId) === String(c.id) ? 'selected' : ''}>${escapeHtml(c.name)}</option>`).join('');
  }catch(e){ /* ignore */ }
}

/* Ouvre le modal en repeuplant d'abord la liste des catégories */
async function openTaskModalWithCategories(task = null, defaults = {}){
  await populateCategorySelect(document.getElementById('taskCategory'), task ? task.category_id : '');
  openTaskModal(task, defaults);
}

/* Trouve une tâche par id dans un cache local rempli par la page courante */
function findTaskInCache(id){
  return editingTaskCache[id] || null;
}

function wireEditButtons(container){
  container.querySelectorAll('.edit-btn').forEach(btn=>{
    btn.addEventListener('click', ()=>{
      const task = findTaskInCache(btn.dataset.id);
      if(task) openTaskModalWithCategories(task);
    });
  });
}

/* ================= DASHBOARD ================= */
if(document.getElementById('todoTodayList')){
  async function loadDashboard(){
    // To-Do du jour
    const wrap = document.getElementById('todoTodayList');
    try{
      const res = await fetch('api/tasks.php?scope=today');
      const tasks = await res.json();
      tasks.forEach(t => editingTaskCache[t.id] = t);
      wrap.innerHTML = tasks.length
        ? tasks.map(t => renderTaskCard(t)).join('')
        : '<p class="empty">Aucune tâche pour aujourd\'hui.</p>';
      attachTaskCardEvents(wrap, loadDashboard);
      wireEditButtons(wrap);
    }catch(e){ wrap.innerHTML = '<p class="empty">Impossible de charger les tâches.</p>'; }

    // Donuts
    const donutsWrap = document.getElementById('statusDonuts');
    try{
      const res = await fetch('api/tasks.php?stats=1');
      const s = await res.json();
      const total = s.total || 0;
      const pct = n => total ? Math.round((n / total) * 100) : 0;
      const items = [
        { label: 'Completed', n: s.termine, pct: pct(s.termine), color: 'var(--green)' },
        { label: 'In Progress', n: s.en_cours, pct: pct(s.en_cours), color: 'var(--blue)' },
        { label: 'Not Started', n: s.non_commence, pct: pct(s.non_commence), color: 'var(--red)' },
      ];
      donutsWrap.innerHTML = items.map(it => `
        <div class="donut-item">
          <div class="donut" style="--pct:${it.pct}; --donut-color:${it.color};">
            <div class="donut-inner">${it.pct}%</div>
          </div>
          <div class="donut-label"><span class="swatch" style="background:${it.color}"></span>${it.label}</div>
        </div>
      `).join('');
    }catch(e){ donutsWrap.innerHTML = '<p class="empty">—</p>'; }

    // Completed
    const compWrap = document.getElementById('completedList');
    try{
      const res = await fetch('api/tasks.php?scope=completed&limit=4');
      const tasks = await res.json();
      tasks.forEach(t => editingTaskCache[t.id] = t);
      compWrap.innerHTML = tasks.length
        ? tasks.map(t => renderTaskCard(t, { showCompletedTime: true })).join('')
        : '<p class="empty">Rien de terminé pour l\'instant.</p>';
      attachTaskCardEvents(compWrap, loadDashboard);
      wireEditButtons(compWrap);
    }catch(e){ compWrap.innerHTML = '<p class="empty">—</p>'; }
  }

  window.refreshCurrentPage = loadDashboard;
  document.getElementById('quickAddBtn').addEventListener('click', () => openTaskModalWithCategories());
  loadDashboard();
}

/* ================= VITAL TASK ================= */
if(document.getElementById('vitalList')){
  async function loadVital(){
    const wrap = document.getElementById('vitalList');
    try{
      const res = await fetch('api/tasks.php?scope=vital');
      const tasks = await res.json();
      tasks.forEach(t => editingTaskCache[t.id] = t);
      wrap.innerHTML = tasks.length
        ? tasks.map(t => renderTaskCard(t)).join('')
        : '<p class="empty">Aucune tâche prioritaire pour le moment.</p>';
      attachTaskCardEvents(wrap, loadVital);
      wireEditButtons(wrap);
    }catch(e){ wrap.innerHTML = '<p class="empty">Impossible de charger les tâches.</p>'; }
  }
  window.refreshCurrentPage = loadVital;
  document.getElementById('quickAddBtnVital').addEventListener('click', () => openTaskModalWithCategories(null, { priority: 'haute' }));
  loadVital();
}

/* ================= MY TASK ================= */
if(document.getElementById('myTaskList')){
  let currentStatus = '';
  let currentCategory = '';
  const urlParams = new URLSearchParams(window.location.search);
  const currentSearch = urlParams.get('q') || '';

  async function loadMyTasks(){
    const wrap = document.getElementById('myTaskList');
    const params = new URLSearchParams();
    if(currentStatus) params.set('status', currentStatus);
    if(currentCategory) params.set('category_id', currentCategory);
    if(currentSearch) params.set('q', currentSearch);

    try{
      const res = await fetch(`api/tasks.php?${params.toString()}`);
      const tasks = await res.json();
      tasks.forEach(t => editingTaskCache[t.id] = t);
      wrap.innerHTML = tasks.length
        ? tasks.map(t => renderTaskCard(t, { showCompletedTime: true })).join('')
        : '<p class="empty">Aucune tâche ne correspond.</p>';
      attachTaskCardEvents(wrap, loadMyTasks);
      wireEditButtons(wrap);
    }catch(e){ wrap.innerHTML = '<p class="empty">Impossible de charger les tâches.</p>'; }
  }

  window.refreshCurrentPage = loadMyTasks;

  document.querySelectorAll('.status-filters .chip').forEach(chip=>{
    chip.addEventListener('click', ()=>{
      document.querySelectorAll('.status-filters .chip').forEach(c => c.classList.remove('active'));
      chip.classList.add('active');
      currentStatus = chip.dataset.status;
      loadMyTasks();
    });
  });

  (async ()=>{
    try{
      const res = await fetch('api/categories.php');
      const cats = await res.json();
      const sel = document.getElementById('categoryFilter');
      sel.innerHTML = '<option value="">Toutes les catégories</option>' +
        cats.map(c => `<option value="${c.id}">${escapeHtml(c.name)}</option>`).join('');
    }catch(e){ /* ignore */ }
  })();
  document.getElementById('categoryFilter').addEventListener('change', (e)=>{
    currentCategory = e.target.value;
    loadMyTasks();
  });

  document.getElementById('quickAddBtnMyTask').addEventListener('click', () => openTaskModalWithCategories());
  loadMyTasks();
}

/* ================= TASK CATEGORIES ================= */
if(document.getElementById('categoriesList')){
  async function loadCategories(){
    const wrap = document.getElementById('categoriesList');
    try{
      const res = await fetch('api/categories.php');
      const cats = await res.json();
      wrap.innerHTML = cats.length
        ? cats.map(c => `
          <div class="category-card">
            <div class="cat-info">
              <span class="cat-dot" style="background:${escapeHtml(c.color)}"></span>
              <div>
                <div class="cat-name">${escapeHtml(c.name)}</div>
                <div class="cat-count">${c.task_count} tâche${c.task_count !== 1 ? 's' : ''}</div>
              </div>
            </div>
            <button class="cat-remove" data-id="${c.id}" aria-label="Supprimer">✕</button>
          </div>
        `).join('')
        : '<p class="empty">Aucune catégorie pour le moment.</p>';

      wrap.querySelectorAll('.cat-remove').forEach(btn=>{
        btn.addEventListener('click', async ()=>{
          if(!confirm('Supprimer cette catégorie ? Les tâches associées seront décatégorisées.')) return;
          await fetch(`api/categories.php?id=${btn.dataset.id}`, { method:'DELETE' });
          loadCategories();
        });
      });
    }catch(e){ wrap.innerHTML = '<p class="empty">Impossible de charger les catégories.</p>'; }
  }

  document.getElementById('categoryForm').addEventListener('submit', async (e)=>{
    e.preventDefault();
    const name = document.getElementById('categoryName').value.trim();
    const color = document.getElementById('categoryColor').value;
    if(!name) return;
    await fetch('api/categories.php', {
      method:'POST', headers:{'Content-Type':'application/json'},
      body: JSON.stringify({ name, color })
    });
    document.getElementById('categoryName').value = '';
    loadCategories();
  });

  loadCategories();
}
