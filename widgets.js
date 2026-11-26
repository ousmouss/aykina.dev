document.addEventListener('DOMContentLoaded', () => {
  loadCategoryBreakdown();
  loadUpcomingDeadlines();
  loadStreak();
});

async function loadCategoryBreakdown() {
  const el = document.getElementById('categoryBreakdown');
  if (!el) return;
  try {
    const res = await fetch('api/widget_categories.php');
    const data = await res.json();

    if (!data.length) {
      el.innerHTML = '<p class="empty">Aucune tâche catégorisée.</p>';
      return;
    }

    el.innerHTML = data.map(cat => `
      <div class="legend-row">
        <span class="cat-dot" style="background:${cat.color}"></span>
        <span class="legend-name">${escapeHtml(cat.name)}</span>
        <strong class="legend-count">${cat.total}</strong>
      </div>
    `).join('');
  } catch (e) {
    el.innerHTML = '<p class="empty">Erreur de chargement.</p>';
  }
}

async function loadUpcomingDeadlines() {
  const el = document.getElementById('upcomingDeadlines');
  if (!el) return;
  try {
    const res = await fetch('api/widget_deadlines.php');
    const data = await res.json();

    if (!data.length) {
      el.innerHTML = '<p class="empty">Aucune échéance à venir.</p>';
      return;
    }

    el.innerHTML = data.map(d => {
      const badge = d.is_late
        ? '<span class="badge badge-late">En retard</span>'
        : `<span class="badge">${formatDateShort(d.due_date)}</span>`;
      return `
        <div class="deadline-row">
          <span class="deadline-title">${escapeHtml(d.title)}</span>
          ${badge}
        </div>
      `;
    }).join('');
  } catch (e) {
    el.innerHTML = '<p class="empty">Erreur de chargement.</p>';
  }
}

async function loadStreak() {
  const el = document.getElementById('streakCount');
  if (!el) return;
  try {
    const res = await fetch('api/widget_streak.php');
    const data = await res.json();
    const n = data.streak || 0;
    el.innerHTML = `
      <p class="streak-number">${n} <span>jour${n > 1 ? 's' : ''} de suite</span></p>
    `;
  } catch (e) {
    el.innerHTML = '<p class="empty">Erreur de chargement.</p>';
  }
}

function formatDateShort(isoDate) {
  const d = new Date(isoDate);
  return d.toLocaleDateString('fr-FR', { day: '2-digit', month: 'short' });
}

function escapeHtml(str) {
  const div = document.createElement('div');
  div.textContent = str;
  return div.innerHTML;
}