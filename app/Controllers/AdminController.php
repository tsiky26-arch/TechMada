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
        $departementCount = (int) $db->table('Departements')->countAllResults();
        $leaveTypeCount = (int) $db->table('Types_Conge')->countAllResults();
        $monthlyRequests = (int) $db->table('Conges')
            ->where('statut', 'approuve')
            ->where('strftime("%Y-%m", date_debut) =', date('Y-m'))
            ->countAllResults();
        $approvedCount = $monthlyRequests;

        return view('admin/dashboard', [
            'email' => (string) session()->get('employee_email'),
            'nom' => (string) session()->get('employee_nom'),
            'stats' => [
                'active_employees' => $activeCount,
                'pending_requests' => $pendingCount,
                'approved_requests' => $approvedCount,
                'departments' => $departementCount,
                'leave_types' => $leaveTypeCount,
                'monthly_requests' => $monthlyRequests,
            ],
        ]);
    }

    public function employees()
    {
        if ($guard = $this->guardAdmin()) {
            return $guard;
        }

        $employees = (new EmployeModel())->orderBy('id', 'DESC')->findAll();
        $departments = $this->db()->table('Departements')->orderBy('nom', 'ASC')->get()->getResultArray();

        return view('admin/employees', [
            'employees' => $employees,
            'departments' => $departments,
        ]);
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

        $demandes = [];
        foreach ($rowsData as $row) {
            $demandes[] = [
                'id' => (int) $row['id'],
                'employe_nom' => $row['prenom'] . ' ' . $row['nom'],
                'type_conge' => $row['libelle'],
                'date_debut' => $row['date_debut'],
                'date_fin' => $row['date_fin'],
                'nb_jours' => $row['nb_jours'],
                'statut' => $row['statut'],
                'motif' => $row['motif'] ?? '',
            ];
        }

        return view('admin/conges', [
            'demandes' => $demandes,
        ]);
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

        $db = $this->db();
        $conge = $db->table('Conges')
            ->where('id', $congeId)
            ->get()
            ->getRowArray();

        if (! $conge) {
            session()->setFlashdata('admin_success', 'Demande introuvable.');
            return redirect()->to(site_url('admin/conges'));
        }

        if ((string) $conge['statut'] !== 'en_attente') {
            session()->setFlashdata('admin_success', 'Seules les demandes en attente peuvent etre traitees ici.');
            return redirect()->to(site_url('admin/conges'));
        }

        if ($newStatut === 'approuve') {
            $annee = (int) date('Y', strtotime((string) $conge['date_debut']));
            $solde = $db->table('Soldes')
                ->where('employe_id', (int) $conge['employe_id'])
                ->where('type_conge_id', (int) $conge['type_conge_id'])
                ->where('annee', $annee)
                ->get()
                ->getRowArray();

            if (! $solde) {
                session()->setFlashdata('admin_success', 'Aucun solde trouve pour approuver cette demande.');
                return redirect()->to(site_url('admin/conges'));
            }

            $nbJours = (float) $conge['nb_jours'];
            $joursPris = (float) $solde['jours_pris'];
            $joursAttribues = (float) $solde['jours_attribues'];

            if ($joursPris + $nbJours > $joursAttribues) {
                session()->setFlashdata('admin_success', 'Solde insuffisant pour approuver cette demande.');
                return redirect()->to(site_url('admin/conges'));
            }

            $db->transStart();
            $db->table('Soldes')
                ->where('id', (int) $solde['id'])
                ->update(['jours_pris' => $joursPris + $nbJours]);
        } else {
            $db->transStart();
        }

        $db->table('Conges')
            ->where('id', $congeId)
            ->update([
                'statut' => $newStatut,
                'traite_par' => (int) session()->get('employee_id'),
            ]);
        $db->transComplete();

        session()->setFlashdata('admin_success', 'Statut de la demande mis a jour.');
        return redirect()->to(site_url('admin/conges'));
    }

    public function departments()
    {
        if ($guard = $this->guardAdmin()) {
            return $guard;
        }

        $departments = $this->db()->table('Departements')->orderBy('id', 'DESC')->get()->getResultArray();

        return view('admin/departments', [
            'departments' => $departments,
        ]);
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

        return view('admin/leave-types', [
            'types' => $types,
        ]);
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
