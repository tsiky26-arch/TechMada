<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width,initial-scale=1.0"/>
<title>TechMada RH — Gestion des congés CI4</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:wght@300;400;500&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet"/>
<style>
/* ═══════════════════════════════════════════
   TOKENS
═══════════════════════════════════════════ */
:root{
  --ink:      #1c2b1e;
  --forest:   #2d5a3d;
  --forest2:  #3d7a52;
  --leaf:     #5fa876;
  --mint:     #d4ede0;
  --cream:    #f8f6f1;
  --white:    #ffffff;
  --border:   #dde8e1;
  --muted:    #7a8f80;
  --danger:   #c0392b;
  --danger-bg:#fdf0ee;
  --danger-br:#f0b8b2;
  --warn:     #b8750a;
  --warn-bg:  #fef9ee;
  --warn-br:  #f5d98a;
  --success:  #1e6b3f;
  --success-bg:#edf7f2;
  --success-br:#8fd4aa;
  --info:     #1a4f7a;
  --info-bg:  #eaf2fb;
  --info-br:  #8fbde8;
  --sidebar-w:240px;
  --topbar-h: 62px;
}
*{box-sizing:border-box}
body{font-family:'DM Sans',sans-serif;background:var(--cream);color:var(--ink);margin:0;font-size:15px}
h1,h2,h3,.brand-name{font-family:'Playfair Display',serif}
code,pre,.mono{font-family:'DM Mono',monospace}

/* ─── MOTIF GÉOMÉTRIQUE ────────────────── */
.geo-bg{
  position:relative;
  overflow:hidden;
}
.geo-bg::before{
  content:'';
  position:absolute;inset:0;
  background-image:
    repeating-linear-gradient(0deg,transparent,transparent 39px,rgba(45,90,61,.04) 40px),
    repeating-linear-gradient(90deg,transparent,transparent 39px,rgba(45,90,61,.04) 40px);
  pointer-events:none;
  z-index:0;
}
.geo-bg>*{position:relative;z-index:1}

/* ─── AUTH ─────────────────────────────── */
.auth-page{min-height:100vh;display:flex;align-items:center;justify-content:center;padding:2rem;background:var(--ink)}
.auth-page.geo-bg::before{background-image:repeating-linear-gradient(0deg,transparent,transparent 39px,rgba(255,255,255,.03) 40px),repeating-linear-gradient(90deg,transparent,transparent 39px,rgba(255,255,255,.03) 40px)}
.auth-split{display:grid;grid-template-columns:1fr 420px;max-width:900px;width:100%;border-radius:16px;overflow:hidden;background:var(--white)}
.auth-left{background:var(--forest);padding:3rem;display:flex;flex-direction:column;justify-content:space-between}
.auth-left-brand{font-family:'Playfair Display',serif;font-size:1.6rem;color:var(--white);letter-spacing:-.5px;margin:0}
.auth-left-brand span{display:block;font-size:.85rem;font-weight:300;font-family:'DM Sans',sans-serif;color:rgba(255,255,255,.5);margin-top:4px;letter-spacing:0}
.auth-left-text{color:rgba(255,255,255,.6);font-size:.875rem;line-height:1.7}
.auth-left-text strong{color:var(--white);display:block;font-size:1.25rem;font-family:'Playfair Display',serif;margin-bottom:.5rem}
.auth-right{padding:2.5rem}
.auth-title{font-size:1.3rem;font-weight:700;margin:0 0 .25rem}
.auth-sub{font-size:.85rem;color:var(--muted);margin:0 0 1.75rem}
.f-label{font-size:.8rem;font-weight:500;color:var(--ink);margin-bottom:5px;display:block}
.f-input{width:100%;border:1.5px solid var(--border);border-radius:8px;padding:10px 12px;font-size:.875rem;font-family:'DM Sans',sans-serif;background:var(--white);color:var(--ink);transition:border-color .15s,box-shadow .15s}
.f-input:focus{border-color:var(--forest);box-shadow:0 0 0 3px rgba(45,90,61,.1);outline:none}
.f-group{margin-bottom:1rem}
.f-error{font-size:.75rem;color:var(--danger);margin-top:4px}
.btn-primary{background:var(--forest);color:var(--white);border:none;border-radius:8px;padding:11px 20px;font-weight:500;font-size:.9rem;cursor:pointer;transition:background .15s;font-family:'DM Sans',sans-serif;width:100%}
.btn-primary:hover{background:var(--forest2)}
.auth-footer{text-align:center;margin-top:1.25rem;font-size:.8rem;color:var(--muted)}
.auth-footer a{color:var(--forest);text-decoration:none;font-weight:500}
.auth-roles{display:flex;flex-direction:column;gap:8px;margin-top:1.5rem;padding-top:1.5rem;border-top:1px solid rgba(255,255,255,.1)}
.role-pill{background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.12);border-radius:8px;padding:8px 12px;display:flex;align-items:center;gap:10px}
.role-pill i{color:var(--leaf);font-size:1.1rem}
.role-pill-name{font-size:.8rem;font-weight:500;color:var(--white)}
.role-pill-cred{font-size:.72rem;color:rgba(255,255,255,.4);font-family:'DM Mono',monospace}

