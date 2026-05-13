<?php

namespace App\Controllers;

use App\Models\EmployeModel;

class EmployeeAuthController extends BaseController
{
    /**
     * Roles autorises a se connecter.
     *
     * Adapte cette liste si les valeurs de la base sont differentes.
     *
     * @var list<string>
     */
    private array $allowedRoles = ['admin', 'employe', 'manager', 'rh'];

    public function showLogin(): string
    {
        return view('auth/login', [
            'error' => session()->getFlashdata('auth_error'),
            'email' => old('email', 'employe@techmada.mg'),
        ]);
    }

    public function login()
    {
        $email = trim((string) $this->request->getPost('email'));
        $password = (string) $this->request->getPost('password');

        if ($email === '' || $password === '') {
            session()->setFlashdata('auth_error', 'Email et mot de passe requis.');

            return redirect()->to('/login')->withInput();
        }

        $employeModel = new EmployeModel();
        $employee = $employeModel->where('email', $email)->first();

        if (! $employee || (int) ($employee['actif'] ?? 0) !== 1) {
            session()->setFlashdata('auth_error', 'Compte introuvable ou inactif.');

            return redirect()->to('/login')->withInput();
        }

        $role = strtolower(trim((string) ($employee['role'] ?? '')));
        if (! in_array($role, $this->allowedRoles, true)) {
            session()->setFlashdata('auth_error', 'Role non autorise pour la connexion.');

            return redirect()->to('/login')->withInput();
        }

        $storedPassword = (string) ($employee['password'] ?? '');
        $isValidPassword = password_verify($password, $storedPassword) || hash_equals($storedPassword, $password);

        if (! $isValidPassword) {
            session()->setFlashdata('auth_error', 'Email ou mot de passe invalide.');

            return redirect()->to('/login')->withInput();
        }

        session()->regenerate();
        session()->set([
            'employee_logged_in' => true,
            'employee_id'        => (int) $employee['id'],
            'employee_email'     => (string) $employee['email'],
            'employee_nom'       => trim(((string) ($employee['prenom'] ?? '')) . ' ' . ((string) ($employee['nom'] ?? ''))),
            'employee_role'      => $role,
        ]);

        return redirect()->to('/dashboard');
    }

    public function dashboard()
    {
        if (! session()->get('employee_logged_in')) {
            return redirect()->to('/login');
        }

        $db = db_connect();
        $employeeId = (int) session()->get('employee_id');
        $currentYear = (int) date('Y');

        $soldes = $db->table('Soldes s')
            ->select('s.*, t.libelle AS type_conge, (s.jours_attribues - s.jours_pris) AS jours_restants')
            ->join('Types_Conge t', 't.id = s.type_conge_id')
            ->where('s.employe_id', $employeeId)
            ->where('s.annee', $currentYear)
            ->orderBy('t.libelle', 'ASC')
            ->get()
            ->getResultArray();

        $demandes = $db->table('Conges c')
            ->select('c.*, t.libelle AS type_conge')
            ->join('Types_Conge t', 't.id = c.type_conge_id')
            ->where('c.employe_id', $employeeId)
            ->orderBy('c.created_at', 'DESC')
            ->limit(5)
            ->get()
            ->getResultArray();

        $statsRows = $db->table('Conges')
            ->select('statut, COUNT(*) AS total')
            ->where('employe_id', $employeeId)
            ->groupBy('statut')
            ->get()
            ->getResultArray();

        $stats = ['en_attente' => 0, 'approuve' => 0, 'refuse' => 0, 'annule' => 0];
        foreach ($statsRows as $row) {
            $stats[(string) $row['statut']] = (int) $row['total'];
        }

        $totalRestant = array_reduce(
            $soldes,
            static fn (float $carry, array $solde): float => $carry + (float) $solde['jours_restants'],
            0.0
        );

        return view('employee/dashboard', [
            'email' => (string) session()->get('employee_email'),
            'nom' => (string) session()->get('employee_nom'),
            'role' => (string) session()->get('employee_role'),
            'soldes' => $soldes,
            'demandes' => $demandes,
            'stats' => $stats,
            'totalRestant' => $totalRestant,
        ]);
    }

    public function logout()
    {
        session()->remove([
            'employee_logged_in',
            'employee_id',
            'employee_email',
            'employee_nom',
            'employee_role',
        ]);
        session()->regenerate();

        return redirect()->to('/login');
    }
}
