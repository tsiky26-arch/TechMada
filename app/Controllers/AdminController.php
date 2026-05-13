<?php

namespace App\Controllers;

use App\Models\EmployeModel;
use CodeIgniter\Database\BaseConnection;

class AdminController extends BaseController
{
    private function guardAdmin()
    {
        if (! session()->get('employee_logged_in')) {
            return redirect()->to(site_url('employee/login'));
        }

        if ((string) session()->get('employee_role') !== 'admin') {
            return redirect()->to(site_url('employee/dashboard'));
        }

        return null;
    }

    private function db(): BaseConnection
    {
        return db_connect();
    }

    public function dashboard()
    {
        if ($guard = $this->guardAdmin()) {
            return $guard;
        }

        $db = $this->db();
        $pendingCount = (int) $db->table('Conges')->where('statut', 'en_attente')->countAllResults();
        $employeeCount = (int) $db->table('Employes')->countAllResults();
        $activeCount = (int) $db->table('Employes')->where('actif', 1)->countAllResults();
        $monthlyAbsences = (int) $db->table('Conges')
            ->where('statut', 'approuve')
            ->where('strftime("%Y-%m", date_debut) =', date('Y-m'))
            ->countAllResults();
        $message = session()->getFlashdata('admin_success');

        return '
            <h1>Espace Administrateur</h1>
            ' . ($message ? '<p style="color:green;">' . esc($message) . '</p>' : '') . '
            <p>Bienvenue, ' . esc((string) session()->get('employee_nom')) . '</p>
            <p>Total employes: ' . $employeeCount . '</p>
            <p>Employes actifs: ' . $activeCount . '</p>
            <p>Demandes en attente: ' . $pendingCount . '</p>
            <p>Absences approuvees du mois: ' . $monthlyAbsences . '</p>
            <p><a href="' . site_url('admin/employees') . '">Voir les employes</a></p>
            <p><a href="' . site_url('admin/departments') . '">Gerer les departements</a></p>
            <p><a href="' . site_url('admin/leave-types') . '">Gerer les types de conge</a></p>
            <p><a href="' . site_url('admin/conges') . '">Gerer les demandes de conge</a></p>
            <form method="post" action="' . site_url('employee/logout') . '">
                <button type="submit">Se deconnecter</button>
            </form>
        ';
    }

    public function employees()
    {
        if ($guard = $this->guardAdmin()) {
            return $guard;
        }

        $employees = (new EmployeModel())->orderBy('id', 'DESC')->findAll();
        $rows = '';
        foreach ($employees as $employee) {
            $employeeId = (int) $employee['id'];
            $isActive = (int) $employee['actif'] === 1;
            $actionForm = $isActive
                ? '<form method="post" action="' . site_url('admin/employees/' . $employeeId . '/deactivate') . '"><button type="submit">Desactiver</button></form>'
                : '<form method="post" action="' . site_url('admin/employees/' . $employeeId . '/activate') . '"><button type="submit">Activer</button></form>';

            $rows .= '<tr>'
                . '<td>' . $employeeId . '</td>'
                . '<td>' . esc((string) $employee['prenom'] . ' ' . (string) $employee['nom']) . '</td>'
                . '<td>' . esc((string) $employee['email']) . '</td>'
                . '<td>' . esc((string) $employee['role']) . '</td>'
                . '<td>' . ($isActive ? 'Oui' : 'Non') . '</td>'
                . '<td>' . $actionForm . '</td>'
                . '</tr>';
        }

        return '
            <h1>Liste des employes</h1>
            <p><a href="' . site_url('admin/dashboard') . '">Retour dashboard admin</a></p>
            <h2>Creer un employe</h2>
            <form method="post" action="' . site_url('admin/employees') . '">
                <label>Nom</label><br><input name="nom" required><br>
                <label>Prenom</label><br><input name="prenom" required><br>
                <label>Email</label><br><input type="email" name="email" required><br>
                <label>Mot de passe</label><br><input type="password" name="password" required><br>
                <label>Role</label><br><input name="role" value="employe" required><br>
                <label>Departement ID</label><br><input type="number" name="departement_id"><br>
                <label>Date embauche</label><br><input type="date" name="date_embauche" required><br>
                <button type="submit">Creer</button>
            </form>
            <table border="1" cellpadding="6" cellspacing="0">
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
                <tbody>' . $rows . '</tbody>
            </table>
        ';
    }