/* ─── LAYOUT APP ────────────────────────── */
.app-wrap{display:flex;min-height:100vh}
.sidebar{width:var(--sidebar-w);background:var(--ink);display:flex;flex-direction:column;flex-shrink:0;position:sticky;top:0;height:100vh;overflow-y:auto}
.sidebar-brand{padding:1.4rem 1.2rem 1rem;display:flex;align-items:center;gap:10px;border-bottom:1px solid rgba(255,255,255,.06)}
.sidebar-logo-icon{width:34px;height:34px;background:var(--forest);border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0}
.sidebar-logo-icon i{color:var(--white);font-size:1.1rem}
.sidebar-brand-name{font-family:'Playfair Display',serif;font-size:1rem;color:var(--white);line-height:1.2}
.sidebar-brand-name span{display:block;font-size:.65rem;font-family:'DM Sans',sans-serif;font-weight:400;color:rgba(255,255,255,.35);letter-spacing:.05em;text-transform:uppercase}
.sidebar-section{padding:.75rem 1.1rem .3rem;font-size:.62rem;font-weight:500;letter-spacing:1.4px;text-transform:uppercase;color:rgba(255,255,255,.25);margin-top:.25rem}
.sidebar-nav{list-style:none;padding:0 .75rem;margin:0}
.sidebar-nav li{margin-bottom:2px}
.sidebar-nav li a{display:flex;align-items:center;gap:9px;padding:9px 11px;border-radius:7px;color:rgba(255,255,255,.55);text-decoration:none;font-size:.85rem;font-weight:400;transition:all .15s}
.sidebar-nav li a:hover{background:rgba(255,255,255,.06);color:rgba(255,255,255,.9)}
.sidebar-nav li a.active{background:var(--forest);color:var(--white)}
.sidebar-nav li a i{font-size:1.05rem;flex-shrink:0}
.nav-badge{margin-left:auto;font-size:.65rem;padding:2px 7px;border-radius:10px;background:rgba(255,255,255,.12);color:var(--white)}
.nav-badge.alert{background:var(--danger);color:var(--white)}
.sidebar-user{padding:.85rem .75rem;border-top:1px solid rgba(255,255,255,.06);margin-top:auto}
.s-user-row{display:flex;align-items:center;gap:9px;padding:9px 11px;border-radius:7px;cursor:pointer;transition:background .15s}
.s-user-row:hover{background:rgba(255,255,255,.06)}
.avatar{width:32px;height:32px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:.7rem;font-weight:500;color:var(--white);flex-shrink:0;font-family:'DM Mono',monospace}
.av-green{background:var(--forest2)}
.av-blue{background:#1a4f7a}
.av-amber{background:#b8750a}
.user-name{font-size:.825rem;font-weight:500;color:var(--white);line-height:1.2}
.user-role{font-size:.65rem;color:rgba(255,255,255,.35);text-transform:uppercase;letter-spacing:.06em}

/* ─── MAIN ──────────────────────────────── */
.main{flex:1;min-width:0;display:flex;flex-direction:column}
.topbar{height:var(--topbar-h);background:var(--white);border-bottom:1px solid var(--border);display:flex;align-items:center;padding:0 1.75rem;gap:1rem;position:sticky;top:0;z-index:10}
.topbar-title{font-family:'Playfair Display',serif;font-size:1.05rem;font-weight:600;color:var(--ink)}
.topbar-breadcrumb{font-size:.78rem;color:var(--muted);display:flex;align-items:center;gap:5px}
.topbar-breadcrumb a{color:var(--muted);text-decoration:none}
.topbar-breadcrumb a:hover{color:var(--forest)}
.topbar-actions{margin-left:auto;display:flex;align-items:center;gap:8px}
.icon-btn{width:34px;height:34px;border:1.5px solid var(--border);background:var(--white);border-radius:7px;display:flex;align-items:center;justify-content:center;cursor:pointer;color:var(--muted);transition:all .15s;text-decoration:none}
.icon-btn:hover{border-color:var(--forest);color:var(--forest)}
.content{padding:1.75rem;flex:1}

/* ─── FLASH MESSAGES ────────────────────── */
.flash{padding:11px 14px;border-radius:8px;font-size:.85rem;font-weight:500;display:flex;align-items:center;gap:9px;margin-bottom:1.25rem;border:1px solid transparent}
.flash-success{background:var(--success-bg);color:var(--success);border-color:var(--success-br)}
.flash-error{background:var(--danger-bg);color:var(--danger);border-color:var(--danger-br)}
.flash-warn{background:var(--warn-bg);color:var(--warn);border-color:var(--warn-br)}
.flash-info{background:var(--info-bg);color:var(--info);border-color:var(--info-br)}

/* ─── METRIC CARDS ──────────────────────── */
.metrics{display:grid;grid-template-columns:repeat(auto-fit,minmax(170px,1fr));gap:1rem;margin-bottom:1.75rem}
.metric{background:var(--white);border:1px solid var(--border);border-radius:12px;padding:1.1rem 1.25rem}
.metric-top{display:flex;align-items:center;justify-content:space-between;margin-bottom:.75rem}
.metric-icon{width:36px;height:36px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:1rem}
.mi-green{background:var(--success-bg);color:var(--success)}
.mi-amber{background:var(--warn-bg);color:var(--warn)}
.mi-red{background:var(--danger-bg);color:var(--danger)}
.mi-blue{background:var(--info-bg);color:var(--info)}
.mi-forest{background:var(--mint);color:var(--forest)}
.metric-val{font-family:'DM Mono',monospace;font-size:1.75rem;font-weight:500;color:var(--ink);line-height:1}
.metric-label{font-size:.775rem;color:var(--muted);margin-top:4px}
.metric-sub{font-size:.72rem;color:var(--muted);margin-top:3px;display:flex;align-items:center;gap:3px}
.metric-sub.up{color:var(--success)}
.metric-sub.down{color:var(--danger)}

/* ─── SOLDE BAR (spécial congés) ─────────── */
.solde-card{background:var(--white);border:1px solid var(--border);border-radius:12px;padding:1.1rem 1.25rem;margin-bottom:1rem}
.solde-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:.6rem}
.solde-type{font-size:.875rem;font-weight:500;color:var(--ink)}
.solde-nums{font-family:'DM Mono',monospace;font-size:.8rem;color:var(--muted)}
.solde-nums strong{color:var(--ink)}
.solde-bar{height:6px;background:var(--mint);border-radius:3px;overflow:hidden}
.solde-fill{height:100%;background:var(--forest2);border-radius:3px;transition:width .3s}
.solde-fill.warn{background:var(--warn)}
.solde-fill.danger{background:var(--danger)}
.solde-label{font-size:.72rem;color:var(--muted);margin-top:4px}

/* ─── DATA CARD + TABLE ─────────────────── */
.data-card{background:var(--white);border:1px solid var(--border);border-radius:12px;overflow:hidden;margin-bottom:1.5rem}
.data-card-head{padding:.9rem 1.25rem;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;gap:.75rem;flex-wrap:wrap}
.data-card-head h3{font-family:'Playfair Display',serif;font-size:.95rem;margin:0;font-weight:600;color:var(--ink)}
.tbl{width:100%;border-collapse:collapse;font-size:.85rem}
.tbl thead th{padding:9px 14px;font-size:.68rem;font-weight:500;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);background:var(--cream);border-bottom:1px solid var(--border);text-align:left;white-space:nowrap}
.tbl tbody tr{border-bottom:1px solid var(--border);transition:background .1s}
.tbl tbody tr:last-child{border-bottom:none}
.tbl tbody tr:hover{background:var(--cream)}
.tbl td{padding:12px 14px;color:var(--ink);vertical-align:middle}
.td-name{font-weight:500}
.td-muted{color:var(--muted)}
.td-mono{font-family:'DM Mono',monospace;font-size:.8rem}

