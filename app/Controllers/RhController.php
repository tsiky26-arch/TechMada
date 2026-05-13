<?php

namespace App\Controllers;

use CodeIgniter\Database\Exceptions\DatabaseException;

class RhController extends BaseController
{
    private const APPROVED_STATUS = 'approuve';

    public function index()
    {
        if ($redirect = $this->requireRh()) {
            return $redirect;
        }

        $db = db_connect();
        $departementId = trim((string) $this->request->getGet('departement_id'));
        $statut = trim((string) $this->request->getGet('statut'));
        $statut = $statut === '' ? 'en_attente' : $statut;

        $demandesBuilder = $db->table('Conges c')
            ->select('c.*, e.nom, e.prenom, e.email, d.nom AS departement, t.libelle AS type_conge, s.jours_attribues, s.jours_pris, (s.jours_attribues - s.jours_pris) AS jours_restants')
            ->join('Employes e', 'e.id = c.employe_id')
            ->join('Departements d', 'd.id = e.departement_id', 'left')
            ->join('Types_Conge t', 't.id = c.type_conge_id')
            ->join('Soldes s', 's.employe_id = c.employe_id AND s.type_conge_id = c.type_conge_id AND s.annee = CAST(strftime("%Y", c.date_debut) AS INTEGER)', 'left')
            ->orderBy('c.created_at', 'DESC');

        if ($departementId !== '') {
            $demandesBuilder->where('e.departement_id', (int) $departementId);
        }

        if ($statut !== 'tous') {
            $demandesBuilder->where('c.statut', $statut);
        }

        $soldesBuilder = $db->table('Soldes s')
            ->select('s.*, e.nom, e.prenom, e.email, d.nom AS departement, t.libelle AS type_conge, (s.jours_attribues - s.jours_pris) AS jours_restants')
            ->join('Employes e', 'e.id = s.employe_id')
            ->join('Departements d', 'd.id = e.departement_id', 'left')
            ->join('Types_Conge t', 't.id = s.type_conge_id')
            ->where('s.annee', (int) date('Y'))
            ->orderBy('e.nom', 'ASC')
            ->orderBy('t.libelle', 'ASC');

        if ($departementId !== '') {
            $soldesBuilder->where('e.departement_id', (int) $departementId);
        }

        return view('rh/dashboard', [
            'email' => (string) session()->get('employee_email'),
            'nom' => (string) session()->get('employee_nom'),
            'role' => (string) session()->get('employee_role'),
            'demandes' => $demandesBuilder->get()->getResultArray(),
            'soldes' => $soldesBuilder->get()->getResultArray(),
            'departements' => $db->table('Departements')->orderBy('nom', 'ASC')->get()->getResultArray(),
            'filters' => [
                'departement_id' => $departementId,
                'statut' => $statut,
            ],
            'success' => session()->getFlashdata('rh_success'),
            'error' => session()->getFlashdata('rh_error'),
        ]);
    }

    public function approve(int $id)
    {
        if ($redirect = $this->requireRh()) {
            return $redirect;
        }

        $commentaire = trim((string) $this->request->getPost('commentaire_rh'));
        $db = db_connect();
        $db->transStart();

        try {
            $demande = $this->findDemande($id);

            if (! $demande || $demande['statut'] !== 'en_attente') {
                throw new DatabaseException('Cette demande ne peut pas etre approuvee.');
            }

            $annee = (int) date('Y', strtotime((string) $demande['date_debut']));
            $solde = $this->findSolde((int) $demande['employe_id'], (int) $demande['type_conge_id'], $annee);

            if (! $solde) {
                throw new DatabaseException('Aucun solde trouve pour cet employe et ce type de conge.');
            }

            $nbJours = (float) $demande['nb_jours'];
            $joursPris = (float) $solde['jours_pris'];
            $joursAttribues = (float) $solde['jours_attribues'];

            if ($joursPris + $nbJours > $joursAttribues) {
                throw new DatabaseException('Solde insuffisant : jours pris + demande depassent les jours attribues.');
            }

            $db->table('Soldes')
                ->where('id', (int) $solde['id'])
                ->update(['jours_pris' => $joursPris + $nbJours]);

            $db->table('Conges')
                ->where('id', $id)
                ->update([
                    'statut' => self::APPROVED_STATUS,
                    'commentaire_rh' => $commentaire !== '' ? $commentaire : null,
                    'traite_par' => (int) session()->get('employee_id'),
                ]);

            $db->transComplete();
            session()->setFlashdata('rh_success', 'Demande approuvee et solde mis a jour.');
        } catch (DatabaseException $exception) {
            $db->transRollback();
            session()->setFlashdata('rh_error', $exception->getMessage());
        }

        return redirect()->to('/rh?' . http_build_query($this->currentFilters()));
    }

