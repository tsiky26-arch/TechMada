<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCongesTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INTEGER',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'employe_id' => [
                'type'     => 'INTEGER',
                'unsigned' => true,
                'null'     => false,
            ],
            'type_conge_id' => [
                'type'     => 'INTEGER',
                'unsigned' => true,
                'null'     => false,
            ],
            'date_debut' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'date_fin' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'nb_jours' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,1',
                'null'       => false,
            ],
            'motif' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'statut' => [
                'type'       => 'ENUM',
                'constraint' => ['en_attente', 'approuve', 'refuse', 'annule'],
                'null'       => false,
                'default'    => 'en_attente',
            ],
            'commentaire_rh' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'traite_par' => [
                'type'     => 'INTEGER',
                'unsigned' => true,
                'null'     => true,     // null si pas encore traité
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('employe_id',    'employes',    'id', 'CASCADE',  'CASCADE');
        $this->forge->addForeignKey('type_conge_id', 'types_conge', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->addForeignKey('traite_par',    'employes',    'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('conges');
    }

    public function down(): void
    {
        $this->forge->dropTable('conges');
    }
}
