<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>TechMada RH - Gestion Employes</title>
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
      <a class="active" href="<?= site_url('admin/employees') ?>"><i class="bi bi-people"></i> Employes</a>
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
        <div class="title">Gestion des employes</div>
        <div class="muted">Administration</div>
      </div>
    </header>
    <section class="content">
      <?php if (! empty(session()->getFlashdata('admin_success'))): ?>
        <div class="flash flash-success"><i class="bi bi-check-circle-fill"></i><?= esc((string) session()->getFlashdata('admin_success')) ?></div>
      <?php endif; ?>

      <div class="card">
        <h2>Ajouter un employe</h2>
        <form method="post" action="<?= site_url('admin/employees') ?>" class="filters">
          <div class="field">
            <label>Nom</label>
            <input name="nom" type="text" required>
          </div>
          <div class="field">
            <label>Prenom</label>
            <input name="prenom" type="text" required>
          </div>
          <div class="field">
            <label>Email</label>
            <input name="email" type="email" required>
          </div>
          <div class="field">
            <label>Mot de passe</label>
            <input name="password" type="password" required>
          </div>
          <div class="field">
            <label>Role</label>
            <select name="role" required>
              <option value="employe">Employe</option>
              <option value="rh">RH</option>
              <option value="admin">Admin</option>
            </select>
          </div>
          <div class="field">
            <label>Departement</label>
            <select name="departement_id" required>
              <option value="">Choisir departement...</option>
              <?php foreach (($departments ?? []) as $dept): ?>
                <option value="<?= esc((string) $dept['id']) ?>"><?= esc($dept['nom']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="field">
            <label>Date embauche</label>
            <input name="date_embauche" type="date" required>
          </div>
          <button class="btn" type="submit"><i class="bi bi-plus-circle"></i> Creer employe</button>
        </form>
      </div>

      <div class="card">
        <h2>Liste des employes</h2>
        <div class="table-wrap">
          <table>
            <thead>
              <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Email</th>
                <th>Role</th>
                <th>Actif</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($employees)): ?>
                <tr><td colspan="6" class="empty">Aucun employe.</td></tr>
              <?php else: ?>
                <?php foreach ($employees as $employee): ?>
                  <tr>
                    <td><?= esc((string) $employee['id']) ?></td>
                    <td><?= esc($employee['prenom'] . ' ' . $employee['nom']) ?></td>
                    <td><?= esc($employee['email']) ?></td>
                    <td><?= esc($employee['role']) ?></td>
                    <td><?= ((int) $employee['actif'] === 1) ? 'Oui' : 'Non' ?></td>
                    <td>
                      <?php if ((int) $employee['actif'] === 1): ?>
                        <form method="post" action="<?= site_url('admin/employees/' . (string) $employee['id'] . '/deactivate') ?>" style="display: inline;">
                          <button class="btn btn-sm" type="submit"><i class="bi bi-x-circle"></i> Desactiver</button>
                        </form>
                      <?php else: ?>
                        <form method="post" action="<?= site_url('admin/employees/' . (string) $employee['id'] . '/activate') ?>" style="display: inline;">
                          <button class="btn btn-sm" type="submit"><i class="bi bi-check-circle"></i> Activer</button>
                        </form>
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
