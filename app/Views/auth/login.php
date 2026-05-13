<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>TechMada RH - Connexion</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:wght@300;400;500&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
<link href="<?= base_url('assets/css/auth/login.css') ?>" rel="stylesheet">
</head>
<body>
<section id="page-login">
<div class="auth-page geo-bg">
<div class="auth-split">
  <div class="auth-left">
    <div>
      <p class="auth-left-brand">TechMada RH<span>Gestion des congés</span></p>
      <p class="auth-left-text">
        <strong>Bienvenue sur votre espace RH.</strong>
        Gérez vos demandes de congés, consultez votre solde et suivez l'état de vos demandes en temps réel.
      </p>
    </div>
    <div class="auth-roles">
      <div class="role-title">Comptes de demonstration</div>
      <div class="role-pill">
        <i class="bi bi-shield-check"></i>
        <div><div class="role-pill-name">Administrateur</div><div class="role-pill-cred">admin@techmada.mg - admin123</div></div>
      </div>
      <div class="role-pill">
        <i class="bi bi-person-check"></i>
        <div><div class="role-pill-name">Responsable RH</div><div class="role-pill-cred">rh@techmada.mg - rh123</div></div>
      </div>
      <div class="role-pill">
        <i class="bi bi-person"></i>
        <div><div class="role-pill-name">Employe</div><div class="role-pill-cred">employe@techmada.mg - emp123</div></div>
      </div>
    </div>
  </div>

  <div class="auth-right">
    <p class="auth-title">Connexion</p>
    <p class="auth-sub">Entrez vos identifiants pour acceder a votre espace.</p>

    <?php if (! empty($error)): ?>
      <div class="flash flash-error">
        <i class="bi bi-exclamation-circle-fill"></i>
        <?= esc($error) ?>
      </div>
    <?php endif; ?>

    <form method="post" action="<?= site_url('login') ?>">
      <div class="f-group">
        <label class="f-label" for="email">Adresse email</label>
        <input id="email" name="email" type="email" class="f-input" placeholder="vous@techmada.mg" value="<?= esc($email ?? '') ?>" required>
      </div>
      <div class="f-group">
        <label class="f-label" for="password">Mot de passe</label>
        <input id="password" name="password" type="password" class="f-input" placeholder="Votre mot de passe" required>
      </div>
      <button type="submit" class="btn-primary" style="margin-top:.5rem">
        Se connecter <i class="bi bi-arrow-right-short"></i>
      </button>
    </form>
  </div>
</div>
</div>
</section>
</body>
</html>
