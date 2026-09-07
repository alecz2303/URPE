<?php

namespace Tests\Feature;

use App\Models\CenterOperatingHour;
use App\Models\Patient;
use App\Models\Therapist;
use App\Models\TherapistAvailabilityWindow;
use App\Models\Therapy;
use App\Models\User;
use Database\Seeders\DemoSiteSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DemoSiteSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_demo_site_seeder_creates_requested_sample_data(): void
    {
        config()->set('urpe.demo.password', 'ConfiguredDemoPassword!');

        $this->seed(DemoSiteSeeder::class);

        $this->assertSame(10, CenterOperatingHour::query()->count());
        $this->assertDatabaseHas('center_operating_hours', [
            'day_of_week' => 1,
            'opens_at' => '09:00:00',
            'closes_at' => '14:00:00',
            'is_enabled' => 1,
        ]);
        $this->assertDatabaseHas('center_operating_hours', [
            'day_of_week' => 5,
            'opens_at' => '16:00:00',
            'closes_at' => '18:00:00',
            'is_enabled' => 1,
        ]);

        $this->assertDatabaseHas('therapies', [
            'name' => 'Vojta',
            'duration_minutes' => 40,
            'required_therapists' => 1,
            'is_active' => 1,
        ]);
        $this->assertDatabaseHas('therapies', [
            'name' => 'Bipedestador',
            'duration_minutes' => 40,
            'required_therapists' => 1,
            'is_active' => 1,
        ]);
        $this->assertDatabaseHas('therapies', [
            'name' => 'Pediasuit',
            'duration_minutes' => 60,
            'required_therapists' => 2,
            'is_active' => 1,
        ]);

        $this->assertSame(5, Therapist::query()->whereIn('name', [
            'Jonatham Zambrano',
            'Terapeuta 1',
            'Terapeuta 2',
            'Terapeuta 3',
            'Terapeuta 4',
        ])->count());

        $this->assertSame(5, User::query()
            ->where('email', 'like', '%@demo.urpe.test')
            ->count());

        $jonatham = Therapist::query()->where('name', 'Jonatham Zambrano')->firstOrFail();
        $jonathamUser = User::query()->where('email', 'jonatham.zambrano@demo.urpe.test')->firstOrFail();

        $this->assertSame($jonathamUser->id, $jonatham->user_id);
        $this->assertSame('jonatham.zambrano@demo.urpe.test', $jonatham->email);
        $this->assertTrue($jonathamUser->is_active);
        $this->assertTrue($jonathamUser->hasRole('therapist'));
        $this->assertTrue(Hash::check('ConfiguredDemoPassword!', $jonathamUser->password));
        $this->assertSame(10, TherapistAvailabilityWindow::query()
            ->where('therapist_id', $jonatham->id)
            ->count());

        $this->assertTrue(Therapist::query()
            ->whereIn('name', ['Jonatham Zambrano', 'Terapeuta 1', 'Terapeuta 2', 'Terapeuta 3', 'Terapeuta 4'])
            ->get()
            ->every(fn (Therapist $therapist) => $therapist->user_id !== null));

        $this->assertSame(12, Patient::query()
            ->where('first_name', 'Paciente')
            ->where('last_name', 'like', 'Demo %')
            ->count());
        $this->assertTrue(Patient::query()
            ->where('first_name', 'Paciente')
            ->where('last_name', 'like', 'Demo %')
            ->get()
            ->every(fn (Patient $patient) => str_starts_with($patient->folio, 'URPE-')));
    }

    public function test_demo_site_seeder_is_safe_to_run_again_without_duplicate_sample_records(): void
    {
        $this->seed(DemoSiteSeeder::class);
        $this->seed(DemoSiteSeeder::class);

        $this->assertSame(10, CenterOperatingHour::query()->count());
        $this->assertSame(3, Therapy::query()->whereIn('name', ['Vojta', 'Bipedestador', 'Pediasuit'])->count());
        $this->assertSame(5, Therapist::query()->whereIn('name', [
            'Jonatham Zambrano',
            'Terapeuta 1',
            'Terapeuta 2',
            'Terapeuta 3',
            'Terapeuta 4',
        ])->count());
        $this->assertSame(5, User::query()->where('email', 'like', '%@demo.urpe.test')->count());
        $this->assertSame(50, TherapistAvailabilityWindow::query()->count());
        $this->assertSame(12, Patient::query()
            ->where('first_name', 'Paciente')
            ->where('last_name', 'like', 'Demo %')
            ->count());
    }
}