    public function refuse(int $id)
    {
        if ($redirect = $this->requireRh()) {
            return $redirect;
        }

        $demande = $this->findDemande($id);
        if (! $demande || $demande['statut'] !== 'en_attente') {
            session()->setFlashdata('rh_error', 'Cette demande ne peut pas etre refusee.');

            return redirect()->to('/rh?' . http_build_query($this->currentFilters()));
        }

        db_connect()->table('Conges')
            ->where('id', $id)
            ->update([
                'statut' => 'refuse',
                'commentaire_rh' => trim((string) $this->request->getPost('commentaire_rh')) ?: null,
                'traite_par' => (int) session()->get('employee_id'),
            ]);

        session()->setFlashdata('rh_success', 'Demande refusee. Le solde reste intact.');

        return redirect()->to('/rh?' . http_build_query($this->currentFilters()));
    }

    public function cancel(int $id)
    {
        if ($redirect = $this->requireRh()) {
            return $redirect;
        }

        $db = db_connect();
        $db->transStart();

        try {
            $demande = $this->findDemande($id);

            if (! $demande || $demande['statut'] !== self::APPROVED_STATUS) {
                throw new DatabaseException('Seule une demande approuvee peut etre annulee ici.');
            }

            $annee = (int) date('Y', strtotime((string) $demande['date_debut']));
            $solde = $this->findSolde((int) $demande['employe_id'], (int) $demande['type_conge_id'], $annee);

            if (! $solde) {
                throw new DatabaseException('Aucun solde trouve pour annuler cette demande.');
            }

            $db->table('Soldes')
                ->where('id', (int) $solde['id'])
                ->update(['jours_pris' => max(0, (float) $solde['jours_pris'] - (float) $demande['nb_jours'])]);

            $db->table('Conges')
                ->where('id', $id)
                ->update([
                    'statut' => 'annule',
                    'commentaire_rh' => trim((string) $this->request->getPost('commentaire_rh')) ?: $demande['commentaire_rh'],
                    'traite_par' => (int) session()->get('employee_id'),
                ]);

            $db->transComplete();
            session()->setFlashdata('rh_success', 'Demande annulee et jours remis dans le solde.');
        } catch (DatabaseException $exception) {
            $db->transRollback();
            session()->setFlashdata('rh_error', $exception->getMessage());
        }

        return redirect()->to('/rh?' . http_build_query($this->currentFilters()));
    }

    private function requireRh()
    {
        if (! session()->get('employee_logged_in')) {
            return redirect()->to('/login');
        }

        if (! in_array((string) session()->get('employee_role'), ['rh', 'admin'], true)) {
            session()->setFlashdata('auth_error', 'Acces reserve au service RH.');

            return redirect()->to('/dashboard');
        }

        return null;
    }

    private function findDemande(int $id): ?array
    {
        $demande = db_connect()->table('Conges')
            ->where('id', $id)
            ->get()
            ->getRowArray();

        return $demande ?: null;
    }

    private function findSolde(int $employeId, int $typeCongeId, int $annee): ?array
    {
        $solde = db_connect()->table('Soldes')
            ->where('employe_id', $employeId)
            ->where('type_conge_id', $typeCongeId)
            ->where('annee', $annee)
            ->get()
            ->getRowArray();

        return $solde ?: null;
    }

    private function currentFilters(): array
    {
        return [
            'departement_id' => (string) ($this->request->getPost('departement_id') ?? $this->request->getGet('departement_id') ?? ''),
            'statut' => (string) ($this->request->getPost('statut') ?? $this->request->getGet('statut') ?? 'en_attente'),
        ];
    }
}
