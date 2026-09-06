<?php

namespace Tests\Feature;

use App\Models\AuditEvent;
use App\Models\CenterOperatingHour;
use App\Models\User;
use App\Services\AuditTrail;
use App\Services\CenterConfiguration;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class CenterConfigurationTest extends TestCase
{
    use RefreshDatabase;

    public function test_default_configuration_uses_urpe_identity_and_mexico_city_timezone(): void
    {
        $center = app(CenterConfiguration::class)->settings();

        $this->assertSame('URPE', $center->name);
        $this->assertSame('America/Mexico_City', $center->timezone);
    }

    public function test_default_weekly_hours_are_nine_to_eighteen_on_weekdays(): void
    {
        app(CenterConfiguration::class)->ensureDefaultHours();

        $this->assertDatabaseCount('center_operating_hours', 7);
        $this->assertDatabaseHas('center_operating_hours', [
            'day_of_week' => 1,
            'is_enabled' => true,
            'opens_at' => '09:00:00',
            'closes_at' => '18:00:00',
        ]);
        $this->assertDatabaseHas('center_operating_hours', [
            'day_of_week' => 7,
            'is_enabled' => false,
            'opens_at' => null,
            'closes_at' => null,
        ]);
    }

    public function test_open_check_respects_enabled_window_and_closing_boundary(): void
    {
        $service = app(CenterConfiguration::class);

        $this->assertTrue($service->isOpenAt(CarbonImmutable::parse('2026-09-07 09:00:00')));
        $this->assertTrue($service->isOpenAt(CarbonImmutable::parse('2026-09-07 17:59:59')));
        $this->assertFalse($service->isOpenAt(CarbonImmutable::parse('2026-09-07 18:00:00')));
        $this->assertFalse($service->isOpenAt(CarbonImmutable::parse('2026-09-06 12:00:00')));
    }

    public function test_interval_check_requires_entire_interval_inside_one_operating_window(): void
    {
        $service = app(CenterConfiguration::class);

        $this->assertTrue($service->isOpenDuring(
            CarbonImmutable::parse('2026-09-07 09:00:00'),
            CarbonImmutable::parse('2026-09-07 10:00:00'),
        ));

        $this->assertTrue($service->isOpenDuring(
            CarbonImmutable::parse('2026-09-07 17:00:00'),
            CarbonImmutable::parse('2026-09-07 18:00:00'),
        ));

        $this->assertFalse($service->isOpenDuring(
            CarbonImmutable::parse('2026-09-07 17:30:00'),
            CarbonImmutable::parse('2026-09-07 18:30:00'),
        ));

        $this->assertFalse($service->isOpenDuring(
            CarbonImmutable::parse('2026-09-07 18:00:00'),
            CarbonImmutable::parse('2026-09-07 18:30:00'),
        ));
    }

    public function test_interval_check_rejects_cross_day_and_invalid_ranges(): void
    {
        $service = app(CenterConfiguration::class);

        $this->assertFalse($service->isOpenDuring(
            CarbonImmutable::parse('2026-09-07 17:30:00'),
            CarbonImmutable::parse('2026-09-08 09:30:00'),
        ));

        $this->assertFalse($service->isOpenDuring(
            CarbonImmutable::parse('2026-09-07 10:00:00'),
            CarbonImmutable::parse('2026-09-07 10:00:00'),
        ));

        $this->assertFalse($service->isOpenDuring(
            CarbonImmutable::parse('2026-09-07 11:00:00'),
            CarbonImmutable::parse('2026-09-07 10:00:00'),
        ));
    }

    public function test_interval_check_does_not_bridge_a_closed_gap_between_windows(): void
    {
        CenterOperatingHour::query()->delete();
        CenterOperatingHour::query()->create([
            'day_of_week' => 1,
            'is_enabled' => true,
            'opens_at' => '09:00:00',
            'closes_at' => '13:00:00',
            'sort_order' => 0,
        ]);
        CenterOperatingHour::query()->create([
            'day_of_week' => 1,
            'is_enabled' => true,
            'opens_at' => '14:00:00',
            'closes_at' => '18:00:00',
            'sort_order' => 1,
        ]);

        $service = app(CenterConfiguration::class);

        $this->assertTrue($service->isOpenDuring(
            CarbonImmutable::parse('2026-09-07 09:30:00'),
            CarbonImmutable::parse('2026-09-07 12:30:00'),
        ));

        $this->assertFalse($service->isOpenDuring(
            CarbonImmutable::parse('2026-09-07 12:30:00'),
            CarbonImmutable::parse('2026-09-07 14:30:00'),
        ));
    }

    public function test_overlapping_windows_are_rejected(): void
    {
        $this->expectException(ValidationException::class);

        app(CenterConfiguration::class)->validateHours([
            1 => [
                ['is_enabled' => true, 'opens_at' => '09:00', 'closes_at' => '13:00'],
                ['is_enabled' => true, 'opens_at' => '12:00', 'closes_at' => '18:00'],
            ],
        ]);
    }

    public function test_configuration_update_is_persisted_and_audited(): void
    {
        $user = User::factory()->create();
        $service = app(CenterConfiguration::class);

        $service->update([
            'name' => 'URPE',
            'phone' => '9610000000',
            'email' => 'contacto@example.test',
            'address' => 'Tuxtla Gutiérrez, Chiapas',
            'timezone' => 'America/Mexico_City',
        ], [
            1 => [['is_enabled' => true, 'opens_at' => '08:00', 'closes_at' => '17:00']],
            2 => [['is_enabled' => false, 'opens_at' => null, 'closes_at' => null]],
        ], $user, app(AuditTrail::class));

        $this->assertDatabaseHas('center_settings', [
            'name' => 'URPE',
            'phone' => '9610000000',
        ]);
        $this->assertDatabaseHas('center_operating_hours', [
            'day_of_week' => 1,
            'opens_at' => '08:00:00',
            'closes_at' => '17:00:00',
        ]);

        $event = AuditEvent::query()->where('event', 'center.configuration_updated')->first();
        $this->assertNotNull($event);
        $this->assertSame($user->id, $event->actor_id);
        $this->assertTrue((bool) data_get($event->metadata, 'operating_hours_updated'));
    }

    public function test_multiple_non_overlapping_windows_are_supported(): void
    {
        app(CenterConfiguration::class)->validateHours([
            1 => [
                ['is_enabled' => true, 'opens_at' => '09:00', 'closes_at' => '13:00'],
                ['is_enabled' => true, 'opens_at' => '14:00', 'closes_at' => '18:00'],
            ],
        ]);

        $this->assertTrue(true);
    }
}
