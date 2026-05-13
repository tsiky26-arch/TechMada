<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $this->db->table('Departements')->ignore(true)->insertBatch([
            ['id' => 1, 'nom' => 'Administration', 'description' => 'Direction et administration'],
            ['id' => 2, 'nom' => 'Ressources Humaines', 'description' => 'Gestion RH'],
            ['id' => 3, 'nom' => 'IT', 'description' => 'Developpement et support technique'],
        ]);

        $this->db->table('Types_Conge')->ignore(true)->insertBatch([
            ['id' => 1, 'libelle' => 'Conge annuel', 'jours_annuels' => 30, 'deductible' => 1],
            ['id' => 2, 'libelle' => 'Conge maladie', 'jours_annuels' => 10, 'deductible' => 0],
            ['id' => 3, 'libelle' => 'Conge special', 'jours_annuels' => 5, 'deductible' => 1],
        ]);

        $this->db->table('Employes')->ignore(true)->insertBatch([
            [
                'id' => 1,
                'nom' => 'Admin',
                'prenom' => 'TechMada',
                'email' => 'admin@techmada.mg',
                'password' => '$2y$10$9CALcPcMR5uBpEhTU82Uzufv2iTJodxPOtfbiBabG2EoDMxWQqpxa',
                'role' => 'admin',
                'departement_id' => 1,
                'date_embauche' => '2024-01-01',
                'actif' => 1,
            ],
            [
                'id' => 2,
                'nom' => 'Rabe',
                'prenom' => 'Marie',
                'email' => 'rh@techmada.mg',
                'password' => '$2y$10$5fiz8i93LJJ7BqhM0mZGE.yh6OtskzPTO/9E8r1jUQHGHF0aaD.yG',
                'role' => 'rh',
                'departement_id' => 2,
                'date_embauche' => '2024-02-01',
                'actif' => 1,
            ],
            [
                'id' => 3,
                'nom' => 'Rakoto',
                'prenom' => 'Soa',
                'email' => 'employe@techmada.mg',
                'password' => '$2y$10$Ns2KSwxIInUF.hxzvuGp.OxBfoDO8zKEauZxpYvp2Oz9Z5PjS0nNe',
                'role' => 'employe',
                'departement_id' => 3,
                'date_embauche' => '2024-03-01',
                'actif' => 1,
            ],
        ]);

        $year = (int) date('Y');

        $employes = $this->db->table('Employes')->select('id')->get()->getResultArray();
        $types = $this->db->table('Types_Conge')->select('id, jours_annuels')->get()->getResultArray();

        $soldes = [];
        foreach ($employes as $employe) {
            foreach ($types as $type) {
                $soldes[] = [
                    'employe_id' => (int) $employe['id'],
                    'type_conge_id' => (int) $type['id'],
                    'annee' => $year,
                    'jours_attribues' => (float) $type['jours_annuels'],
                    'jours_pris' => 0,
                ];
            }
        }

        if ($soldes !== []) {
            $this->db->table('Soldes')->ignore(true)->insertBatch($soldes);
        }
    }
}
