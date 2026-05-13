<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>TechMada RH - Tableau de bord</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:wght@400;500&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
<link href="/assets/css/employee/dashboard.css" rel="stylesheet">
</head>
<body>
<?php
  $isRh = in_array($role, ['rh', 'admin'], true);
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
      <a class="active" href="/dashboard"><i class="bi bi-grid-1x2"></i> Tableau de bord</a>
      <a href="#"><i class="bi bi-plus-circle"></i> Nouvelle demande</a>
      <a href="#"><i class="bi bi-calendar3"></i> Mes demandes</a>
      <a href="#"><i class="bi bi-person"></i> Mon profil</a>
      <?php if ($isRh): ?>
        <a href="/rh"><i class="bi bi-people"></i> Espace RH</a>
      <?php endif; ?>
    </nav>
    <form class="logout" method="post" action="/logout">
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
      <div class="card profile">
        <div class="avatar"><?= esc(strtoupper(substr($nom ?: $email, 0, 2))) ?></div>
        <div>
          <div><strong><?= esc($nom ?: 'Utilisateur') ?></strong></div>
          <div class="muted"><?= esc($email) ?> - <?= esc($role) ?></div>
        </div>
      </div>
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

        <div class="card col-6">
          <h2>Dernieres demandes</h2>
          <div class="table-wrap">
            <table>
              <thead>
                <tr><th>Type</th><th>Periode</th><th>Jours</th><th>Statut</th></tr>
              </thead>
              <tbody>
                <?php if (empty($demandes)): ?>
                  <tr><td colspan="4" class="empty">Aucune demande pour le moment.</td></tr>
                <?php endif; ?>
                <?php foreach ($demandes as $demande): ?>
                  <tr>
                    <td><?= esc($demande['type_conge']) ?></td>
                    <td><?= esc($demande['date_debut']) ?> au <?= esc($demande['date_fin']) ?></td>
                    <td><?= esc(number_format((float) $demande['nb_jours'], 1, ',', ' ')) ?></td>
                    <td><span class="status <?= esc($statusClass($demande['statut'])) ?>"><?= esc($statusLabel($demande['statut'])) ?></span></td>
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
</body>
</html>
