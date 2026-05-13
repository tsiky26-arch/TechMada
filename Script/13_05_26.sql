
PRAGMA foreign_keys = ON;

-- Table Departements (créée avant Employes car Employes y fait référence)
CREATE TABLE IF NOT EXISTS Departements (
    id        INTEGER PRIMARY KEY AUTOINCREMENT,
    nom       VARCHAR(50)  NOT NULL UNIQUE,
    description TEXT
);

-- Table Employes
CREATE TABLE IF NOT EXISTS Employes (
    id              INTEGER PRIMARY KEY AUTOINCREMENT,
    nom             VARCHAR(50)  NOT NULL,
    prenom          VARCHAR(50)  NOT NULL,
    email           VARCHAR(100) NOT NULL UNIQUE,
    password        VARCHAR(255) NOT NULL,
    role            VARCHAR(20)  NOT NULL,
    departement_id  INTEGER      REFERENCES Departements(id) ON DELETE SET NULL,
    date_embauche   DATE         NOT NULL,
    actif           INTEGER      NOT NULL DEFAULT 1 CHECK (actif IN (0, 1))
);

-- Table Types_Conge
CREATE TABLE IF NOT EXISTS Types_Conge (
    id           INTEGER PRIMARY KEY AUTOINCREMENT,
    libelle      VARCHAR(50) NOT NULL UNIQUE,
    jours_annuels INTEGER    NOT NULL,
    deductible   INTEGER     NOT NULL DEFAULT 1 CHECK (deductible IN (0, 1))
);

-- Table Soldes
CREATE TABLE IF NOT EXISTS Soldes (
    id              INTEGER PRIMARY KEY AUTOINCREMENT,
    employe_id      INTEGER NOT NULL REFERENCES Employes(id)     ON DELETE CASCADE,
    type_conge_id   INTEGER NOT NULL REFERENCES Types_Conge(id)  ON DELETE CASCADE,
    annee           INTEGER NOT NULL,
    jours_attribues REAL    NOT NULL DEFAULT 0,
    jours_pris      REAL    NOT NULL DEFAULT 0,
    jours_restants  REAL    GENERATED ALWAYS AS (jours_attribues - jours_pris) VIRTUAL,
    UNIQUE (employe_id, type_conge_id, annee)   -- un seul solde par employé/type/année
);

-- Table Conges
CREATE TABLE IF NOT EXISTS Conges (
    id                INTEGER PRIMARY KEY AUTOINCREMENT,
    employe_id        INTEGER      NOT NULL REFERENCES Employes(id)    ON DELETE CASCADE,
    type_conge_id     INTEGER      NOT NULL REFERENCES Types_Conge(id) ON DELETE RESTRICT,
    date_debut        DATE         NOT NULL,
    date_fin          DATE         NOT NULL,
    nb_jours          REAL         NOT NULL CHECK (nb_jours > 0),
    motif             TEXT,
    statut            VARCHAR(20)  NOT NULL DEFAULT 'en_attente'
                                   CHECK (statut IN ('en_attente', 'approuve', 'refuse', 'annule')),
    commentaire_rh    TEXT,
    created_at        DATETIME     NOT NULL DEFAULT (datetime('now')),
    traite_par        INTEGER      REFERENCES Employes(id) ON DELETE SET NULL,
    CHECK (date_fin >= date_debut)
);