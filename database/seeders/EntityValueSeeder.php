<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Chatbot\EntityValue;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class EntityValueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        EntityValue::create([
            'entity_id' => 1,
            'value' => 'Internet'
        ]);

        EntityValue::create([
            'entity_id' => 1,
            'value' => 'Teléfono'
        ]);

        EntityValue::create([
            'entity_id' => 1,
            'value' => 'TV'
        ]);

        EntityValue::create([
            'entity_id' => 2,
            'value' => '100.000'
        ]);

        // ServiceType values
        EntityValue::create([
            'entity_id' => 3,
            'value' => 'Instalación'
        ]);

        EntityValue::create([
            'entity_id' => 3,
            'value' => 'Mantenimiento'
        ]);

        EntityValue::create([
            'entity_id' => 3,
            'value' => 'Actualización'
        ]);

        // IssueType values
        EntityValue::create([
            'entity_id' => 4,
            'value' => 'Conectividad'
        ]);

        EntityValue::create([
            'entity_id' => 4,
            'value' => 'Facturación'
        ]);

        EntityValue::create([
            'entity_id' => 4,
            'value' => 'Soporte tecnico'
        ]);
    }
}
