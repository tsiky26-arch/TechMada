<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>TechMada RH - Gestion Demandes</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:wght@400;500&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
<link href="<?= base_url('assets/css/employee/dashboard.css') ?>" rel="stylesheet">
</head>
<body>
<div class="app">
  <aside class="sidebar">
    <div class="brand">TechMada RH<span>Administration</span></div>
    <nav class="nav">
      <a href="<?= site_url('admin/dashboard') ?>"><i class="bi bi-speedometer2"></i> Vue d'ensemble</a>
      <a class="active" href="<?= site_url('admin/conges') ?>"><i class="bi bi-inbox"></i> Demandes de conge</a>
      <a href="<?= site_url('admin/employees') ?>"><i class="bi bi-people"></i> Employes</a>
      <a href="<?= site_url('admin/departments') ?>"><i class="bi bi-building"></i> Departements</a>
      <a href="<?= site_url('admin/leave-types') ?>"><i class="bi bi-tags"></i> Types de conge</a>
      <a href="<?= site_url('dashboard') ?>"><i class="bi bi-grid-1x2"></i> Retour tableau de bord</a>
    </nav>
    <form class="logout" method="post" action="<?= site_url('logout') ?>">
      <button class="btn" type="submit"><i class="bi bi-box-arrow-right"></i> Deconnexion</button>
    </form>
  </aside>

  <main class="main">
    <header class="topbar">
      <div>
        <div class="title">Gestion des demandes de conge</div>
        <div class="muted">Administration</div>
      </div>
    </header>
    <section class="content">
      <?php if (! empty(session()->getFlashdata('admin_success'))): ?>
        <div class="flash flash-success"><i class="bi bi-check-circle-fill"></i><?= esc((string) session()->getFlashdata('admin_success')) ?></div>
      <?php endif; ?>

      <div class="card">
        <h2>Demandes de conge</h2>
        <div class="table-wrap">
          <table>
            <thead>
              <tr>
                <th>ID</th>
                <th>Employe</th>
                <th>Type</th>
                <th>Periode</th>
                <th>Jours</th>
                <th>Statut</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($demandes)): ?>
                <tr><td colspan="7" class="empty">Aucune demande.</td></tr>
              <?php else: ?>
                <?php foreach ($demandes as $demande): ?>
                  <tr>
                    <td><?= esc((string) $demande['id']) ?></td>
                    <td><?= esc($demande['employe_nom'] ?? 'N/A') ?></td>
                    <td><?= esc($demande['type_conge'] ?? 'N/A') ?></td>
                    <td><?= esc($demande['date_debut']) ?> au <?= esc($demande['date_fin']) ?></td>
                    <td><?= esc(number_format((float) $demande['nb_jours'], 1, ',', ' ')) ?></td>
                    <td><?= esc($demande['statut']) ?></td>
                    <td>
                      <?php if ($demande['statut'] === 'en_attente'): ?>
                        <form method="post" action="<?= site_url('admin/conges/' . (string) $demande['id'] . '/statut') ?>" style="display: inline;">
                          <select name="statut" required onchange="this.form.submit()">
                            <option value="">Choisir...</option>
                            <option value="approuve">Approuver</option>
                            <option value="refuse">Refuser</option>
                          </select>
                        </form>
                      <?php else: ?>
                        <span class="muted">—</span>
                      <?php endif; ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </section>
  </main>
</div>
</body>
</html>
