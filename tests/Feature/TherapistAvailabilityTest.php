<?php

namespace Tests\Feature;

use App\Models\AuditEvent;
use App\Models\Therapist;
use App\Models\User;
use App\Services\AuditTrail;
use App\Services\TherapistAvailability;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class TherapistAvailabilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_therapist_profile_can_be_linked_to_an_internal_user(): void
    {
        $user = User::factory()->create();
        $therapist = Therapist::query()->create([
            'user_id' => $user->id,
            'name' => 'Terapeuta URPE',
            'professional_title' => 'Fisioterapeuta',
        ]);

        $this->assertTrue($therapist->is_active);
        $this->assertTrue($therapist->user->is($user));
    }

    public function test_weekly_schedule_must_fit_inside_center_operating_hours(): void
    {
        $this->expectException(ValidationException::class);

        app(TherapistAvailability::class)->validateWeeklySchedule([
            1 => [['starts_at' => '08:00', 'ends_at' => '10:00']],
        ]);
    }

    public function test_overlapping_therapist_windows_are_rejected(): void
    {
        $this->expectException(ValidationException::class);

        app(TherapistAvailability::class)->validateWeeklySchedule([
            1 => [
                ['starts_at' => '09:00', 'ends_at' => '13:00'],
                ['starts_at' => '12:30', 'ends_at' => '17:00'],
            ],
        ]);
    }

    public function test_multiple_non_overlapping_windows_are_supported_and_audited(): void
    {
        $actor = User::factory()->create();
        $therapist = Therapist::query()->create(['name' => 'Terapeuta URPE']);
        $service = app(TherapistAvailability::class);

        $service->replaceWeeklySchedule($therapist, [
            1 => [
                ['starts_at' => '09:00', 'ends_at' => '13:00'],
                ['starts_at' => '14:00', 'ends_at' => '18:00'],
            ],
        ], $actor, app(AuditTrail::class));

        $this->assertDatabaseCount('therapist_availability_windows', 2);
        $this->assertDatabaseHas('therapist_availability_windows', [
            'therapist_id' => $therapist->id,
            'day_of_week' => 1,
            'starts_at' => '09:00:00',
            'ends_at' => '13:00:00',
        ]);
        $this->assertDatabaseHas('audit_events', [
            'actor_id' => $actor->id,
            'event' => 'therapist.availability_updated',
        ]);
    }

    public function test_therapist_is_available_only_when_full_interval_fits_weekly_window(): void
    {
        $actor = User::factory()->create();
        $therapist = Therapist::query()->create(['name' => 'Terapeuta URPE']);
        $service = app(TherapistAvailability::class);
        $service->replaceWeeklySchedule($therapist, [
            1 => [['starts_at' => '09:00', 'ends_at' => '17:00']],
        ], $actor, app(AuditTrail::class));

        $this->assertTrue($service->isAvailableDuring(
            $therapist,
            CarbonImmutable::parse('2026-09-07 10:00:00'),
            CarbonImmutable::parse('2026-09-07 11:00:00'),
        ));
        $this->assertFalse($service->isAvailableDuring(
            $therapist,
            CarbonImmutable::parse('2026-09-07 16:30:00'),
            CarbonImmutable::parse('2026-09-07 17:30:00'),
        ));
    }

    public function test_block_makes_overlapping_interval_unavailable_and_is_audited(): void
    {
        $actor = User::factory()->create();
        $therapist = Therapist::query()->create(['name' => 'Terapeuta URPE']);
        $service = app(TherapistAvailability::class);
        $service->replaceWeeklySchedule($therapist, [
            1 => [['starts_at' => '09:00', 'ends_at' => '18:00']],
        ], $actor, app(AuditTrail::class));

        $block = $service->addBlock(
            $therapist,
            CarbonImmutable::parse('2026-09-07 12:00:00'),
            CarbonImmutable::parse('2026-09-07 13:00:00'),
            'Ausencia',
            $actor,
            app(AuditTrail::class),
        );

        $this->assertFalse($service->isAvailableDuring(
            $therapist,
            CarbonImmutable::parse('2026-09-07 12:30:00'),
            CarbonImmutable::parse('2026-09-07 13:30:00'),
        ));
        $this->assertTrue($service->isAvailableDuring(
            $therapist,
            CarbonImmutable::parse('2026-09-07 13:00:00'),
            CarbonImmutable::parse('2026-09-07 14:00:00'),
        ));

        $event = AuditEvent::query()->where('event', 'therapist.block_created')->first();
        $this->assertNotNull($event);
        $this->assertSame($block->id, data_get($event->metadata, 'block_id'));
    }

    public function test_inactive_therapist_is_never_available(): void
    {
        $actor = User::factory()->create();
        $therapist = Therapist::query()->create(['name' => 'Terapeuta URPE', 'is_active' => false]);
        $service = app(TherapistAvailability::class);
        $service->replaceWeeklySchedule($therapist, [
            1 => [['starts_at' => '09:00', 'ends_at' => '18:00']],
        ], $actor, app(AuditTrail::class));

        $this->assertFalse($service->isAvailableDuring(
            $therapist,
            CarbonImmutable::parse('2026-09-07 10:00:00'),
            CarbonImmutable::parse('2026-09-07 11:00:00'),
        ));
    }

    public function test_invalid_block_range_is_rejected(): void
    {
        $this->expectException(ValidationException::class);

        $actor = User::factory()->create();
        $therapist = Therapist::query()->create(['name' => 'Terapeuta URPE']);

        app(TherapistAvailability::class)->addBlock(
            $therapist,
            CarbonImmutable::parse('2026-09-07 13:00:00'),
            CarbonImmutable::parse('2026-09-07 12:00:00'),
            null,
            $actor,
            app(AuditTrail::class),
        );
    }
}
