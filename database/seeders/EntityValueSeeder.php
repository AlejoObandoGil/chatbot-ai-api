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
            'entitie_id' => 1,
            'value' => 'Internet'
        ]);

        EntityValue::create([
            'entitie_id' => 1,
            'value' => 'Teléfono'
        ]);

        EntityValue::create([
            'entitie_id' => 1,
            'value' => 'TV'
        ]);

        EntityValue::create([
            'entitie_id' => 2,
            'value' => '100.000'
        ]);

        // ServiceType values
        EntityValue::create([
            'entitie_id' => 3,
            'value' => 'Instalación'
        ]);

        EntityValue::create([
            'entitie_id' => 3,
            'value' => 'Mantenimiento'
        ]);

        EntityValue::create([
            'entitie_id' => 3,
            'value' => 'Actualización'
        ]);

        // IssueType values
        EntityValue::create([
            'entitie_id' => 4,
            'value' => 'Conectividad'
        ]);

        EntityValue::create([
            'entitie_id' => 4,
            'value' => 'Facturación'
        ]);

        EntityValue::create([
            'entitie_id' => 4,
            'value' => 'Soporte tecnico'
        ]);
    }
}