    public function createEmployee()
    {
        if ($guard = $this->guardAdmin()) {
            return $guard;
        }

        $data = [
            'nom'            => trim((string) $this->request->getPost('nom')),
            'prenom'         => trim((string) $this->request->getPost('prenom')),
            'email'          => trim((string) $this->request->getPost('email')),
            'password'       => password_hash((string) $this->request->getPost('password'), PASSWORD_DEFAULT),
            'role'           => strtolower(trim((string) $this->request->getPost('role'))),
            'departement_id' => $this->request->getPost('departement_id') !== '' ? (int) $this->request->getPost('departement_id') : null,
            'date_embauche'  => (string) $this->request->getPost('date_embauche'),
            'actif'          => 1,
        ];

        (new EmployeModel())->insert($data);
        session()->setFlashdata('admin_success', 'Employe cree avec succes.');

        return redirect()->to(site_url('admin/employees'));
    }

    public function deactivateEmployee(int $id)
    {
        if ($guard = $this->guardAdmin()) {
            return $guard;
        }

        (new EmployeModel())->update($id, ['actif' => 0]);
        session()->setFlashdata('admin_success', 'Employe desactive.');

        return redirect()->to(site_url('admin/employees'));
    }

    public function activateEmployee(int $id)
    {
        if ($guard = $this->guardAdmin()) {
            return $guard;
        }

        (new EmployeModel())->update($id, ['actif' => 1]);
        session()->setFlashdata('admin_success', 'Employe active.');

        return redirect()->to(site_url('admin/employees'));
    }

    public function conges()
    {
        if ($guard = $this->guardAdmin()) {
            return $guard;
        }

        $rowsData = $this->db()->table('Conges c')
            ->select('c.id, c.date_debut, c.date_fin, c.nb_jours, c.statut, c.motif, c.created_at, e.nom, e.prenom, t.libelle')
            ->join('Employes e', 'e.id = c.employe_id')
            ->join('Types_Conge t', 't.id = c.type_conge_id')
            ->orderBy('c.created_at', 'DESC')
            ->get()
            ->getResultArray();

        $rows = '';
        foreach ($rowsData as $row) {
            $actions = '';
            if ((string) $row['statut'] === 'en_attente') {
                $actions = '
                    <form method="post" action="' . site_url('admin/conges/' . (int) $row['id'] . '/statut') . '">
                        <button type="submit" name="statut" value="approuve">Approuver</button>
                        <button type="submit" name="statut" value="refuse">Refuser</button>
                    </form>
                ';
            }

            $rows .= '<tr>'
                . '<td>' . (int) $row['id'] . '</td>'
                . '<td>' . esc((string) $row['prenom'] . ' ' . (string) $row['nom']) . '</td>'
                . '<td>' . esc((string) $row['libelle']) . '</td>'
                . '<td>' . esc((string) $row['date_debut']) . ' -> ' . esc((string) $row['date_fin']) . '</td>'
                . '<td>' . esc((string) $row['nb_jours']) . '</td>'
                . '<td>' . esc((string) $row['statut']) . '</td>'
                . '<td>' . esc((string) ($row['motif'] ?? '')) . '</td>'
                . '<td>' . $actions . '</td>'
                . '</tr>';
        }

        return '
            <h1>Gestion des demandes de conge</h1>
            <p><a href="' . site_url('admin/dashboard') . '">Retour dashboard admin</a></p>
            <table border="1" cellpadding="6" cellspacing="0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Employe</th>
                        <th>Type</th>
                        <th>Periode</th>
                        <th>Jours</th>
                        <th>Statut</th>
                        <th>Motif</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>' . $rows . '</tbody>
            </table>
        ';
    }

