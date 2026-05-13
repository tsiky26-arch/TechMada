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
    private array $allowedRoles = ['employe', 'manager', 'rh'];

    public function showLogin(): string
    {
        $error = session()->getFlashdata('auth_error');

        return '
            <h1>Connexion Employe</h1>
            ' . ($error ? '<p style="color:red;">' . esc($error) . '</p>' : '') . '
            <form method="post" action="' . site_url('employee/login') . '">
                <label for="email">Email</label><br>
                <input id="email" type="email" name="email" required><br><br>

                <label for="password">Mot de passe</label><br>
                <input id="password" type="password" name="password" required><br><br>

                <button type="submit">Se connecter</button>
            </form>
        ';
    }

    public function login()
    {
        $email = trim((string) $this->request->getPost('email'));
        $password = (string) $this->request->getPost('password');

        if ($email === '' || $password === '') {
            session()->setFlashdata('auth_error', 'Email et mot de passe requis.');

            return redirect()->to(site_url('employee/login'));
        }

        $employeModel = new EmployeModel();
        $employee = $employeModel->where('email', $email)->first();

        if (! $employee || (int) ($employee['actif'] ?? 0) !== 1) {
            session()->setFlashdata('auth_error', 'Compte introuvable ou inactif.');

            return redirect()->to(site_url('employee/login'));
        }

        $role = strtolower(trim((string) ($employee['role'] ?? '')));
        if (! in_array($role, $this->allowedRoles, true)) {
            session()->setFlashdata('auth_error', 'Role non autorise pour la connexion.');

            return redirect()->to(site_url('employee/login'));
        }

        $storedPassword = (string) ($employee['password'] ?? '');
        $isValidPassword = password_verify($password, $storedPassword) || hash_equals($storedPassword, $password);

        if (! $isValidPassword) {
            session()->setFlashdata('auth_error', 'Email ou mot de passe invalide.');

            return redirect()->to(site_url('employee/login'));
        }

        session()->regenerate();
        session()->set([
            'employee_logged_in' => true,
            'employee_id'        => (int) $employee['id'],
            'employee_email'     => (string) $employee['email'],
            'employee_nom'       => trim(((string) ($employee['prenom'] ?? '')) . ' ' . ((string) ($employee['nom'] ?? ''))),
            'employee_role'      => $role,
        ]);

        return redirect()->to(site_url('employee/dashboard'));
    }

    public function dashboard()
    {
        if (! session()->get('employee_logged_in')) {
            return redirect()->to(site_url('employee/login'));
        }

        $email = (string) session()->get('employee_email');
        $nom = (string) session()->get('employee_nom');
        $role = (string) session()->get('employee_role');

        return '
            <h1>Espace Employe</h1>
            <p>Nom: ' . esc($nom) . '</p>
            <p>Connecte en tant que: ' . esc($email) . '</p>
            <p>Role: ' . esc($role) . '</p>
            <form method="post" action="' . site_url('employee/logout') . '">
                <button type="submit">Se deconnecter</button>
            </form>
        ';
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

        return redirect()->to(site_url('employee/login'));
    }
}
