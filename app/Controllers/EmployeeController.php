<?php

namespace App\Controllers;

use CodeIgniter\I18n\Time;

class EmployeeController extends BaseController
{
    private function guardDashboard()
    {
        if (! session()->get('employee_logged_in')) {
            return redirect()->to(site_url('employee/login'));
        }

        $role = (string) session()->get('employee_role');
        if ($role === 'admin') {
            return redirect()->to(site_url('admin/dashboard'));
        }

        if (! in_array($role, ['employe', 'rh'], true)) {
            return redirect()->to(site_url('employee/login'));
        }

        return null;
    }

    private function guardEmployeeActions()
    {
        if (! session()->get('employee_logged_in')) {
            return redirect()->to(site_url('employee/login'));
        }

        $role = (string) session()->get('employee_role');
        if ($role === 'admin') {
            return redirect()->to(site_url('admin/dashboard'));
        }

        if ($role === 'rh') {
            return redirect()->to(site_url('rh'));
        }

        if ($role !== 'employe') {
            return redirect()->to(site_url('employee/login'));
        }

        return null;
    }

    public function dashboard()
    {
        if ($guard = $this->guardDashboard()) {
            return $guard;
        }

        $employeeId = (int) session()->get('employee_id');
        $db = db_connect();
        $role = (string) session()->get('employee_role');

        $statsRows = $db->table('Conges')
            ->select('statut, COUNT(*) AS total')
            ->where('employe_id', $employeeId)
            ->groupBy('statut')
            ->get()
            ->getResultArray();

        $stats = [
            'en_attente' => 0,
            'approuve'   => 0,
            'refuse'     => 0,
            'annule'     => 0,
        ];
        foreach ($statsRows as $row) {
            $key = (string) ($row['statut'] ?? '');
            if ($key !== '' && array_key_exists($key, $stats)) {
                $stats[$key] = (int) $row['total'];
            }
        }

        $currentYear = (int) date('Y');
        $types = $db->table('Types_Conge')
            ->select('id, libelle')
            ->orderBy('libelle', 'ASC')
            ->get()
            ->getResultArray();

        $soldes = $db->table('Soldes s')
            ->select('t.libelle AS type_conge, s.jours_attribues, s.jours_pris, (s.jours_attribues - s.jours_pris) AS jours_restants')
            ->join('Types_Conge t', 't.id = s.type_conge_id')
            ->where('s.employe_id', $employeeId)
            ->where('s.annee', $currentYear)
            ->orderBy('t.libelle', 'ASC')
            ->get()
            ->getResultArray();

        $totalRestant = 0.0;
        foreach ($soldes as $solde) {
            $totalRestant += (float) ($solde['jours_restants'] ?? 0);
        }

        $demandes = $db->table('Conges c')
            ->select('c.id, t.libelle AS type_conge, c.date_debut, c.date_fin, c.nb_jours, c.statut')
            ->join('Types_Conge t', 't.id = c.type_conge_id')
            ->where('c.employe_id', $employeeId)
            ->orderBy('c.created_at', 'DESC')
            ->get()
            ->getResultArray();

        $statsByTypeRows = $db->table('Conges c')
            ->select('t.libelle AS type_conge, COUNT(*) AS total')
            ->join('Types_Conge t', 't.id = c.type_conge_id')
            ->where('c.employe_id', $employeeId)
            ->groupBy('t.libelle')
            ->orderBy('t.libelle', 'ASC')
            ->get()
            ->getResultArray();

        $statsByType = [];
        foreach ($statsByTypeRows as $row) {
            $statsByType[] = [
                'type_conge' => (string) $row['type_conge'],
                'total' => (int) $row['total'],
            ];
        }

        $calendarEvents = [];
        foreach ($demandes as $demande) {
            if (in_array((string) $demande['statut'], ['annule', 'refuse'], true)) {
                continue;
            }

            $calendarEvents[] = [
                'id' => (int) $demande['id'],
                'type_conge' => (string) $demande['type_conge'],
                'date_debut' => (string) $demande['date_debut'],
                'date_fin' => (string) $demande['date_fin'],
                'statut' => (string) $demande['statut'],
                'nb_jours' => (float) $demande['nb_jours'],
            ];
        }

        return view('employee/dashboard', [
            'email'       => (string) session()->get('employee_email'),
            'nom'         => (string) session()->get('employee_nom'),
            'role'        => $role,
            'stats'       => $stats,
            'types'       => $types,
            'soldes'      => $soldes,
            'demandes'    => $demandes,
            'totalRestant'=> $totalRestant,
            'statsByType' => $statsByType,
            'calendarEvents' => $calendarEvents,
        ]);
    }

