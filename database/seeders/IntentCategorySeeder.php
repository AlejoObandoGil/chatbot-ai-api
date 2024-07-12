<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Intent\IntentCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class IntentCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $intentCategories = [
            'greetings',
            'only_information',
            'save_information',
            'goodbyes',
            'unanswered',
            'errors'
        ];

        foreach ($intentCategories as $category) {
            IntentCategory::create(['name' => $category]);
        }
    }
}
