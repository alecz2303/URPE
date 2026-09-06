<?php

namespace Database\Seeders;

use App\Models\Therapy;
use Illuminate\Database\Seeder;

class TherapySeeder extends Seeder
{
    public function run(): void
    {
        $therapies = [
            [
                'name' => 'Vojta',
                'duration_minutes' => 40,
                'required_therapists' => 1,
                'color' => '#3B82F6',
                'is_active' => true,
            ],
            [
                'name' => 'Pediasuit',
                'duration_minutes' => 60,
                'required_therapists' => 2,
                'color' => '#EC4899',
                'is_active' => true,
            ],
        ];

        foreach ($therapies as $therapy) {
            Therapy::query()->updateOrCreate(
                ['name' => $therapy['name']],
                $therapy,
            );
        }
    }
}
