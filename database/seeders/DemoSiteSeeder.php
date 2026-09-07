<?php

namespace Database\Seeders;

use App\Models\CenterOperatingHour;
use App\Models\Patient;
use App\Models\Therapist;
use App\Models\TherapistAvailabilityWindow;
use App\Models\Therapy;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DemoSiteSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(AuthorizationSeeder::class);

        DB::transaction(function (): void {
            $this->seedCenterHours();
            $this->seedTherapies();
            $this->seedTherapists();
            $this->seedPatients();
        });
    }

    private function seedCenterHours(): void
    {
        CenterOperatingHour::query()->delete();

        foreach (range(1, 5) as $day) {
            CenterOperatingHour::query()->create([
                'day_of_week' => $day,
                'is_enabled' => true,
                'opens_at' => '09:00:00',
                'closes_at' => '14:00:00',
                'sort_order' => 0,
            ]);

            CenterOperatingHour::query()->create([
                'day_of_week' => $day,
                'is_enabled' => true,
                'opens_at' => '16:00:00',
                'closes_at' => '18:00:00',
                'sort_order' => 1,
            ]);
        }
    }

    private function seedTherapies(): void
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
                'name' => 'Bipedestador',
                'duration_minutes' => 40,
                'required_therapists' => 1,
                'color' => '#14B8A6',
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

    private function seedTherapists(): void
    {
        $demoPassword = (string) config('urpe.demo.password');

        $therapists = [
            [
                'name' => 'Jonatham Zambrano',
                'email' => 'jonatham.zambrano@demo.urpe.test',
            ],
            [
                'name' => 'Terapeuta 1',
                'email' => 'terapeuta1@demo.urpe.test',
            ],
            [
                'name' => 'Terapeuta 2',
                'email' => 'terapeuta2@demo.urpe.test',
            ],
            [
                'name' => 'Terapeuta 3',
                'email' => 'terapeuta3@demo.urpe.test',
            ],
            [
                'name' => 'Terapeuta 4',
                'email' => 'terapeuta4@demo.urpe.test',
            ],
        ];

        foreach ($therapists as $demoTherapist) {
            $user = User::query()->updateOrCreate(
                ['email' => $demoTherapist['email']],
                [
                    'name' => $demoTherapist['name'],
                    'password' => $demoPassword,
                    'is_active' => true,
                ],
            );
            $user->syncRoles(['therapist']);

            $therapist = Therapist::query()->updateOrCreate(
                ['name' => $demoTherapist['name']],
                [
                    'user_id' => $user->id,
                    'professional_title' => 'Fisioterapeuta',
                    'email' => $demoTherapist['email'],
                    'is_active' => true,
                    'notes' => 'Perfil generado para sitio de demostración.',
                ],
            );

            $therapist->availabilityWindows()->delete();

            foreach (range(1, 5) as $day) {
                TherapistAvailabilityWindow::query()->create([
                    'therapist_id' => $therapist->id,
                    'day_of_week' => $day,
                    'is_enabled' => true,
                    'starts_at' => '09:00:00',
                    'ends_at' => '14:00:00',
                    'sort_order' => 0,
                ]);

                TherapistAvailabilityWindow::query()->create([
                    'therapist_id' => $therapist->id,
                    'day_of_week' => $day,
                    'is_enabled' => true,
                    'starts_at' => '16:00:00',
                    'ends_at' => '18:00:00',
                    'sort_order' => 1,
                ]);
            }
        }
    }

    private function seedPatients(): void
    {
        $patients = [
            ['first_name' => 'Paciente', 'last_name' => 'Demo 01', 'date_of_birth' => '2018-02-14', 'sex' => 'male'],
            ['first_name' => 'Paciente', 'last_name' => 'Demo 02', 'date_of_birth' => '2019-05-23', 'sex' => 'female'],
            ['first_name' => 'Paciente', 'last_name' => 'Demo 03', 'date_of_birth' => '2020-01-11', 'sex' => 'male'],
            ['first_name' => 'Paciente', 'last_name' => 'Demo 04', 'date_of_birth' => '2017-09-30', 'sex' => 'female'],
            ['first_name' => 'Paciente', 'last_name' => 'Demo 05', 'date_of_birth' => '2021-03-07', 'sex' => 'male'],
            ['first_name' => 'Paciente', 'last_name' => 'Demo 06', 'date_of_birth' => '2018-11-19', 'sex' => 'female'],
            ['first_name' => 'Paciente', 'last_name' => 'Demo 07', 'date_of_birth' => '2019-08-02', 'sex' => 'male'],
            ['first_name' => 'Paciente', 'last_name' => 'Demo 08', 'date_of_birth' => '2020-06-16', 'sex' => 'female'],
            ['first_name' => 'Paciente', 'last_name' => 'Demo 09', 'date_of_birth' => '2016-12-04', 'sex' => 'male'],
            ['first_name' => 'Paciente', 'last_name' => 'Demo 10', 'date_of_birth' => '2021-10-28', 'sex' => 'female'],
            ['first_name' => 'Paciente', 'last_name' => 'Demo 11', 'date_of_birth' => '2017-04-21', 'sex' => 'male'],
            ['first_name' => 'Paciente', 'last_name' => 'Demo 12', 'date_of_birth' => '2019-12-09', 'sex' => 'female'],
        ];

        foreach ($patients as $patient) {
            Patient::query()->updateOrCreate(
                [
                    'first_name' => $patient['first_name'],
                    'last_name' => $patient['last_name'],
                ],
                array_merge($patient, [
                    'is_active' => true,
                    'administrative_notes' => 'Paciente ficticio generado para sitio de demostración.',
                ]),
            );
        }
    }
}
