<?php

namespace App\Controllers;

class EmployeeAuthController extends BaseController
{
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
        $email = (string) $this->request->getPost('email');
        $password = (string) $this->request->getPost('password');

        // Identifiants temporaires en attendant l'integration de la base de donnees.
        if ($email === 'employe@techmada.com' && $password === '123456') {
            session()->set([
                'employee_logged_in' => true,
                'employee_email'     => $email,
            ]);

            return redirect()->to(site_url('employee/dashboard'));
        }

        session()->setFlashdata('auth_error', 'Email ou mot de passe invalide.');

        return redirect()->to(site_url('employee/login'));
    }

    public function dashboard()
    {
        if (! session()->get('employee_logged_in')) {
            return redirect()->to(site_url('employee/login'));
        }

        $email = (string) session()->get('employee_email');

        return '
            <h1>Espace Employe</h1>
            <p>Connecte en tant que: ' . esc($email) . '</p>
            <form method="post" action="' . site_url('employee/logout') . '">
                <button type="submit">Se deconnecter</button>
            </form>
        ';
    }

    public function logout()
    {
        session()->remove(['employee_logged_in', 'employee_email']);

        return redirect()->to(site_url('employee/login'));
    }
}
