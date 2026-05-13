<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>TechMada RH - Gestion Departements</title>
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
      <a href="<?= site_url('admin/conges') ?>"><i class="bi bi-inbox"></i> Demandes de conge</a>
      <a href="<?= site_url('admin/employees') ?>"><i class="bi bi-people"></i> Employes</a>
      <a class="active" href="<?= site_url('admin/departments') ?>"><i class="bi bi-building"></i> Departements</a>
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
        <div class="title">Gestion des departements</div>
        <div class="muted">Administration</div>
      </div>
    </header>
    <section class="content">
      <?php if (! empty(session()->getFlashdata('admin_success'))): ?>
        <div class="flash flash-success"><i class="bi bi-check-circle-fill"></i><?= esc((string) session()->getFlashdata('admin_success')) ?></div>
      <?php endif; ?>

      <div class="card">
        <h2>Ajouter un departement</h2>
        <form method="post" action="<?= site_url('admin/departments') ?>" class="filters">
          <div class="field">
            <label>Nom</label>
            <input name="nom" type="text" required>
          </div>
          <div class="field">
            <label>Description</label>
            <textarea name="description" placeholder="Description optionnelle..."></textarea>
          </div>
          <button class="btn" type="submit"><i class="bi bi-plus-circle"></i> Ajouter departement</button>
        </form>
      </div>

      <div class="card">
        <h2>Liste des departements</h2>
        <div class="table-wrap">
          <table>
            <thead>
              <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Description</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($departments)): ?>
                <tr><td colspan="3" class="empty">Aucun departement.</td></tr>
              <?php else: ?>
                <?php foreach ($departments as $dept): ?>
                  <tr>
                    <td><?= esc((string) $dept['id']) ?></td>
                    <td><?= esc($dept['nom']) ?></td>
                    <td><?= esc($dept['description'] ?? '') ?></td>
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