/* ─── BADGES STATUT ─────────────────────── */
.statut{display:inline-flex;align-items:center;gap:5px;font-size:.7rem;font-weight:500;padding:4px 9px;border-radius:12px}
.statut::before{content:'';width:5px;height:5px;border-radius:50%;display:inline-block;flex-shrink:0}
.s-attente{background:var(--warn-bg);color:var(--warn)}
.s-attente::before{background:var(--warn)}
.s-approuvee{background:var(--success-bg);color:var(--success)}
.s-approuvee::before{background:var(--success)}
.s-refusee{background:var(--danger-bg);color:var(--danger)}
.s-refusee::before{background:var(--danger)}
.s-annulee{background:#f1efe8;color:#7a8f80}
.s-annulee::before{background:#b4b2a9}

/* ─── BADGES TYPE CONGÉ ─────────────────── */
.type-badge{display:inline-block;font-size:.68rem;font-weight:500;padding:3px 8px;border-radius:4px}
.t-annuel{background:var(--mint);color:var(--forest)}
.t-maladie{background:var(--info-bg);color:var(--info)}
.t-special{background:#f0e8fb;color:#5a2d82}
.t-sans-solde{background:#f1efe8;color:#7a8f80}

/* ─── ACTION BUTTONS ────────────────────── */
.action-btns{display:flex;gap:5px;flex-wrap:wrap}
.btn-sm{font-size:.72rem;font-weight:500;padding:5px 10px;border-radius:6px;border:1px solid transparent;cursor:pointer;transition:all .15s;text-decoration:none;display:inline-flex;align-items:center;gap:4px;font-family:'DM Sans',sans-serif}
.btn-approve{background:var(--success-bg);color:var(--success);border-color:var(--success-br)}
.btn-approve:hover{background:#d5f0e3}
.btn-refuse{background:var(--danger-bg);color:var(--danger);border-color:var(--danger-br)}
.btn-refuse:hover{background:#f8dbd8}
.btn-edit{background:var(--info-bg);color:var(--info);border-color:var(--info-br)}
.btn-edit:hover{background:#d5e8f7}
.btn-del{background:var(--cream);color:var(--muted);border-color:var(--border)}
.btn-del:hover{background:var(--danger-bg);color:var(--danger);border-color:var(--danger-br)}
.btn-cancel{background:var(--cream);color:var(--muted);border-color:var(--border)}
.btn-cancel:hover{background:var(--danger-bg);color:var(--danger)}
.btn-view{background:var(--cream);color:var(--muted);border-color:var(--border)}
.btn-view:hover{background:var(--mint);color:var(--forest);border-color:var(--forest)}
.btn-secondary{background:var(--white);color:var(--muted);border:1.5px solid var(--border);border-radius:8px;padding:9px 16px;font-size:.85rem;font-weight:500;cursor:pointer;font-family:'DM Sans',sans-serif;display:inline-flex;align-items:center;gap:6px;text-decoration:none;transition:all .15s}
.btn-secondary:hover{border-color:var(--muted);color:var(--ink)}
.btn-forest{background:var(--forest);color:var(--white);border:none;border-radius:8px;padding:9px 16px;font-size:.85rem;font-weight:500;cursor:pointer;font-family:'DM Sans',sans-serif;display:inline-flex;align-items:center;gap:6px;text-decoration:none;transition:background .15s}
.btn-forest:hover{background:var(--forest2);color:var(--white)}

/* ─── FORMULAIRES ───────────────────────── */
.form-section{background:var(--white);border:1px solid var(--border);border-radius:12px;padding:1.5rem;margin-bottom:1.5rem}
.form-section h3{font-family:'Playfair Display',serif;font-size:.95rem;font-weight:600;margin:0 0 1.25rem;color:var(--ink)}
.form-grid-2{display:grid;grid-template-columns:1fr 1fr;gap:1rem}
@media(max-width:600px){.form-grid-2{grid-template-columns:1fr}}
.f-select{width:100%;border:1.5px solid var(--border);border-radius:8px;padding:10px 12px;font-size:.875rem;font-family:'DM Sans',sans-serif;background:var(--white);color:var(--ink);cursor:pointer}
.f-select:focus{border-color:var(--forest);outline:none;box-shadow:0 0 0 3px rgba(45,90,61,.1)}
.f-textarea{width:100%;border:1.5px solid var(--border);border-radius:8px;padding:10px 12px;font-size:.875rem;font-family:'DM Sans',sans-serif;background:var(--white);color:var(--ink);resize:vertical;min-height:80px}
.f-textarea:focus{border-color:var(--forest);outline:none;box-shadow:0 0 0 3px rgba(45,90,61,.1)}
.form-actions{display:flex;gap:10px;flex-wrap:wrap;margin-top:1.25rem}
.f-hint{font-size:.75rem;color:var(--muted);margin-top:4px}
.f-computed{background:var(--mint);border:1px solid #9fd4b8;border-radius:8px;padding:10px 14px;margin:1rem 0;display:flex;align-items:center;gap:10px}
.f-computed-num{font-family:'DM Mono',monospace;font-size:1.3rem;font-weight:500;color:var(--forest);flex-shrink:0}
.f-computed-label{font-size:.8rem;color:var(--forest)}

/* ─── EMPTY STATE ───────────────────────── */
.empty{padding:2.5rem 1rem;text-align:center;color:var(--muted)}
.empty i{font-size:2.5rem;display:block;margin-bottom:.75rem;opacity:.3}
.empty p{font-size:.875rem;margin:0}

/* ─── PROFIL ROW ────────────────────────── */
.profile-row{display:flex;align-items:center;gap:12px}
.profile-row .avatar{width:44px;height:44px;font-size:.8rem}
.profile-info .pname{font-weight:500;font-size:.9rem;color:var(--ink)}
.profile-info .pdept{font-size:.75rem;color:var(--muted)}

/* ─── STAT INLINE ───────────────────────── */
.inline-stats{display:flex;gap:1.5rem;flex-wrap:wrap;margin-top:.5rem}
.inline-stat{font-size:.78rem;color:var(--muted);display:flex;align-items:center;gap:5px}
.inline-stat strong{color:var(--ink);font-family:'DM Mono',monospace}

/* ─── FOOTER ────────────────────────────── */
.footer-app{padding:.75rem 1.75rem;border-top:1px solid var(--border);font-size:.75rem;color:var(--muted);background:var(--white);display:flex;align-items:center;gap:6px}
.footer-app span{color:var(--forest);font-weight:500}
</style>
</head>
<body>

<!-- ╔══════════════════════════════════════════════════════════════╗
     ║  PAGE 1 — CONNEXION  (auth/login.php)                       ║
     ╚══════════════════════════════════════════════════════════════╝ -->
<section id="page-login">
<div class="auth-page geo-bg">
<div class="auth-split">

  <!-- Panneau gauche -->
  <div class="auth-left">
    <div>
      <p class="auth-left-brand">TechMada RH<span>Gestion des congés</span></p>
      <p class="auth-left-text" style="margin-top:2rem">
        <strong>Bienvenue sur votre espace RH.</strong>
        Gérez vos demandes de congés, consultez votre solde et suivez l'état de vos demandes en temps réel.
      </p>
    </div>
    <div class="auth-roles">
      <div style="font-size:.65rem;text-transform:uppercase;letter-spacing:1px;color:rgba(255,255,255,.25);margin-bottom:4px">Comptes de démonstration</div>
      <div class="role-pill">
        <i class="bi bi-shield-check"></i>
        <div><div class="role-pill-name">Administrateur</div><div class="role-pill-cred">admin@techmada.mg · admin123</div></div>
      </div>
      <div class="role-pill">
        <i class="bi bi-person-check"></i>
        <div><div class="role-pill-name">Responsable RH</div><div class="role-pill-cred">rh@techmada.mg · rh123</div></div>
      </div>
      <div class="role-pill">
        <i class="bi bi-person"></i>
        <div><div class="role-pill-name">Employé</div><div class="role-pill-cred">employe@techmada.mg · emp123</div></div>
      </div>
    </div>
  </div>

  <!-- Panneau droit -->
  <div class="auth-right">
    <p class="auth-title">Connexion</p>
    <p class="auth-sub">Entrez vos identifiants pour accéder à votre espace.</p>

    <!-- Flashdata CI4 — erreur -->
    <div class="flash flash-error">
      <i class="bi bi-exclamation-circle-fill"></i>
      Identifiants incorrects. Veuillez réessayer.
    </div>

    <form>
      <div class="f-group">
        <label class="f-label">Adresse email</label>
        <input type="email" class="f-input" placeholder="vous@techmada.mg" value="employe@techmada.mg"/>
      </div>
      <div class="f-group">
        <label class="f-label">Mot de passe</label>
        <input type="password" class="f-input" placeholder="••••••••" value="emp123"/>
      </div>
      <button type="submit" class="btn-primary" style="margin-top:.5rem">
        Se connecter <i class="bi bi-arrow-right-short"></i>
      </button>
    </form>
  </div>

</div>
</div>
</section>


<!-- ╔══════════════════════════════════════════════════════════════╗
     ║  PAGE 2 — DASHBOARD EMPLOYÉ  (employe/dashboard.php)        ║
     ╚══════════════════════════════════════════════════════════════╝ -->
<section id="page-dashboard-employe" style="margin-top:3rem">
<div class="app-wrap">

  <!-- SIDEBAR EMPLOYÉ -->
  <aside class="sidebar">
    <div class="sidebar-brand">
      <div class="sidebar-logo-icon"><i class="bi bi-briefcase"></i></div>
      <div class="sidebar-brand-name">TechMada RH<span>Espace employé</span></div>
    </div>
    <div class="sidebar-section">Menu</div>
    <ul class="sidebar-nav">
      <li><a href="#page-dashboard-employe" class="active"><i class="bi bi-grid-1x2"></i> Tableau de bord</a></li>
      <li><a href="#page-form-conge"><i class="bi bi-plus-circle"></i> Nouvelle demande</a></li>
      <li>
        <a href="#page-mes-conges">
          <i class="bi bi-calendar3"></i> Mes demandes
          <span class="nav-badge alert">2</span>
        </a>
      </li>
      <li><a href="#page-profil-employe"><i class="bi bi-person"></i> Mon profil</a></li>
    </ul>
    <div class="sidebar-user">
      <div class="s-user-row">
        <div class="avatar av-green">SR</div>
        <div>
          <div class="user-name">Soa Rakoto</div>
          <div class="user-role">Employé · IT</div>
        </div>
        <a href="#page-login" style="margin-left:auto;color:rgba(255,255,255,.25);font-size:1.1rem" title="Déconnexion"><i class="bi bi-box-arrow-right"></i></a>
      </div>
    </div>
  </aside>

  <div class="main">
    <div class="topbar">
      <div>
        <div class="topbar-title">Tableau de bord</div>
        <div class="topbar-breadcrumb">Accueil</div>
      </div>
      <div class="topbar-actions">
        <a href="#page-form-conge" class="btn-forest" style="padding:7px 14px;font-size:.82rem">
          <i class="bi bi-plus-lg"></i> Nouvelle demande
        </a>
      </div>
    </div>

    <div class="content">

      <!-- Flash succès -->
      <div class="flash flash-success">
        <i class="bi bi-check-circle-fill"></i>
        Votre demande de congé a bien été soumise. Elle est en attente de validation.
      </div>

      <!-- Métriques -->
      <div class="metrics">
        <div class="metric">
          <div class="metric-top"><div class="metric-icon mi-amber"><i class="bi bi-hourglass-split"></i></div></div>
          <div class="metric-val">2</div>
          <div class="metric-label">En attente</div>
        </div>
        <div class="metric">
          <div class="metric-top"><div class="metric-icon mi-green"><i class="bi bi-check-circle"></i></div></div>
          <div class="metric-val">5</div>
          <div class="metric-label">Approuvées</div>
        </div>
        <div class="metric">
          <div class="metric-top"><div class="metric-icon mi-forest"><i class="bi bi-calendar-check"></i></div></div>
          <div class="metric-val">18</div>
          <div class="metric-label">Jours restants</div>
          <div class="metric-sub">sur 30 cette année</div>
        </div>
        <div class="metric">
          <div class="metric-top"><div class="metric-icon mi-red"><i class="bi bi-x-circle"></i></div></div>
          <div class="metric-val">1</div>
          <div class="metric-label">Refusée</div>
        </div>
      </div>

      <!-- Soldes de congés -->
      <div class="data-card">
        <div class="data-card-head"><h3>Mes soldes de congés — 2025</h3></div>
        <div style="padding:1rem 1.25rem;display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:1rem">
          <div class="solde-card" style="margin:0">
            <div class="solde-header">
              <span class="solde-type">Congé annuel</span>
              <span class="solde-nums"><strong>18</strong> / 30 j</span>
            </div>
            <div class="solde-bar"><div class="solde-fill" style="width:60%"></div></div>
            <div class="solde-label">18 jours restants · 12 pris</div>
          </div>
          <div class="solde-card" style="margin:0">
            <div class="solde-header">
              <span class="solde-type">Congé maladie</span>
              <span class="solde-nums"><strong>8</strong> / 10 j</span>
            </div>
            <div class="solde-bar"><div class="solde-fill" style="width:80%"></div></div>
            <div class="solde-label">8 jours restants · 2 pris</div>
          </div>
          <div class="solde-card" style="margin:0">
            <div class="solde-header">
              <span class="solde-type">Congé spécial</span>
              <span class="solde-nums"><strong>1</strong> / 5 j</span>
            </div>
            <div class="solde-bar"><div class="solde-fill warn" style="width:20%"></div></div>
            <div class="solde-label">1 jour restant · 4 pris</div>
          </div>
        </div>
      </div>

      <!-- Dernières demandes -->
      <div class="data-card">
        <div class="data-card-head">
          <h3>Mes dernières demandes</h3>
          <a href="#page-mes-conges" style="font-size:.8rem;color:var(--forest);text-decoration:none">Voir tout →</a>
        </div>
        <table class="tbl">
          <thead>
            <tr><th>Type</th><th>Du</th><th>Au</th><th>Durée</th><th>Statut</th><th>Action</th></tr>
          </thead>
          <tbody>
            <tr>
              <td><span class="type-badge t-annuel">Annuel</span></td>
              <td class="td-muted">16 juin 2025</td>
              <td class="td-muted">20 juin 2025</td>
              <td class="td-mono">5 j</td>
              <td><span class="statut s-attente">en attente</span></td>
              <td><button class="btn-sm btn-cancel"><i class="bi bi-x"></i> Annuler</button></td>
            </tr>
            <tr>
              <td><span class="type-badge t-maladie">Maladie</span></td>
              <td class="td-muted">2 juin 2025</td>
              <td class="td-muted">3 juin 2025</td>
              <td class="td-mono">2 j</td>
              <td><span class="statut s-approuvee">approuvée</span></td>
              <td><span class="td-muted" style="font-size:.75rem">—</span></td>
            </tr>
            <tr>
              <td><span class="type-badge t-annuel">Annuel</span></td>
              <td class="td-muted">12 mai 2025</td>
              <td class="td-muted">16 mai 2025</td>
              <td class="td-mono">5 j</td>
              <td><span class="statut s-approuvee">approuvée</span></td>
              <td><span class="td-muted" style="font-size:.75rem">—</span></td>
            </tr>
          </tbody>
        </table>
      </div>

    </div>
    <div class="footer-app"><i class="bi bi-c-circle"></i> 2025 <span>TechMada RH</span> — Projet CodeIgniter 4</div>
  </div>

</div>
</section>


<!-- ╔══════════════════════════════════════════════════════════════╗
     ║  PAGE 3 — FORMULAIRE DEMANDE  (employe/create.php)          ║
     ╚══════════════════════════════════════════════════════════════╝ -->
<section id="page-form-conge" style="margin-top:3rem">
<div class="app-wrap">

  <aside class="sidebar">
    <div class="sidebar-brand">
      <div class="sidebar-logo-icon"><i class="bi bi-briefcase"></i></div>
      <div class="sidebar-brand-name">TechMada RH<span>Espace employé</span></div>
    </div>
    <ul class="sidebar-nav" style="margin-top:1rem">
      <li><a href="#page-dashboard-employe"><i class="bi bi-grid-1x2"></i> Tableau de bord</a></li>
      <li><a href="#page-form-conge" class="active"><i class="bi bi-plus-circle"></i> Nouvelle demande</a></li>
      <li><a href="#page-mes-conges"><i class="bi bi-calendar3"></i> Mes demandes</a></li>
      <li><a href="#page-profil-employe"><i class="bi bi-person"></i> Mon profil</a></li>
    </ul>
    <div class="sidebar-user">
      <div class="s-user-row">
        <div class="avatar av-green">SR</div>
        <div><div class="user-name">Soa Rakoto</div><div class="user-role">Employé · IT</div></div>
      </div>
    </div>
  </aside>

  <div class="main">
    <div class="topbar">
      <div>
        <div class="topbar-title">Nouvelle demande de congé</div>
        <div class="topbar-breadcrumb">
          <a href="#page-dashboard-employe">Accueil</a>
          <i class="bi bi-chevron-right" style="font-size:.6rem"></i> Nouvelle demande
        </div>
      </div>
    </div>

    <div class="content">

      <div style="display:grid;grid-template-columns:1fr 300px;gap:1.5rem;align-items:start" class="form-layout">

        <!-- Formulaire principal -->
        <div>
          <div class="form-section">
            <h3>Détails de la demande</h3>

            <div class="f-group" style="margin-bottom:1rem">
              <label class="f-label">Type de congé <span style="color:var(--danger)">*</span></label>
              <select class="f-select">
                <option value="">-- Choisir un type --</option>
                <option value="1" selected>Congé annuel (18 j restants)</option>
                <option value="2">Congé maladie (8 j restants)</option>
                <option value="3">Congé spécial (1 j restant)</option>
                <option value="4">Sans solde</option>
              </select>
              <!-- Erreur validation CI4 -->
              <div class="f-error"><i class="bi bi-exclamation-circle"></i> Ce champ est requis.</div>
            </div>

            <div class="form-grid-2" style="margin-bottom:1rem">
              <div class="f-group">
                <label class="f-label">Date de début <span style="color:var(--danger)">*</span></label>
                <input type="date" class="f-input" value="2025-06-23"/>
              </div>
              <div class="f-group">
                <label class="f-label">Date de fin <span style="color:var(--danger)">*</span></label>
                <input type="date" class="f-input" value="2025-06-27"/>
              </div>
            </div>

            <!-- Calcul automatique côté PHP (affiché après soumission ou en JS) -->
            <div class="f-computed">
              <div class="f-computed-num">5</div>
              <div class="f-computed-label">jours calendaires calculés<br><span style="font-size:.7rem;opacity:.7">du lundi 23 au vendredi 27 juin 2025</span></div>
            </div>

            <div class="f-group" style="margin-bottom:1rem">
              <label class="f-label">Motif (optionnel)</label>
              <textarea class="f-textarea" placeholder="Précisez le motif de votre demande si nécessaire..."></textarea>
              <div class="f-hint">Le motif est visible par le responsable RH.</div>
            </div>

            <div class="form-actions">
              <button class="btn-forest" type="submit"><i class="bi bi-send"></i> Soumettre la demande</button>
              <a href="#page-dashboard-employe" class="btn-secondary"><i class="bi bi-x"></i> Annuler</a>
            </div>
          </div>
        </div>

        <!-- Panneau latéral : solde & règles -->
        <div style="display:flex;flex-direction:column;gap:1rem">
          <div class="data-card" style="margin:0">
            <div class="data-card-head"><h3><i class="bi bi-piggy-bank" style="color:var(--forest);margin-right:5px"></i>Vos soldes actuels</h3></div>
            <div style="padding:.75rem 1.1rem;display:flex;flex-direction:column;gap:.75rem">
              <div>
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:4px">
                  <span style="font-size:.8rem;color:var(--ink)">Congé annuel</span>
                  <span style="font-family:'DM Mono',monospace;font-size:.8rem;color:var(--forest);font-weight:500">18 j</span>
                </div>
                <div class="solde-bar"><div class="solde-fill" style="width:60%"></div></div>
              </div>
              <div>
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:4px">
                  <span style="font-size:.8rem;color:var(--ink)">Maladie</span>
                  <span style="font-family:'DM Mono',monospace;font-size:.8rem;color:var(--forest);font-weight:500">8 j</span>
                </div>
                <div class="solde-bar"><div class="solde-fill" style="width:80%"></div></div>
              </div>
              <div>
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:4px">
                  <span style="font-size:.8rem;color:var(--ink)">Spécial</span>
                  <span style="font-family:'DM Mono',monospace;font-size:.8rem;color:var(--warn);font-weight:500">1 j</span>
                </div>
                <div class="solde-bar"><div class="solde-fill warn" style="width:20%"></div></div>
              </div>
            </div>
          </div>
          <div class="flash flash-info" style="margin:0">
            <i class="bi bi-info-circle-fill"></i>
            <span style="font-size:.8rem">Le solde est déduit uniquement à l'approbation de votre responsable.</span>
          </div>
          <div style="background:var(--cream);border:1px solid var(--border);border-radius:8px;padding:.85rem 1rem">
            <div style="font-size:.78rem;font-weight:500;color:var(--ink);margin-bottom:.5rem"><i class="bi bi-clipboard-check" style="color:var(--forest);margin-right:5px"></i>Rappel des règles</div>
            <ul style="margin:0;padding-left:1rem;font-size:.75rem;color:var(--muted);line-height:1.7">
              <li>Préavis minimum : 48h avant la date de début</li>
              <li>Pas de chevauchement avec une demande en cours</li>
              <li>Solde insuffisant = demande refusée automatiquement</li>
            </ul>
          </div>
        </div>

      </div>
    </div>
    <div class="footer-app"><i class="bi bi-c-circle"></i> 2025 <span>TechMada RH</span></div>
  </div>

</div>
</section>


<!-- ╔══════════════════════════════════════════════════════════════╗
     ║  PAGE 4 — MES DEMANDES EMPLOYÉ  (employe/index.php)         ║
     ╚══════════════════════════════════════════════════════════════╝ -->
<section id="page-mes-conges" style="margin-top:3rem">
<div class="app-wrap">

  <aside class="sidebar">
    <div class="sidebar-brand">
      <div class="sidebar-logo-icon"><i class="bi bi-briefcase"></i></div>
      <div class="sidebar-brand-name">TechMada RH<span>Espace employé</span></div>
    </div>
    <ul class="sidebar-nav" style="margin-top:1rem">
      <li><a href="#page-dashboard-employe"><i class="bi bi-grid-1x2"></i> Tableau de bord</a></li>
      <li><a href="#page-form-conge"><i class="bi bi-plus-circle"></i> Nouvelle demande</a></li>
      <li><a href="#page-mes-conges" class="active"><i class="bi bi-calendar3"></i> Mes demandes</a></li>
      <li><a href="#page-profil-employe"><i class="bi bi-person"></i> Mon profil</a></li>
    </ul>
    <div class="sidebar-user">
      <div class="s-user-row">
        <div class="avatar av-green">SR</div>
        <div><div class="user-name">Soa Rakoto</div><div class="user-role">Employé · IT</div></div>
      </div>
    </div>
  </aside>

  <div class="main">
    <div class="topbar">
      <div>
        <div class="topbar-title">Mes demandes de congé</div>
        <div class="topbar-breadcrumb"><a href="#page-dashboard-employe">Accueil</a> <i class="bi bi-chevron-right" style="font-size:.6rem"></i> Mes demandes</div>
      </div>
      <div class="topbar-actions">
        <a href="#page-form-conge" class="btn-forest" style="padding:7px 14px;font-size:.82rem"><i class="bi bi-plus-lg"></i> Nouvelle demande</a>
      </div>
    </div>

    <div class="content">
      <div class="data-card">
        <div class="data-card-head">
          <h3>Toutes mes demandes</h3>
          <div style="display:flex;gap:6px">
            <select class="f-select" style="font-size:.8rem;padding:6px 10px;width:auto">
              <option>Tous les statuts</option>
              <option>En attente</option>
              <option>Approuvée</option>
              <option>Refusée</option>
              <option>Annulée</option>
            </select>
          </div>
        </div>
        <table class="tbl">
          <thead>
            <tr><th>Type</th><th>Début</th><th>Fin</th><th>Durée</th><th>Statut</th><th>Commentaire RH</th><th>Action</th></tr>
          </thead>
          <tbody>
            <tr>
              <td><span class="type-badge t-annuel">Annuel</span></td>
              <td class="td-muted">23 juin 2025</td>
              <td class="td-muted">27 juin 2025</td>
              <td class="td-mono">5 j</td>
              <td><span class="statut s-attente">en attente</span></td>
              <td class="td-muted" style="font-size:.78rem">—</td>
              <td><button class="btn-sm btn-cancel"><i class="bi bi-x"></i> Annuler</button></td>
            </tr>
            <tr>
              <td><span class="type-badge t-maladie">Maladie</span></td>
              <td class="td-muted">2 juin 2025</td>
              <td class="td-muted">3 juin 2025</td>
              <td class="td-mono">2 j</td>
              <td><span class="statut s-approuvee">approuvée</span></td>
              <td style="font-size:.78rem;color:var(--success)"><i class="bi bi-check-circle"></i> Validé</td>
              <td><span class="td-muted" style="font-size:.75rem">—</span></td>
            </tr>
            <tr>
              <td><span class="type-badge t-annuel">Annuel</span></td>
              <td class="td-muted">12 mai 2025</td>
              <td class="td-muted">16 mai 2025</td>
              <td class="td-mono">5 j</td>
              <td><span class="statut s-approuvee">approuvée</span></td>
              <td style="font-size:.78rem;color:var(--success)"><i class="bi bi-check-circle"></i> OK</td>
              <td><span class="td-muted" style="font-size:.75rem">—</span></td>
            </tr>
            <tr>
              <td><span class="type-badge t-special">Spécial</span></td>
              <td class="td-muted">5 avr. 2025</td>
              <td class="td-muted">5 avr. 2025</td>
              <td class="td-mono">1 j</td>
              <td><span class="statut s-refusee">refusée</span></td>
              <td style="font-size:.78rem;color:var(--danger)">Chevauchement détecté</td>
              <td><span class="td-muted" style="font-size:.75rem">—</span></td>
            </tr>
            <tr>
              <td><span class="type-badge t-sans-solde">Sans solde</span></td>
              <td class="td-muted">10 mars 2025</td>
              <td class="td-muted">12 mars 2025</td>
              <td class="td-mono">3 j</td>
              <td><span class="statut s-annulee">annulée</span></td>
              <td class="td-muted" style="font-size:.78rem">Annulé par l'employé</td>
              <td><span class="td-muted" style="font-size:.75rem">—</span></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <div class="footer-app"><i class="bi bi-c-circle"></i> 2025 <span>TechMada RH</span></div>
  </div>

</div>
</section>


<!-- ╔══════════════════════════════════════════════════════════════╗
     ║  PAGE 5 — LISTE RH (VALIDATION)  (rh/index.php)             ║
     ╚══════════════════════════════════════════════════════════════╝ -->
<section id="page-liste-rh" style="margin-top:3rem">
<div class="app-wrap">

  <aside class="sidebar">
    <div class="sidebar-brand">
      <div class="sidebar-logo-icon"><i class="bi bi-person-check"></i></div>
      <div class="sidebar-brand-name">TechMada RH<span>Espace responsable</span></div>
    </div>
    <div class="sidebar-section">Menu</div>
    <ul class="sidebar-nav">
      <li><a href="#page-dashboard-rh"><i class="bi bi-grid-1x2"></i> Tableau de bord</a></li>
      <li>
        <a href="#page-liste-rh" class="active">
          <i class="bi bi-inbox"></i> Demandes à traiter
          <span class="nav-badge alert">4</span>
        </a>
      </li>
      <li><a href="#page-liste-rh"><i class="bi bi-archive"></i> Historique</a></li>
      <li><a href="#page-liste-rh"><i class="bi bi-people"></i> Soldes employés</a></li>
    </ul>
    <div class="sidebar-user">
      <div class="s-user-row">
        <div class="avatar av-blue">MR</div>
        <div><div class="user-name">Marie Rabe</div><div class="user-role">Responsable RH</div></div>
        <a href="#page-login" style="margin-left:auto;color:rgba(255,255,255,.25);font-size:1.1rem"><i class="bi bi-box-arrow-right"></i></a>
      </div>
    </div>
  </aside>

  <div class="main">
    <div class="topbar">
      <div>
        <div class="topbar-title">Demandes à traiter</div>
        <div class="topbar-breadcrumb"><a href="#page-dashboard-rh">Accueil</a> <i class="bi bi-chevron-right" style="font-size:.6rem"></i> Demandes</div>
      </div>
      <div class="topbar-actions">
        <span style="font-size:.8rem;color:var(--muted);background:var(--warn-bg);border:1px solid var(--warn-br);border-radius:6px;padding:5px 10px;display:flex;align-items:center;gap:5px;color:var(--warn)">
          <i class="bi bi-hourglass-split"></i> 4 en attente
        </span>
      </div>
    </div>

    <div class="content">

      <!-- Flash -->
      <div class="flash flash-success">
        <i class="bi bi-check-circle-fill"></i>
        Demande de Soa Rakoto approuvée. Son solde a été mis à jour automatiquement.
      </div>

      <!-- Filtre -->
      <div style="display:flex;gap:8px;margin-bottom:1.25rem;flex-wrap:wrap">
        <button style="padding:6px 14px;border-radius:20px;font-size:.8rem;font-weight:500;border:1.5px solid var(--forest);background:var(--forest);color:var(--white);cursor:pointer">Tous (8)</button>
        <button style="padding:6px 14px;border-radius:20px;font-size:.8rem;font-weight:500;border:1.5px solid var(--border);background:var(--white);color:var(--muted);cursor:pointer">En attente (4)</button>
        <button style="padding:6px 14px;border-radius:20px;font-size:.8rem;font-weight:500;border:1.5px solid var(--border);background:var(--white);color:var(--muted);cursor:pointer">Approuvées (3)</button>
        <button style="padding:6px 14px;border-radius:20px;font-size:.8rem;font-weight:500;border:1.5px solid var(--border);background:var(--white);color:var(--muted);cursor:pointer">Refusées (1)</button>
        <select class="f-select" style="font-size:.8rem;padding:6px 10px;width:auto;margin-left:auto">
          <option>Tous les départements</option>
          <option>IT</option>
          <option>Finance</option>
          <option>Marketing</option>
        </select>
      </div>

      <div class="data-card">
        <div class="data-card-head"><h3>Toutes les demandes</h3></div>
        <table class="tbl">
          <thead>
            <tr><th>Employé</th><th>Type</th><th>Période</th><th>Durée</th><th>Solde dispo</th><th>Statut</th><th>Actions</th></tr>
          </thead>
          <tbody>
            <!-- En attente — actions disponibles -->
            <tr>
              <td>
                <div class="profile-row">
                  <div class="avatar av-green" style="width:32px;height:32px;font-size:.7rem">SR</div>
                  <div class="profile-info">
                    <div class="pname">Soa Rakoto</div>
                    <div class="pdept">IT · 23 juin → 27 juin</div>
                  </div>
                </div>
              </td>
              <td><span class="type-badge t-annuel">Annuel</span></td>
              <td class="td-muted" style="font-size:.8rem">23/06 – 27/06/2025</td>
              <td class="td-mono">5 j</td>
              <td>
                <span style="font-family:'DM Mono',monospace;font-size:.82rem;color:var(--success);font-weight:500">18 j</span>
                <span style="font-size:.72rem;color:var(--muted)"> dispo</span>
              </td>
              <td><span class="statut s-attente">en attente</span></td>
              <td>
                <div class="action-btns">
                  <button class="btn-sm btn-approve"><i class="bi bi-check-lg"></i> Approuver</button>
                  <button class="btn-sm btn-refuse"><i class="bi bi-x-lg"></i> Refuser</button>
                </div>
              </td>
            </tr>
            <tr>
              <td>
                <div class="profile-row">
                  <div class="avatar av-amber" style="width:32px;height:32px;font-size:.7rem">TF</div>
                  <div class="profile-info">
                    <div class="pname">Tsiry Fidy</div>
                    <div class="pdept">Finance</div>
                  </div>
                </div>
              </td>
              <td><span class="type-badge t-maladie">Maladie</span></td>
              <td class="td-muted" style="font-size:.8rem">18/06 – 19/06/2025</td>
              <td class="td-mono">2 j</td>
              <td>
                <span style="font-family:'DM Mono',monospace;font-size:.82rem;color:var(--warn);font-weight:500">1 j</span>
                <span style="font-size:.72rem;color:var(--danger)"> ⚠ insuffisant</span>
              </td>
              <td><span class="statut s-attente">en attente</span></td>
              <td>
                <div class="action-btns">
                  <button class="btn-sm btn-approve" disabled style="opacity:.4;cursor:not-allowed"><i class="bi bi-check-lg"></i> Approuver</button>
                  <button class="btn-sm btn-refuse"><i class="bi bi-x-lg"></i> Refuser</button>
                </div>
              </td>
            </tr>
            <tr>
              <td>
                <div class="profile-row">
                  <div class="avatar av-blue" style="width:32px;height:32px;font-size:.7rem">HA</div>
                  <div class="profile-info">
                    <div class="pname">Haja Andria</div>
                    <div class="pdept">Marketing</div>
                  </div>
                </div>
              </td>
              <td><span class="type-badge t-annuel">Annuel</span></td>
              <td class="td-muted" style="font-size:.8rem">30/06 – 04/07/2025</td>
              <td class="td-mono">5 j</td>
              <td>
                <span style="font-family:'DM Mono',monospace;font-size:.82rem;color:var(--success);font-weight:500">22 j</span>
                <span style="font-size:.72rem;color:var(--muted)"> dispo</span>
              </td>
              <td><span class="statut s-attente">en attente</span></td>
              <td>
                <div class="action-btns">
                  <button class="btn-sm btn-approve"><i class="bi bi-check-lg"></i> Approuver</button>
                  <button class="btn-sm btn-refuse"><i class="bi bi-x-lg"></i> Refuser</button>
                </div>
              </td>
            </tr>
            <!-- Déjà traitées -->
            <tr>
              <td>
                <div class="profile-row">
                  <div class="avatar av-green" style="width:32px;height:32px;font-size:.7rem">SR</div>
                  <div class="profile-info"><div class="pname">Soa Rakoto</div><div class="pdept">IT</div></div>
                </div>
              </td>
              <td><span class="type-badge t-maladie">Maladie</span></td>
              <td class="td-muted" style="font-size:.8rem">02/06 – 03/06/2025</td>
              <td class="td-mono">2 j</td>
              <td><span style="font-family:'DM Mono',monospace;font-size:.82rem;color:var(--muted)">—</span></td>
              <td><span class="statut s-approuvee">approuvée</span></td>
              <td><span class="td-muted" style="font-size:.75rem">Traité par Marie R.</span></td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Modal refus (inline, visible ici pour le template) -->
      <div style="margin-top:1.5rem">
        <div class="form-section" style="border-color:var(--danger-br);background:var(--danger-bg)">
          <h3 style="color:var(--danger)"><i class="bi bi-x-circle"></i> Confirmer le refus — Tsiry Fidy</h3>
          <div style="font-size:.875rem;color:var(--ink);margin-bottom:1rem">
            Demande de <strong>2 jours</strong> du 18 au 19 juin 2025 · Type : Maladie<br>
            <span style="font-size:.8rem;color:var(--danger)"><i class="bi bi-exclamation-triangle"></i> Solde insuffisant : 1 jour disponible, 2 demandés.</span>
          </div>
          <div class="f-group">
            <label class="f-label">Commentaire pour l'employé (optionnel)</label>
            <textarea class="f-textarea" placeholder="Ex : Solde insuffisant, veuillez contacter les RH pour un congé sans solde.">Solde insuffisant. Solde maladie restant : 1 jour.</textarea>
          </div>
          <div class="form-actions">
            <button class="btn-sm btn-refuse" style="padding:9px 16px;font-size:.875rem"><i class="bi bi-x-lg"></i> Confirmer le refus</button>
            <button class="btn-secondary"><i class="bi bi-arrow-left"></i> Annuler</button>
          </div>
        </div>
      </div>

    </div>
    <div class="footer-app"><i class="bi bi-c-circle"></i> 2025 <span>TechMada RH</span></div>
  </div>

</div>
</section>


<!-- ╔══════════════════════════════════════════════════════════════╗
     ║  PAGE 6 — DASHBOARD ADMIN  (admin/dashboard.php)            ║
     ╚══════════════════════════════════════════════════════════════╝ -->
<section id="page-dashboard-admin" style="margin-top:3rem">
<div class="app-wrap">

  <aside class="sidebar">
    <div class="sidebar-brand">
      <div class="sidebar-logo-icon" style="background:var(--ink);border:1px solid rgba(255,255,255,.15)"><i class="bi bi-shield-check" style="color:var(--leaf)"></i></div>
      <div class="sidebar-brand-name">TechMada RH
        <span>Administration</span>
      </div>
    </div>
    <div class="sidebar-section">Gestion</div>
    <ul class="sidebar-nav">
      <li><a href="#page-dashboard-admin" class="active"><i class="bi bi-speedometer2"></i> Vue d'ensemble</a></li>
      <li>
        <a href="#page-liste-rh">
          <i class="bi bi-inbox"></i> Toutes les demandes
          <span class="nav-badge alert">4</span>
        </a>
      </li>
      <li><a href="#page-admin-employes"><i class="bi bi-people"></i> Employés</a></li>
      <li><a href="#page-admin-employes"><i class="bi bi-building"></i> Départements</a></li>
      <li><a href="#page-admin-employes"><i class="bi bi-tags"></i> Types de congé</a></li>
      <li><a href="#page-admin-employes"><i class="bi bi-sliders"></i> Soldes annuels</a></li>
    </ul>
    <div class="sidebar-user">
      <div class="s-user-row">
        <div class="avatar" style="background:#5a2d82;width:32px;height:32px;font-size:.7rem">AD</div>
        <div><div class="user-name">Administrateur</div><div class="user-role">Admin système</div></div>
        <a href="#page-login" style="margin-left:auto;color:rgba(255,255,255,.25);font-size:1.1rem"><i class="bi bi-box-arrow-right"></i></a>
      </div>
    </div>
  </aside>

  <div class="main">
    <div class="topbar">
      <div>
        <div class="topbar-title">Vue d'ensemble</div>
        <div class="topbar-breadcrumb">Administration</div>
      </div>
      <div class="topbar-actions">
        <a href="#page-admin-employes" class="btn-forest" style="padding:7px 14px;font-size:.82rem"><i class="bi bi-person-plus"></i> Ajouter un employé</a>
      </div>
    </div>

    <div class="content">

      <!-- Métriques admin -->
      <div class="metrics">
        <div class="metric">
          <div class="metric-top"><div class="metric-icon mi-forest"><i class="bi bi-people"></i></div></div>
          <div class="metric-val">24</div>
          <div class="metric-label">Employés actifs</div>
          <div class="metric-sub up"><i class="bi bi-arrow-up-short"></i> +2 ce mois</div>
        </div>
        <div class="metric">
          <div class="metric-top"><div class="metric-icon mi-amber"><i class="bi bi-hourglass-split"></i></div></div>
          <div class="metric-val">4</div>
          <div class="metric-label">Demandes en attente</div>
        </div>
        <div class="metric">
          <div class="metric-top"><div class="metric-icon mi-green"><i class="bi bi-calendar-check"></i></div></div>
          <div class="metric-val">31</div>
          <div class="metric-label">Approuvées ce mois</div>
          <div class="metric-sub up"><i class="bi bi-arrow-up-short"></i> +6 vs mois dernier</div>
        </div>
        <div class="metric">
          <div class="metric-top"><div class="metric-icon mi-blue"><i class="bi bi-building"></i></div></div>
          <div class="metric-val">4</div>
          <div class="metric-label">Départements</div>
        </div>
        <div class="metric">
          <div class="metric-top"><div class="metric-icon mi-red"><i class="bi bi-person-slash"></i></div></div>
          <div class="metric-val">3</div>
          <div class="metric-label">Absents aujourd'hui</div>
        </div>
      </div>

      <div style="display:grid;grid-template-columns:1fr 320px;gap:1.5rem;align-items:start">

        <!-- Demandes récentes -->
        <div class="data-card" style="margin:0">
          <div class="data-card-head">
            <h3>Demandes récentes</h3>
            <a href="#page-liste-rh" style="font-size:.8rem;color:var(--forest);text-decoration:none">Tout voir →</a>
          </div>
          <table class="tbl">
            <thead>
              <tr><th>Employé</th><th>Type</th><th>Durée</th><th>Statut</th></tr>
            </thead>
            <tbody>
              <tr>
                <td><div style="display:flex;align-items:center;gap:7px"><div class="avatar av-green" style="width:28px;height:28px;font-size:.62rem">SR</div><span class="td-name" style="font-size:.84rem">Soa Rakoto</span></div></td>
                <td><span class="type-badge t-annuel">Annuel</span></td>
                <td class="td-mono">5 j</td>
                <td><span class="statut s-attente">en attente</span></td>
              </tr>
              <tr>
                <td><div style="display:flex;align-items:center;gap:7px"><div class="avatar av-amber" style="width:28px;height:28px;font-size:.62rem">TF</div><span class="td-name" style="font-size:.84rem">Tsiry Fidy</span></div></td>
                <td><span class="type-badge t-maladie">Maladie</span></td>
                <td class="td-mono">2 j</td>
                <td><span class="statut s-attente">en attente</span></td>
              </tr>
              <tr>
                <td><div style="display:flex;align-items:center;gap:7px"><div class="avatar av-blue" style="width:28px;height:28px;font-size:.62rem">HA</div><span class="td-name" style="font-size:.84rem">Haja Andria</span></div></td>
                <td><span class="type-badge t-annuel">Annuel</span></td>
                <td class="td-mono">5 j</td>
                <td><span class="statut s-approuvee">approuvée</span></td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Absents du jour + soldes critiques -->
        <div style="display:flex;flex-direction:column;gap:1rem">
          <div class="data-card" style="margin:0">
            <div class="data-card-head"><h3><i class="bi bi-person-slash" style="color:var(--muted);margin-right:5px"></i>Absents aujourd'hui</h3></div>
            <div style="padding:.75rem 1.1rem;display:flex;flex-direction:column;gap:.6rem">
              <div style="display:flex;align-items:center;gap:8px">
                <div class="avatar av-green" style="width:30px;height:30px;font-size:.65rem">SR</div>
                <div><div style="font-size:.83rem;font-weight:500;color:var(--ink)">Soa Rakoto</div><div style="font-size:.72rem;color:var(--muted)">Congé annuel · retour 28/06</div></div>
              </div>
              <div style="display:flex;align-items:center;gap:8px">
                <div class="avatar" style="width:30px;height:30px;font-size:.65rem;background:#993556">NR</div>
                <div><div style="font-size:.83rem;font-weight:500;color:var(--ink)">Noro Ramarao</div><div style="font-size:.72rem;color:var(--muted)">Maladie · retour 17/06</div></div>
              </div>
              <div style="display:flex;align-items:center;gap:8px">
                <div class="avatar av-amber" style="width:30px;height:30px;font-size:.65rem">KF</div>
                <div><div style="font-size:.83rem;font-weight:500;color:var(--ink)">Ketaka Feno</div><div style="font-size:.72rem;color:var(--muted)">Congé spécial · retour 16/06</div></div>
              </div>
            </div>
          </div>
          <div class="flash flash-warn" style="margin:0">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <span style="font-size:.8rem">2 employés ont un solde critique (≤ 2 jours). <a href="#" style="color:var(--warn);font-weight:500">Voir les soldes →</a></span>
          </div>
        </div>

      </div>

    </div>
    <div class="footer-app"><i class="bi bi-c-circle"></i> 2025 <span>TechMada RH</span></div>
  </div>

</div>
</section>


<!-- ╔══════════════════════════════════════════════════════════════╗
     ║  PAGE 7 — GESTION EMPLOYÉS  (admin/employes.php)            ║
     ╚══════════════════════════════════════════════════════════════╝ -->
<section id="page-admin-employes" style="margin-top:3rem">
<div class="app-wrap">

  <aside class="sidebar">
    <div class="sidebar-brand">
      <div class="sidebar-logo-icon" style="background:var(--ink);border:1px solid rgba(255,255,255,.15)"><i class="bi bi-shield-check" style="color:var(--leaf)"></i></div>
      <div class="sidebar-brand-name">TechMada RH<span>Administration</span></div>
    </div>
    <ul class="sidebar-nav" style="margin-top:1rem">
      <li><a href="#page-dashboard-admin"><i class="bi bi-speedometer2"></i> Vue d'ensemble</a></li>
      <li><a href="#page-liste-rh"><i class="bi bi-inbox"></i> Toutes les demandes</a></li>
      <li><a href="#page-admin-employes" class="active"><i class="bi bi-people"></i> Employés</a></li>
      <li><a href="#page-admin-employes"><i class="bi bi-building"></i> Départements</a></li>
      <li><a href="#page-admin-employes"><i class="bi bi-tags"></i> Types de congé</a></li>
    </ul>
    <div class="sidebar-user">
      <div class="s-user-row">
        <div class="avatar" style="background:#5a2d82;width:32px;height:32px;font-size:.7rem">AD</div>
        <div><div class="user-name">Administrateur</div><div class="user-role">Admin système</div></div>
      </div>
    </div>
  </aside>

  <div class="main">
    <div class="topbar">
      <div>
        <div class="topbar-title">Gestion des employés</div>
        <div class="topbar-breadcrumb"><a href="#page-dashboard-admin">Admin</a> <i class="bi bi-chevron-right" style="font-size:.6rem"></i> Employés</div>
      </div>
      <div class="topbar-actions">
        <a href="#" class="btn-forest" style="padding:7px 14px;font-size:.82rem"><i class="bi bi-person-plus"></i> Ajouter</a>
      </div>
    </div>

    <div class="content">

      <!-- Formulaire ajout -->
      <div class="form-section">
        <h3><i class="bi bi-person-plus" style="color:var(--forest);margin-right:6px"></i>Ajouter un employé</h3>
        <div class="form-grid-2" style="margin-bottom:1rem">
          <div class="f-group">
            <label class="f-label">Prénom</label>
            <input type="text" class="f-input" placeholder="Jean"/>
          </div>
          <div class="f-group">
            <label class="f-label">Nom</label>
            <input type="text" class="f-input" placeholder="Rakoto"/>
          </div>
          <div class="f-group">
            <label class="f-label">Email</label>
            <input type="email" class="f-input" placeholder="jean.rakoto@techmada.mg"/>
          </div>
          <div class="f-group">
            <label class="f-label">Mot de passe initial</label>
            <input type="password" class="f-input" placeholder="À communiquer à l'employé"/>
          </div>
          <div class="f-group">
            <label class="f-label">Département</label>
            <select class="f-select">
              <option>IT</option>
              <option>Finance</option>
              <option>Marketing</option>
              <option>RH</option>
            </select>
          </div>
          <div class="f-group">
            <label class="f-label">Rôle</label>
            <select class="f-select">
              <option value="employe">Employé</option>
              <option value="rh">Responsable RH</option>
              <option value="admin">Administrateur</option>
            </select>
          </div>
          <div class="f-group">
            <label class="f-label">Date d'embauche</label>
            <input type="date" class="f-input" value="2025-06-13"/>
          </div>
        </div>
        <div class="flash flash-info" style="margin-bottom:1rem">
          <i class="bi bi-info-circle-fill"></i>
          <span style="font-size:.82rem">Les soldes de congés seront initialisés automatiquement selon les types de congé configurés.</span>
        </div>
        <div class="form-actions">
          <button class="btn-forest"><i class="bi bi-plus"></i> Créer l'employé</button>
          <button class="btn-secondary">Réinitialiser</button>
        </div>
      </div>

      <!-- Liste employés -->
      <div class="data-card">
        <div class="data-card-head">
          <h3>Tous les employés</h3>
          <div style="display:flex;gap:6px">
            <input type="text" class="f-input" placeholder="Rechercher..." style="width:200px;padding:6px 10px;font-size:.8rem"/>
            <select class="f-select" style="font-size:.8rem;padding:6px 10px;width:auto">
              <option>Tous les depts</option>
              <option>IT</option>
              <option>Finance</option>
            </select>
          </div>
        </div>
        <table class="tbl">
          <thead>
            <tr><th>Employé</th><th>Département</th><th>Rôle</th><th>Embauche</th><th>Statut</th><th>Solde annuel</th><th>Actions</th></tr>
          </thead>
          <tbody>
            <tr>
              <td>
                <div class="profile-row">
                  <div class="avatar av-green" style="width:32px;height:32px;font-size:.68rem">SR</div>
                  <div class="profile-info"><div class="pname">Soa Rakoto</div><div class="pdept">soa@techmada.mg</div></div>
                </div>
              </td>
              <td class="td-muted">IT</td>
              <td><span class="type-badge" style="background:#f1efe8;color:#444441">employe</span></td>
              <td class="td-muted td-mono" style="font-size:.78rem">2022-03-01</td>
              <td><span class="statut s-approuvee" style="font-size:.68rem">actif</span></td>
              <td><span style="font-family:'DM Mono',monospace;font-size:.82rem;color:var(--forest)">18 / 30 j</span></td>
              <td>
                <div class="action-btns">
                  <button class="btn-sm btn-edit"><i class="bi bi-pencil"></i> Éditer</button>
                  <button class="btn-sm btn-del"><i class="bi bi-slash-circle"></i></button>
                </div>
              </td>
            </tr>
            <tr>
              <td>
                <div class="profile-row">
                  <div class="avatar av-blue" style="width:32px;height:32px;font-size:.68rem">MR</div>
                  <div class="profile-info"><div class="pname">Marie Rabe</div><div class="pdept">rh@techmada.mg</div></div>
                </div>
              </td>
              <td class="td-muted">RH</td>
              <td><span class="type-badge t-maladie">rh</span></td>
              <td class="td-muted td-mono" style="font-size:.78rem">2020-01-15</td>
              <td><span class="statut s-approuvee" style="font-size:.68rem">actif</span></td>
              <td><span style="font-family:'DM Mono',monospace;font-size:.82rem;color:var(--forest)">25 / 30 j</span></td>
              <td>
                <div class="action-btns">
                  <button class="btn-sm btn-edit"><i class="bi bi-pencil"></i> Éditer</button>
                  <button class="btn-sm btn-del"><i class="bi bi-slash-circle"></i></button>
                </div>
              </td>
            </tr>
            <tr style="opacity:.5">
              <td>
                <div class="profile-row">
                  <div class="avatar av-amber" style="width:32px;height:32px;font-size:.68rem">TF</div>
                  <div class="profile-info"><div class="pname">Tsiry Fidy</div><div class="pdept">tsiry@techmada.mg</div></div>
                </div>
              </td>
              <td class="td-muted">Finance</td>
              <td><span class="type-badge" style="background:#f1efe8;color:#444441">employe</span></td>
              <td class="td-muted td-mono" style="font-size:.78rem">2019-07-10</td>
              <td><span class="statut s-annulee" style="font-size:.68rem">inactif</span></td>
              <td><span style="font-family:'DM Mono',monospace;font-size:.82rem;color:var(--muted)">— / — j</span></td>
              <td>
                <div class="action-btns">
                  <button class="btn-sm btn-view"><i class="bi bi-arrow-counterclockwise"></i> Réactiver</button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

    </div>
    <div class="footer-app"><i class="bi bi-c-circle"></i> 2025 <span>TechMada RH</span></div>
  </div>

</div>
</section>


<!-- Navigation demo interne -->
<script>
document.querySelectorAll('a[href^="#"]').forEach(a=>{
  a.addEventListener('click',e=>{
    const t=document.querySelector(a.getAttribute('href'));
    if(t){e.preventDefault();t.scrollIntoView({behavior:'smooth',block:'start'})}
  });
});
</script>
</body>
</html>
