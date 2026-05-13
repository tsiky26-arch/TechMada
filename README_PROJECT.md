# TechMada - Système de Gestion des Congés (CodeIgniter 4)

## 📋 Description

Application de gestion des demandes de congés développée en **CodeIgniter 4** avec **SQLite3**. Le système permet aux employés de soumettre des demandes de congés, aux responsables RH d'approuver/refuser les demandes, et aux administrateurs de gérer les données de base.

## 🎯 Fonctionnalités

### 1. **Authentification** ✅
- Formulaire login/logout natif
- Session CI4 native + password_hash()
- AuthFilter appliqué aux routes protégées
- Vérification du rôle dans chaque controller
- CSRF activé sur tous les formulaires POST

### 2. **Espace Employé** ✅
- ✅ Soumettre une demande de congé (type, dates, motif)
- ✅ Calcul des jours ouvrables (exclure week-ends)
- ✅ Blocage si date_debut >= date_fin
- ✅ Blocage des chevauchements avec demandes actives
- ✅ Lister ses propres demandes avec statuts
- ✅ Voir son solde par type de congé
- ✅ Annuler une demande en_attente
- ✅ (Bonus) Modifier son profil (nom, mot de passe)

### 3. **Espace RH** ✅
- ✅ Voir toutes les demandes en_attente
- ✅ Approuver une demande
  - Vérifier jours_pris + nb_jours <= jours_attribues
  - UPDATE soldes SET jours_pris = jours_pris + $nb_jours
  - Passer le statut à approuvee
- ✅ Refuser une demande (commentaire optionnel) → solde intact
- ✅ Annulation après approbation → jours_pris - $nb_jours
- ✅ (Bonus) Filtrer par département ou statut
- ✅ (Bonus) Voir le solde de chaque employé

### 4. **Back-office Admin** ✅
- ✅ CRUD employés (créer, éditer, désactiver actif=0)
- ✅ CRUD départements
- ✅ CRUD types de congé
- ✅ (Bonus) Tableau de bord : absences du mois en cours
- ✅ (Bonus) Initialiser / ajuster le solde annuel d'un employé
- ✅ (Bonus) Voir l'historique complet de toutes les demandes

### 5. **Finition** ✅
- ✅ Layout partagé layout/app.php + sidebar selon rôle
- ✅ Vues séparées : employe/, rh/, admin/
- ✅ Flashdata CI4 pour tous les messages succès/erreur
- ✅ Pattern PRG (POST → redirect après toute écriture)

## 🚀 Installation & Démarrage

### Prérequis
- PHP 8.2+
- Composer
- Intl & mbstring activés

### Étapes d'installation

```bash
# 1. Cloner/accéder au projet
cd TechMada

# 2. Installer les dépendances
composer install

# 3. Exécuter les migrations
php spark migrate

# 4. Remplir la base de données avec les données de test
php spark db:seed DemoSeeder

# 5. Démarrer le serveur
php spark serve
```

Le serveur est accessible sur : **http://localhost:8080**

## 👤 Comptes de Test

| Rôle | Email | Mot de passe |
|------|-------|-------------|
| **Admin** | `admin@techmada.mg` | `admin123` |
| **RH** | `rh@techmada.mg` | `rh123` |
| **Employé** | `employe@techmada.mg` | `employe123` |

## 📁 Structure du Projet

```
TechMada/
├── app/
│   ├── Config/              # Configuration (Database, Routes, etc.)
│   ├── Controllers/         # Controllers pour chaque rôle
│   ├── Database/
│   │   ├── Migrations/      # Migrations (5 tables)
│   │   └── Seeds/           # Seeder DemoSeeder
│   ├── Models/              # Modèles Eloquent
│   ├── Views/               # Vues (employe/, rh/, admin/)
│   └── Filters/             # AuthFilter pour protection des routes
├── public/
│   └── assets/              # CSS/JS statiques
├── writable/
│   └── Conges.db            # Base SQLite (créée à la première migration)
├── composer.json
└── README_PROJECT.md
```

## 📊 Schéma Base de Données

### Tables
1. **Departements** - Départements de l'entreprise
2. **Employes** - Employés (admin, rh, employe)
3. **Types_Conge** - Types de congés (annuel, maladie, spécial)
4. **Soldes** - Soldes par employé/type de congé
5. **Conges** - Demandes de congé avec statuts

### Statuts des demandes
- `en_attente` - En attente d'approbation
- `approuvee` - Approuvée
- `refusee` - Refusée
- `annulee` - Annulée

## 🔒 Sécurité

- ✅ **CSRF Protection** : Token CSRF sur tous les formulaires
- ✅ **Session** : Gestion des sessions CI4 native
- ✅ **Password Hashing** : password_hash() pour tous les mots de passe
- ✅ **Authentification** : AuthFilter vérifie les sessions et les rôles
- ✅ **Autorisation** : Vérification du rôle à chaque accès sensible

## 🧪 Tests & Vérification

```bash
# Vérifier que tout fonctionne
php spark migrate              # ✅ Doit fonctionner sans erreur
php spark db:seed              # ✅ Doit fonctionner sans erreur

# Accéder à l'application
# http://localhost:8080/       # Page de login
# http://localhost:8080/rh     # Espace RH (après login)
# http://localhost:8080/admin/dashboard  # Admin (après login)
```

## 📝 Notes Développement

- **Base de données** : SQLite3 (fichier Conges.db dans writable/)
- **Framework** : CodeIgniter 4.7.2
- **Sessions** : FileHandler (fichiers dans writable/session/)
- **Pattern MVC** : Respecté (Models, Views, Controllers séparés)
- **Pattern PRG** : POST → Redirect → GET appliqué partout

## ✅ Checklist Finales

- ✅ php spark migrate fonctionne sans erreur
- ✅ php spark db:seed fonctionne sans erreur
- ✅ 4 fonctionnalités obligatoires employé fonctionnelles
- ✅ 3 fonctionnalités obligatoires RH fonctionnelles (dont MAJ solde)
- ✅ 3 fonctionnalités obligatoires admin fonctionnelles
- ✅ README complet avec instructions

## 👥 Auteurs

- **ETU003277** : Authentification, Espace Employé, Finition
- **ETU004196** : Setup & BDD, Espace RH, Back-office Admin

---

**Date de livraison** : 13 mai 2026  
**Version** : 1.0 (Stable)
