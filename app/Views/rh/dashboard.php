<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>TechMada RH - Espace RH</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:wght@400;500&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
<link href="/assets/css/employee/dashboard.css" rel="stylesheet">
</head>
<body>
<?php
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
      <a href="/dashboard"><i class="bi bi-grid-1x2"></i> Tableau de bord</a>
      <a class="active" href="/rh"><i class="bi bi-people"></i> Espace RH</a>
      <a href="/rh?statut=en_attente"><i class="bi bi-hourglass-split"></i> En attente</a>
      <a href="/rh?statut=approuve"><i class="bi bi-check2-circle"></i> Approuvees</a>
    </nav>
    <form class="logout" method="post" action="/logout">
      <button class="btn" type="submit"><i class="bi bi-box-arrow-right"></i> Deconnexion</button>
    </form>
  </aside>

  <main class="main">
    <header class="topbar">
      <div>
        <div class="title">Espace RH</div>
        <div class="muted">Demandes de conges et soldes employes</div>
      </div>
      <a class="btn btn-light" href="/dashboard"><i class="bi bi-arrow-left"></i> Retour</a>
    </header>

    <section class="content">
      <?php if (! empty($success)): ?>
        <div class="flash flash-success"><i class="bi bi-check-circle-fill"></i><?= esc($success) ?></div>
      <?php endif; ?>
      <?php if (! empty($error)): ?>
        <div class="flash flash-error"><i class="bi bi-exclamation-circle-fill"></i><?= esc($error) ?></div>
      <?php endif; ?>

      <div class="card">
        <form class="filters" method="get" action="/rh">
          <div class="field">
            <label for="departement_id">Departement</label>
            <select id="departement_id" name="departement_id">
              <option value="">Tous les departements</option>
              <?php foreach ($departements as $departement): ?>
                <option value="<?= esc((string) $departement['id']) ?>" <?= (string) $filters['departement_id'] === (string) $departement['id'] ? 'selected' : '' ?>>
                  <?= esc($departement['nom']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="field">
            <label for="statut">Statut</label>
            <select id="statut" name="statut">
              <?php foreach (['en_attente' => 'En attente', 'approuve' => 'Approuvees', 'refuse' => 'Refusees', 'annule' => 'Annulees', 'tous' => 'Tous'] as $value => $label): ?>
                <option value="<?= esc($value) ?>" <?= $filters['statut'] === $value ? 'selected' : '' ?>><?= esc($label) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <button class="btn" type="submit"><i class="bi bi-funnel"></i> Filtrer</button>
          <a class="btn btn-light" href="/rh"><i class="bi bi-arrow-counterclockwise"></i> Reinitialiser</a>
        </form>
      </div>

      <div class="card">
        <h2>Demandes <?= $filters['statut'] === 'en_attente' ? 'en attente' : 'filtrees' ?></h2>
        <div class="table-wrap">
          <table>
            <thead>
              <tr>
                <th>Employe</th>
                <th>Departement</th>
                <th>Type</th>
                <th>Periode</th>
                <th>Jours</th>
                <th>Solde</th>
                <th>Statut</th>
                <th>Actions RH</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($demandes)): ?>
                <tr><td colspan="8" class="empty">Aucune demande trouvee.</td></tr>
              <?php endif; ?>
              <?php foreach ($demandes as $demande): ?>
                <tr>
                  <td>
                    <strong><?= esc($demande['prenom'] . ' ' . $demande['nom']) ?></strong>
                    <div class="muted"><?= esc($demande['email']) ?></div>
                  </td>
                  <td><?= esc($demande['departement'] ?? 'Non renseigne') ?></td>
                  <td><?= esc($demande['type_conge']) ?></td>
                  <td><?= esc($demande['date_debut']) ?> au <?= esc($demande['date_fin']) ?></td>
                  <td><?= esc(number_format((float) $demande['nb_jours'], 1, ',', ' ')) ?></td>
                  <td>
                    <?php if ($demande['jours_attribues'] === null): ?>
                      <span class="muted">Non initialise</span>
                    <?php else: ?>
                      <?= esc(number_format((float) $demande['jours_pris'], 1, ',', ' ')) ?> /
                      <?= esc(number_format((float) $demande['jours_attribues'], 1, ',', ' ')) ?>
                      <div class="muted"><?= esc(number_format((float) $demande['jours_restants'], 1, ',', ' ')) ?> restants</div>
                    <?php endif; ?>
                  </td>
                  <td><span class="status <?= esc($statusClass($demande['statut'])) ?>"><?= esc($statusLabel($demande['statut'])) ?></span></td>
                  <td>
                    <?php if ($demande['statut'] === 'en_attente'): ?>
                      <div class="actions">
                        <form class="inline-form" method="post" action="/rh/conges/<?= esc((string) $demande['id']) ?>/approuver">
                          <input type="hidden" name="departement_id" value="<?= esc($filters['departement_id']) ?>">
                          <input type="hidden" name="statut" value="<?= esc($filters['statut']) ?>">
                          <div class="field inline-comment">
                            <textarea name="commentaire_rh" placeholder="Commentaire optionnel"></textarea>
                          </div>
                          <button class="btn btn-sm" type="submit"><i class="bi bi-check2"></i> Approuver</button>
                        </form>
                        <form class="inline-form" method="post" action="/rh/conges/<?= esc((string) $demande['id']) ?>/refuser">
                          <input type="hidden" name="departement_id" value="<?= esc($filters['departement_id']) ?>">
                          <input type="hidden" name="statut" value="<?= esc($filters['statut']) ?>">
                          <div class="field inline-comment">
                            <textarea name="commentaire_rh" placeholder="Motif optionnel"></textarea>
                          </div>
                          <button class="btn btn-danger btn-sm" type="submit"><i class="bi bi-x-lg"></i> Refuser</button>
                        </form>
                      </div>
                    <?php elseif ($demande['statut'] === 'approuve'): ?>
                      <form class="inline-form" method="post" action="/rh/conges/<?= esc((string) $demande['id']) ?>/annuler">
                        <input type="hidden" name="departement_id" value="<?= esc($filters['departement_id']) ?>">
                        <input type="hidden" name="statut" value="<?= esc($filters['statut']) ?>">
                        <div class="field inline-comment">
                          <textarea name="commentaire_rh" placeholder="Commentaire optionnel"></textarea>
                        </div>
                        <button class="btn btn-danger btn-sm" type="submit"><i class="bi bi-arrow-counterclockwise"></i> Annuler</button>
                      </form>
                    <?php else: ?>
                      <span class="muted">Aucune action</span>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>

      <div class="card">
        <h2>Soldes de chaque employe - <?= esc(date('Y')) ?></h2>
        <div class="table-wrap">
          <table>
            <thead>
              <tr><th>Employe</th><th>Departement</th><th>Type</th><th>Attribues</th><th>Pris</th><th>Restants</th></tr>
            </thead>
            <tbody>
              <?php if (empty($soldes)): ?>
                <tr><td colspan="6" class="empty">Aucun solde disponible.</td></tr>
              <?php endif; ?>
              <?php foreach ($soldes as $solde): ?>
                <tr>
                  <td>
                    <strong><?= esc($solde['prenom'] . ' ' . $solde['nom']) ?></strong>
                    <div class="muted"><?= esc($solde['email']) ?></div>
                  </td>
                  <td><?= esc($solde['departement'] ?? 'Non renseigne') ?></td>
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
    </section>
  </main>
</div>
</body>
</html>
