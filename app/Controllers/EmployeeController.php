<?php

namespace App\Controllers;

use CodeIgniter\I18n\Time;

class EmployeeController extends BaseController
{
    private function guardEmployee()
    {
        if (! session()->get('employee_logged_in')) {
            return redirect()->to(site_url('employee/login'));
        }

        if ((string) session()->get('employee_role') !== 'employe') {
            if ((string) session()->get('employee_role') === 'admin') {
                return redirect()->to(site_url('admin/dashboard'));
            }

            return redirect()->to(site_url('employee/login'));
        }

        return null;
    }

    public function dashboard()
    {
        if ($guard = $this->guardEmployee()) {
            return $guard;
        }

        $employeeId = (int) session()->get('employee_id');
        $db = db_connect();
        $types = $db->table('Types_Conge')->orderBy('libelle', 'ASC')->get()->getResultArray();
        $demandes = $db->table('Conges c')
            ->select('c.id, c.date_debut, c.date_fin, c.nb_jours, c.motif, c.statut, c.created_at, t.libelle AS type_libelle')
            ->join('Types_Conge t', 't.id = c.type_conge_id')
            ->where('c.employe_id', $employeeId)
            ->orderBy('c.created_at', 'DESC')
            ->get()
            ->getResultArray();

        $soldes = $db->table('Soldes s')
            ->select('s.annee, t.libelle, s.jours_attribues, s.jours_pris, s.jours_restants')
            ->join('Types_Conge t', 't.id = s.type_conge_id')
            ->where('s.employe_id', $employeeId)
            ->orderBy('s.annee', 'DESC')
            ->orderBy('t.libelle', 'ASC')
            ->get()
            ->getResultArray();

        $typeOptions = '';
        foreach ($types as $type) {
            $typeOptions .= '<option value="' . (int) $type['id'] . '">' . esc((string) $type['libelle']) . '</option>';
        }

        $demandesRows = '';
        foreach ($demandes as $demande) {
            $action = '';
            if ((string) $demande['statut'] === 'en_attente') {
                $action = '<form method="post" action="' . site_url('employee/conges/' . (int) $demande['id'] . '/cancel') . '">
                    <button type="submit">Annuler</button>
                </form>';
            }

            $demandesRows .= '<tr>'
                . '<td>' . (int) $demande['id'] . '</td>'
                . '<td>' . esc((string) $demande['type_libelle']) . '</td>'
                . '<td>' . esc((string) $demande['date_debut']) . ' -> ' . esc((string) $demande['date_fin']) . '</td>'
                . '<td>' . esc((string) $demande['nb_jours']) . '</td>'
                . '<td>' . esc((string) $demande['statut']) . '</td>'
                . '<td>' . esc((string) ($demande['motif'] ?? '')) . '</td>'
                . '<td>' . $action . '</td>'
                . '</tr>';
        }

        $soldesRows = '';
        foreach ($soldes as $solde) {
            $soldesRows .= '<tr>'
                . '<td>' . esc((string) $solde['annee']) . '</td>'
                . '<td>' . esc((string) $solde['libelle']) . '</td>'
                . '<td>' . esc((string) $solde['jours_attribues']) . '</td>'
                . '<td>' . esc((string) $solde['jours_pris']) . '</td>'
                . '<td>' . esc((string) $solde['jours_restants']) . '</td>'
                . '</tr>';
        }

        $error = session()->getFlashdata('employee_error');
        $success = session()->getFlashdata('employee_success');

        return '
            <h1>Espace Employe</h1>
            <p>Bienvenue, ' . esc((string) session()->get('employee_nom')) . '</p>
            ' . ($error ? '<p style="color:red;">' . esc($error) . '</p>' : '') . '
            ' . ($success ? '<p style="color:green;">' . esc($success) . '</p>' : '') . '

            <h2>Soumettre une demande de conge</h2>
            <form method="post" action="' . site_url('employee/conges') . '">
                <label>Type de conge</label><br>
                <select name="type_conge_id" required>' . $typeOptions . '</select><br>
                <label>Date debut</label><br>
                <input type="date" name="date_debut" required><br>
                <label>Date fin</label><br>
                <input type="date" name="date_fin" required><br>
                <label>Motif</label><br>
                <textarea name="motif"></textarea><br>
                <button type="submit">Soumettre</button>
            </form>

            <h2>Mes demandes</h2>
            <table border="1" cellpadding="6" cellspacing="0">
                <thead>
                    <tr><th>ID</th><th>Type</th><th>Periode</th><th>Jours</th><th>Statut</th><th>Motif</th><th>Action</th></tr>
                </thead>
                <tbody>' . $demandesRows . '</tbody>
            </table>

            <h2>Mon solde de conges</h2>
            <table border="1" cellpadding="6" cellspacing="0">
                <thead>
                    <tr><th>Annee</th><th>Type</th><th>Attribues</th><th>Pris</th><th>Restants</th></tr>
                </thead>
                <tbody>' . $soldesRows . '</tbody>
            </table>

            <form method="post" action="' . site_url('employee/logout') . '">
                <button type="submit">Se deconnecter</button>
            </form>
        ';
    }

    public function createConge()
    {
        if ($guard = $this->guardEmployee()) {
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

        if ($dateDebut >= $dateFin) {
            session()->setFlashdata('employee_error', 'La date de debut doit etre strictement inferieure a la date de fin.');
            return redirect()->to(site_url('employee/espace'));
        }

        $nbJours = Time::parse($dateDebut)->difference(Time::parse($dateFin))->getDays();
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
        if ($guard = $this->guardEmployee()) {
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