    public function createConge()
    {
        if ($guard = $this->guardEmployeeActions()) {
            return $guard;
        }

        $employeeId = (int) session()->get('employee_id');
        $typeCongeId = (int) $this->request->getPost('type_conge_id');
        $dateDebut = (string) $this->request->getPost('date_debut');
        $dateFin = (string) $this->request->getPost('date_fin');
        $motif = trim((string) $this->request->getPost('motif'));

        if ($typeCongeId <= 0 || $dateDebut === '' || $dateFin === '') {
            session()->setFlashdata('employee_error', 'Tous les champs obligatoires doivent etre renseignes.');
            return redirect()->to(site_url('employee/espace'));
        }

        if ($dateDebut > $dateFin) {
            session()->setFlashdata('employee_error', 'La date de debut doit etre inferieure ou egale a la date de fin.');
            return redirect()->to(site_url('employee/espace'));
        }

        $nbJours = Time::parse($dateDebut)->difference(Time::parse($dateFin))->getDays() + 1;
        if ($nbJours <= 0) {
            session()->setFlashdata('employee_error', 'Nombre de jours invalide.');
            return redirect()->to(site_url('employee/espace'));
        }

        $db = db_connect();
        $hasOverlap = $db->table('Conges')
            ->where('employe_id', $employeeId)
            ->whereIn('statut', ['en_attente', 'approuve'])
            ->where('date_debut <=', $dateFin)
            ->where('date_fin >=', $dateDebut)
            ->countAllResults() > 0;

        if ($hasOverlap) {
            session()->setFlashdata('employee_error', 'Vous avez deja une demande active sur cette periode.');
            return redirect()->to(site_url('employee/espace'));
        }

        $annee = (int) date('Y', strtotime($dateDebut));
        $solde = $db->table('Soldes')
            ->where('employe_id', $employeeId)
            ->where('type_conge_id', $typeCongeId)
            ->where('annee', $annee)
            ->get()
            ->getRowArray();

        if (! $solde) {
            session()->setFlashdata('employee_error', 'Aucun solde initialise pour ce type de conge.');
            return redirect()->to(site_url('employee/espace'));
        }

        $restant = (float) $solde['jours_attribues'] - (float) $solde['jours_pris'];
        if ($nbJours > $restant) {
            session()->setFlashdata('employee_error', 'Solde insuffisant pour cette demande.');
            return redirect()->to(site_url('employee/espace'));
        }

        $db->table('Conges')->insert([
            'employe_id' => $employeeId,
            'type_conge_id' => $typeCongeId,
            'date_debut' => $dateDebut,
            'date_fin' => $dateFin,
            'nb_jours' => $nbJours,
            'motif' => $motif,
            'statut' => 'en_attente',
        ]);

        session()->setFlashdata('employee_success', 'Demande de conge soumise avec succes.');
        return redirect()->to(site_url('employee/espace'));
    }

    public function cancelConge(int $congeId)
    {
        if ($guard = $this->guardEmployeeActions()) {
            return $guard;
        }

        $employeeId = (int) session()->get('employee_id');
        $db = db_connect();
        $conge = $db->table('Conges')
            ->where('id', $congeId)
            ->where('employe_id', $employeeId)
            ->get()
            ->getRowArray();

        if (! $conge) {
            session()->setFlashdata('employee_error', 'Demande introuvable.');
            return redirect()->to(site_url('employee/espace'));
        }

        if ((string) $conge['statut'] !== 'en_attente') {
            session()->setFlashdata('employee_error', 'Seules les demandes en attente peuvent etre annulees.');
            return redirect()->to(site_url('employee/espace'));
        }

        $db->table('Conges')->where('id', $congeId)->update(['statut' => 'annule']);
        session()->setFlashdata('employee_success', 'Demande annulee.');

        return redirect()->to(site_url('employee/espace'));
    }
}