    public function updateCongeStatut(int $congeId)
    {
        if ($guard = $this->guardAdmin()) {
            return $guard;
        }

        $newStatut = (string) $this->request->getPost('statut');
        if (! in_array($newStatut, ['approuve', 'refuse'], true)) {
            return redirect()->to(site_url('admin/conges'));
        }

        $this->db()->table('Conges')
            ->where('id', $congeId)
            ->update([
                'statut' => $newStatut,
                'traite_par' => (int) session()->get('employee_id'),
            ]);
        session()->setFlashdata('admin_success', 'Statut de la demande mis a jour.');

        return redirect()->to(site_url('admin/conges'));
    }

    public function departments()
    {
        if ($guard = $this->guardAdmin()) {
            return $guard;
        }

        $departments = $this->db()->table('Departements')->orderBy('id', 'DESC')->get()->getResultArray();
        $rows = '';
        foreach ($departments as $department) {
            $rows .= '<tr><td>' . (int) $department['id'] . '</td><td>' . esc((string) $department['nom']) . '</td><td>' . esc((string) ($department['description'] ?? '')) . '</td></tr>';
        }

        return '
            <h1>Departements</h1>
            <p><a href="' . site_url('admin/dashboard') . '">Retour dashboard admin</a></p>
            <form method="post" action="' . site_url('admin/departments') . '">
                <label>Nom</label><br><input name="nom" required><br>
                <label>Description</label><br><textarea name="description"></textarea><br>
                <button type="submit">Ajouter</button>
            </form>
            <table border="1" cellpadding="6" cellspacing="0">
                <thead><tr><th>ID</th><th>Nom</th><th>Description</th></tr></thead>
                <tbody>' . $rows . '</tbody>
            </table>
        ';
    }

    public function createDepartment()
    {
        if ($guard = $this->guardAdmin()) {
            return $guard;
        }

        $this->db()->table('Departements')->insert([
            'nom' => trim((string) $this->request->getPost('nom')),
            'description' => trim((string) $this->request->getPost('description')),
        ]);
        session()->setFlashdata('admin_success', 'Departement ajoute.');

        return redirect()->to(site_url('admin/departments'));
    }

    public function leaveTypes()
    {
        if ($guard = $this->guardAdmin()) {
            return $guard;
        }

        $types = $this->db()->table('Types_Conge')->orderBy('id', 'DESC')->get()->getResultArray();
        $rows = '';
        foreach ($types as $type) {
            $rows .= '<tr><td>' . (int) $type['id'] . '</td><td>' . esc((string) $type['libelle']) . '</td><td>' . esc((string) $type['jours_annuels']) . '</td><td>' . ((int) $type['deductible'] === 1 ? 'Oui' : 'Non') . '</td></tr>';
        }

        return '
            <h1>Types de conge</h1>
            <p><a href="' . site_url('admin/dashboard') . '">Retour dashboard admin</a></p>
            <form method="post" action="' . site_url('admin/leave-types') . '">
                <label>Libelle</label><br><input name="libelle" required><br>
                <label>Jours annuels</label><br><input type="number" min="0" name="jours_annuels" required><br>
                <label>Deductible (0 ou 1)</label><br><input type="number" min="0" max="1" name="deductible" value="1" required><br>
                <button type="submit">Ajouter</button>
            </form>
            <table border="1" cellpadding="6" cellspacing="0">
                <thead><tr><th>ID</th><th>Libelle</th><th>Jours annuels</th><th>Deductible</th></tr></thead>
                <tbody>' . $rows . '</tbody>
            </table>
        ';
    }

    public function createLeaveType()
    {
        if ($guard = $this->guardAdmin()) {
            return $guard;
        }

        $this->db()->table('Types_Conge')->insert([
            'libelle' => trim((string) $this->request->getPost('libelle')),
            'jours_annuels' => (int) $this->request->getPost('jours_annuels'),
            'deductible' => (int) $this->request->getPost('deductible') === 1 ? 1 : 0,
        ]);
        session()->setFlashdata('admin_success', 'Type de conge ajoute.');

        return redirect()->to(site_url('admin/leave-types'));
    }
}
