<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>TechMada RH - Tableau de bord</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:wght@400;500&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
<link href="<?= base_url('assets/css/employee/dashboard.css') ?>" rel="stylesheet">
</head>
<body>
<?php
  $isRh = in_array($role, ['rh', 'admin'], true);
  $isEmploye = ($role ?? '') === 'employe';
  $statusClass = static function (string $statut): string {
      return match ($statut) {
          'approuve' => 'status-approved',
          'refuse' => 'status-refused',
          'annule' => 'status-cancelled',
          default => 'status-pending',
      };
  };
  $statusLabel = static function (string $statut): string {
      return match ($statut) {
          'approuve' => 'approuvee',
          'refuse' => 'refusee',
          'annule' => 'annulee',
          default => 'en attente',
      };
  };
?>
<div class="app">
  <aside class="sidebar">
    <div class="brand">TechMada RH<span>Espace <?= esc($role) ?></span></div>
    <nav class="nav">
      <a class="active" href="<?= site_url('dashboard') ?>"><i class="bi bi-grid-1x2"></i> Tableau de bord</a>
      <a href="<?= site_url('dashboard') ?>#calendrier"><i class="bi bi-calendar-week"></i> Calendrier</a>
      <a href="<?= site_url('dashboard') ?>#nouvelle-demande"><i class="bi bi-plus-circle"></i> Nouvelle demande</a>
      <a href="<?= site_url('dashboard') ?>#mes-demandes"><i class="bi bi-calendar3"></i> Mes demandes</a>
      <a href="<?= site_url('dashboard') ?>#mon-profil"><i class="bi bi-person"></i> Mon profil</a>
      <?php if ($isRh): ?>
        <a href="<?= site_url('rh') ?>"><i class="bi bi-people"></i> Espace RH</a>
      <?php endif; ?>
    </nav>
    <form class="logout" method="post" action="<?= site_url('logout') ?>">
      <button class="btn" type="submit"><i class="bi bi-box-arrow-right"></i> Deconnexion</button>
    </form>
  </aside>

  <main class="main">
    <header class="topbar">
      <div>
        <div class="title">Tableau de bord</div>
        <div class="muted">Accueil</div>
      </div>
    </header>
    <section class="content">
      <?php if (! empty(session()->getFlashdata('employee_success'))): ?>
        <div class="flash flash-success"><i class="bi bi-check-circle-fill"></i><?= esc((string) session()->getFlashdata('employee_success')) ?></div>
      <?php endif; ?>
      <?php if (! empty(session()->getFlashdata('employee_error'))): ?>
        <div class="flash flash-error"><i class="bi bi-exclamation-circle-fill"></i><?= esc((string) session()->getFlashdata('employee_error')) ?></div>
      <?php endif; ?>

      <div id="mon-profil" class="card profile">
        <div class="avatar"><?= esc(strtoupper(substr($nom ?: $email, 0, 2))) ?></div>
        <div>
          <div><strong><?= esc($nom ?: 'Utilisateur') ?></strong></div>
          <div class="muted"><?= esc($email) ?> - <?= esc($role) ?></div>
        </div>
      </div>

      <div id="calendrier" class="card">
        <div class="section-head">
          <div>
            <h2>Calendrier hebdomadaire</h2>
            <div class="muted" id="calendar-range">Semaine en cours</div>
          </div>
          <div class="actions">
            <button class="btn btn-light btn-sm" type="button" id="prev-week"><i class="bi bi-chevron-left"></i></button>
            <input class="week-picker" id="week-picker" type="date">
            <button class="btn btn-light btn-sm" type="button" id="today-week"><i class="bi bi-calendar-week"></i> Aujourd'hui</button>
            <button class="btn btn-light btn-sm" type="button" id="next-week"><i class="bi bi-chevron-right"></i></button>
          </div>
        </div>
        <div class="calendar-panel" id="calendar-panel">
          <strong id="calendar-panel-title">Selectionnez une date</strong>
          <span id="calendar-panel-text">Cliquez sur un jour pour preparer une demande ou sur un conge pour afficher ses details.</span>
        </div>
        <div class="weekly-calendar" id="weekly-calendar"></div>
      </div>

      <?php if ($isEmploye): ?>
        <div id="nouvelle-demande" class="card">
          <h2>Nouvelle demande</h2>
          <form class="filters" method="post" action="<?= site_url('employee/conges') ?>">
            <div class="field">
              <label for="type_conge_id">Type de conge</label>
              <select id="type_conge_id" name="type_conge_id" required>
                <option value="">Choisir...</option>
                <?php foreach (($types ?? []) as $type): ?>
                  <option value="<?= esc((string) $type['id']) ?>"><?= esc((string) $type['libelle']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="field">
              <label for="date_debut">Date debut</label>
              <input id="date_debut" name="date_debut" type="date" required>
            </div>
            <div class="field">
              <label for="date_fin">Date fin</label>
              <input id="date_fin" name="date_fin" type="date" required>
            </div>
            <div class="field">
              <label for="motif">Motif (optionnel)</label>
              <textarea id="motif" name="motif" placeholder="Ex: rendez-vous medical..."></textarea>
            </div>
            <button class="btn" type="submit"><i class="bi bi-send"></i> Soumettre</button>
          </form>
          <div class="muted" style="margin-top:.5rem">Le nombre de jours est calcule automatiquement (inclusif).</div>
        </div>
      <?php endif; ?>
      <div class="grid">
        <div class="card col-4">
          <h3>Demandes en attente</h3>
          <div class="metric"><?= esc((string) ($stats['en_attente'] ?? 0)) ?></div>
          <div class="muted">Demandes non traitees</div>
        </div>
        <div class="card col-4">
          <h3>Demandes approuvees</h3>
          <div class="metric"><?= esc((string) ($stats['approuve'] ?? 0)) ?></div>
          <div class="muted">Conges valides</div>
        </div>
        <div class="card col-4">
          <h3>Jours restants</h3>
          <div class="metric"><?= esc(number_format((float) ($totalRestant ?? 0), 1, ',', ' ')) ?></div>
          <div class="muted">Tous types de conge</div>
        </div>
      </div>

      <div class="grid">
        <div class="card col-6">
          <h2>Historique par type</h2>
          <?php
            $maxTypeTotal = 0;
            foreach (($statsByType ?? []) as $typeStat) {
                $maxTypeTotal = max($maxTypeTotal, (int) $typeStat['total']);
            }
          ?>
          <div class="stat-list">
            <?php if (empty($statsByType)): ?>
              <div class="empty">Aucune demande enregistree.</div>
            <?php endif; ?>
            <?php foreach (($statsByType ?? []) as $typeStat): ?>
              <?php $percent = $maxTypeTotal > 0 ? ((int) $typeStat['total'] / $maxTypeTotal) * 100 : 0; ?>
              <div class="stat-row">
                <div class="stat-row-label">
                  <span><?= esc((string) $typeStat['type_conge']) ?></span>
                  <strong><?= esc((string) $typeStat['total']) ?></strong>
                </div>
                <div class="progress"><span style="width: <?= esc(number_format($percent, 2, '.', '')) ?>%"></span></div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>

        <div class="card col-6">
          <h2>Mes soldes</h2>
          <div class="table-wrap">
            <table>
              <thead>
                <tr><th>Type</th><th>Attribues</th><th>Pris</th><th>Restants</th></tr>
              </thead>
              <tbody>
                <?php if (empty($soldes)): ?>
                  <tr><td colspan="4" class="empty">Aucun solde disponible.</td></tr>
                <?php endif; ?>
                <?php foreach ($soldes as $solde): ?>
                  <tr>
                    <td><?= esc($solde['type_conge']) ?></td>
                    <td><?= esc(number_format((float) $solde['jours_attribues'], 1, ',', ' ')) ?></td>
                    <td><?= esc(number_format((float) $solde['jours_pris'], 1, ',', ' ')) ?></td>
                    <td><strong><?= esc(number_format((float) $solde['jours_restants'], 1, ',', ' ')) ?></strong></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>

        <div class="card col-12">
          <h2 id="mes-demandes">Mes demandes</h2>
          <div class="table-wrap">
            <table>
              <thead>
                <tr><th>Type</th><th>Periode</th><th>Jours</th><th>Statut</th><?php if ($isEmploye): ?><th>Action</th><?php endif; ?></tr>
              </thead>
              <tbody>
                <?php if (empty($demandes)): ?>
                  <tr><td colspan="<?= $isEmploye ? 5 : 4 ?>" class="empty">Aucune demande pour le moment.</td></tr>
                <?php endif; ?>
                <?php foreach ($demandes as $demande): ?>
                  <tr>
                    <td><?= esc($demande['type_conge']) ?></td>
                    <td><?= esc($demande['date_debut']) ?> au <?= esc($demande['date_fin']) ?></td>
                    <td><?= esc(number_format((float) $demande['nb_jours'], 1, ',', ' ')) ?></td>
                    <td><span class="status <?= esc($statusClass($demande['statut'])) ?>"><?= esc($statusLabel($demande['statut'])) ?></span></td>
                    <?php if ($isEmploye): ?>
                      <td>
                        <?php if ((string) $demande['statut'] === 'en_attente'): ?>
                          <form method="post" action="<?= site_url('employee/conges/' . (string) $demande['id'] . '/cancel') ?>">
                            <button class="btn btn-light btn-sm" type="submit"><i class="bi bi-x-circle"></i> Annuler</button>
                          </form>
                        <?php else: ?>
                          <span class="muted">—</span>
                        <?php endif; ?>
                      </td>
                    <?php endif; ?>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </section>
  </main>
</div>
<script>
const calendarEvents = <?= json_encode($calendarEvents ?? [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
const calendar = document.getElementById('weekly-calendar');
const calendarRange = document.getElementById('calendar-range');
const weekPicker = document.getElementById('week-picker');
const dateDebutInput = document.getElementById('date_debut');
const dateFinInput = document.getElementById('date_fin');
const calendarPanelTitle = document.getElementById('calendar-panel-title');
const calendarPanelText = document.getElementById('calendar-panel-text');
let weekCursor = new Date();
let selectedDate = null;

function startOfWeek(date) {
  const next = new Date(date);
  const day = next.getDay() || 7;
  next.setHours(0, 0, 0, 0);
  next.setDate(next.getDate() - day + 1);
  return next;
}

function dateKey(date) {
  const year = date.getFullYear();
  const month = String(date.getMonth() + 1).padStart(2, '0');
  const day = String(date.getDate()).padStart(2, '0');
  return `${year}-${month}-${day}`;
}

function formatShort(date) {
  return new Intl.DateTimeFormat('fr-FR', { day: '2-digit', month: 'short' }).format(date);
}

function formatLong(date) {
  return new Intl.DateTimeFormat('fr-FR', { weekday: 'long', day: '2-digit', month: 'long', year: 'numeric' }).format(date);
}

function eventTouchesDay(event, day) {
  const key = dateKey(day);
  return event.date_debut <= key && event.date_fin >= key;
}

function syncWeekPicker() {
  weekPicker.value = dateKey(weekCursor);
}

function selectCalendarDay(day) {
  selectedDate = dateKey(day);
  if (dateDebutInput && dateFinInput) {
    dateDebutInput.value = selectedDate;
    dateFinInput.value = selectedDate;
  }

  calendarPanelTitle.textContent = formatLong(day);
  calendarPanelText.textContent = 'Date selectionnee pour une nouvelle demande. Vous pouvez ajuster la date de fin dans le formulaire.';
  renderCalendar();

  const formCard = document.getElementById('nouvelle-demande');
  if (formCard) {
    formCard.scrollIntoView({ behavior: 'smooth', block: 'start' });
  }
}

function showEventDetails(event) {
  calendarPanelTitle.textContent = event.type_conge;
  calendarPanelText.textContent = `${event.date_debut} au ${event.date_fin} - ${event.nb_jours} jour(s) - ${event.statut.replace('_', ' ')}`;
}

function renderCalendar() {
  const start = startOfWeek(weekCursor);
  const days = Array.from({ length: 7 }, (_, index) => {
    const day = new Date(start);
    day.setDate(start.getDate() + index);
    return day;
  });
  const end = days[6];
  calendarRange.textContent = `${formatShort(start)} - ${formatShort(end)}`;
  syncWeekPicker();
  calendar.innerHTML = '';

  days.forEach((day) => {
    const cell = document.createElement('section');
    cell.className = selectedDate === dateKey(day) ? 'calendar-day selected' : 'calendar-day';
    cell.tabIndex = 0;
    cell.addEventListener('click', () => selectCalendarDay(day));
    cell.addEventListener('keydown', (event) => {
      if (event.key === 'Enter' || event.key === ' ') {
        event.preventDefault();
        selectCalendarDay(day);
      }
    });

    const title = document.createElement('div');
    title.className = 'calendar-day-title';
    title.innerHTML = `<span>${new Intl.DateTimeFormat('fr-FR', { weekday: 'short' }).format(day)}</span><strong>${day.getDate()}</strong>`;
    cell.appendChild(title);

    const events = calendarEvents.filter((event) => eventTouchesDay(event, day));
    if (events.length === 0) {
      const empty = document.createElement('div');
      empty.className = 'calendar-empty';
      empty.textContent = 'Libre';
      cell.appendChild(empty);
    }

    events.forEach((event) => {
      const item = document.createElement('div');
      item.className = `calendar-event calendar-${event.statut}`;
      item.title = `${event.type_conge} - ${event.nb_jours} jour(s)`;
      item.tabIndex = 0;
      item.addEventListener('click', (clickEvent) => {
        clickEvent.stopPropagation();
        showEventDetails(event);
      });
      item.addEventListener('keydown', (keyEvent) => {
        if (keyEvent.key === 'Enter' || keyEvent.key === ' ') {
          keyEvent.preventDefault();
          keyEvent.stopPropagation();
          showEventDetails(event);
        }
      });
      const eventTitle = document.createElement('strong');
      eventTitle.textContent = event.type_conge;
      const eventStatus = document.createElement('span');
      eventStatus.textContent = event.statut.replace('_', ' ');
      item.appendChild(eventTitle);
      item.appendChild(eventStatus);
      cell.appendChild(item);
    });

    calendar.appendChild(cell);
  });
}

document.getElementById('prev-week').addEventListener('click', () => {
  weekCursor.setDate(weekCursor.getDate() - 7);
  renderCalendar();
});
document.getElementById('next-week').addEventListener('click', () => {
  weekCursor.setDate(weekCursor.getDate() + 7);
  renderCalendar();
});
document.getElementById('today-week').addEventListener('click', () => {
  weekCursor = new Date();
  renderCalendar();
});
weekPicker.addEventListener('change', () => {
  if (!weekPicker.value) {
    return;
  }

  weekCursor = new Date(`${weekPicker.value}T12:00:00`);
  renderCalendar();
});
renderCalendar();
</script>
</body>
</html>
