<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSoldesTable extends Migration
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
            'annee' => [
                'type' => 'YEAR',
                'null' => false,
            ],
            'jours_attribues' => [
                'type'    => 'DECIMAL',
                'constraint' => '5,1',
                'null'    => false,
                'default' => 0,
            ],
            'jours_pris' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,1',
                'null'       => false,
                'default'    => 0,
            ],
            // jours_restants = jours_attribues - jours_pris (calculé en PHP ou vue SQL)
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
        $this->forge->addUniqueKey(['employe_id', 'type_conge_id', 'annee']);
        $this->forge->addForeignKey('employe_id',    'employes',     'id', 'CASCADE',  'CASCADE');
        $this->forge->addForeignKey('type_conge_id', 'types_conge',  'id', 'CASCADE',  'CASCADE');
        $this->forge->createTable('soldes');
    }

    public function down(): void
    {
        $this->forge->dropTable('soldes');
    }
}
