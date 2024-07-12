<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Intent\IntentTrainingPhrase;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class IntentTrainingPhraseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        IntentTrainingPhrase::create([
            'intent_id' => 1,
            'phrase' => 'Quiero saber sobre el plan de internet'
        ]);

        IntentTrainingPhrase::create([
            'intent_id' => 1,
            'phrase' => '¿Qué tipo de planes tienen?'
        ]);

        IntentTrainingPhrase::create([
            'intent_id' => 2,
            'phrase' => 'Me gustaría saber mi saldo'
        ]);

        IntentTrainingPhrase::create([
            'intent_id' => 2,
            'phrase' => 'Quiero saber el precio de mi internet'
        ]);

        IntentTrainingPhrase::create([
            'intent_id' => 3,
            'phrase' => 'Quiero reportar un problema'
        ]);

        IntentTrainingPhrase::create([
            'intent_id' => 3,
            'phrase' => 'Tengo un problema con mi servicio'
        ]);

        IntentTrainingPhrase::create([
            'intent_id' => 4,
            'phrase' => 'Quiero solicitar un servicio'
        ]);

        IntentTrainingPhrase::create([
            'intent_id' => 4,
            'phrase' => 'Necesito mantenimiento para mi internet'
        ]);
    }
}
