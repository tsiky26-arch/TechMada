<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>TechMada RH - Espace Administrateur</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:wght@400;500&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
<link href="<?= base_url('assets/css/employee/dashboard.css') ?>" rel="stylesheet">
</head>
<body>
<div class="app">
  <aside class="sidebar">
    <div class="brand">TechMada RH<span>Administration</span></div>
    <nav class="nav">
      <a class="active" href="<?= site_url('admin/dashboard') ?>"><i class="bi bi-speedometer2"></i> Vue d'ensemble</a>
      <a href="<?= site_url('admin/conges') ?>"><i class="bi bi-inbox"></i> Demandes de conge</a>
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
        <div class="title">Vue d'ensemble</div>
        <div class="muted">Administration système</div>
      </div>
    </header>
    <section class="content">
      <?php if (! empty(session()->getFlashdata('admin_success'))): ?>
        <div class="flash flash-success"><i class="bi bi-check-circle-fill"></i><?= esc((string) session()->getFlashdata('admin_success')) ?></div>
      <?php endif; ?>
      <?php if (! empty(session()->getFlashdata('admin_error'))): ?>
        <div class="flash flash-error"><i class="bi bi-exclamation-circle-fill"></i><?= esc((string) session()->getFlashdata('admin_error')) ?></div>
      <?php endif; ?>

      <div class="card profile">
        <div class="avatar"><?= esc(strtoupper(substr($nom ?: $email, 0, 2))) ?></div>
        <div>
          <div><strong><?= esc($nom ?: 'Administrateur') ?></strong></div>
          <div class="muted"><?= esc($email) ?> - Admin</div>
        </div>
      </div>

      <div class="grid">
        <div class="card col-4">
          <h3>Employes actifs</h3>
          <div class="metric"><?= esc((string) ($stats['active_employees'] ?? 0)) ?></div>
          <div class="muted">Total employes</div>
        </div>
        <div class="card col-4">
          <h3>Demandes en attente</h3>
          <div class="metric"><?= esc((string) ($stats['pending_requests'] ?? 0)) ?></div>
          <div class="muted">En cours de traitement</div>
        </div>
        <div class="card col-4">
          <h3>Demandes approuvees</h3>
          <div class="metric"><?= esc((string) ($stats['approved_requests'] ?? 0)) ?></div>
          <div class="muted">Ce mois</div>
        </div>
      </div>

      <div class="grid">
        <div class="card col-6">
          <h2>Gestion</h2>
          <div class="actions" style="display: flex; flex-direction: column; gap: 0.75rem;">
            <a href="<?= site_url('admin/conges') ?>" class="btn"><i class="bi bi-inbox"></i> Gerer les demandes</a>
            <a href="<?= site_url('admin/employees') ?>" class="btn"><i class="bi bi-people"></i> Gerer les employes</a>
            <a href="<?= site_url('admin/departments') ?>" class="btn"><i class="bi bi-building"></i> Gerer les departements</a>
            <a href="<?= site_url('admin/leave-types') ?>" class="btn"><i class="bi bi-tags"></i> Gerer les types de conge</a>
          </div>
        </div>

        <div class="card col-6">
          <h2>Informations</h2>
          <table style="width: 100%;">
            <tr>
              <td style="color: var(--muted);">Employes actifs</td>
              <td style="text-align: right; font-weight: 500;"><?= esc((string) ($stats['active_employees'] ?? 0)) ?></td>
            </tr>
            <tr>
              <td style="color: var(--muted);">Total departements</td>
              <td style="text-align: right; font-weight: 500;"><?= esc((string) ($stats['departments'] ?? 0)) ?></td>
            </tr>
            <tr>
              <td style="color: var(--muted);">Types de conge</td>
              <td style="text-align: right; font-weight: 500;"><?= esc((string) ($stats['leave_types'] ?? 0)) ?></td>
            </tr>
            <tr>
              <td style="color: var(--muted);">Demandes ce mois</td>
              <td style="text-align: right; font-weight: 500;"><?= esc((string) ($stats['monthly_requests'] ?? 0)) ?></td>
            </tr>
          </table>
        </div>
      </div>
    </section>
  </main>
</div>
</body>
</html>
