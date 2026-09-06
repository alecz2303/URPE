<?php

namespace App\Services;

use App\Models\Therapist;
use App\Models\TherapistAvailabilityWindow;
use App\Models\TherapistBlock;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TherapistAvailability
{
    public function __construct(private readonly CenterConfiguration $center)
    {
    }

    public function replaceWeeklySchedule(Therapist $therapist, array $hours, User $actor, AuditTrail $audit): void
    {
        $normalized = $this->validateWeeklySchedule($hours);

        DB::transaction(function () use ($therapist, $normalized, $actor, $audit): void {
            $therapist->availabilityWindows()->delete();

            foreach ($normalized as $day => $windows) {
                foreach ($windows as $index => $window) {
                    TherapistAvailabilityWindow::query()->create([
                        'therapist_id' => $therapist->id,
                        'day_of_week' => $day,
                        'is_enabled' => true,
                        'starts_at' => $window['starts_at'],
                        'ends_at' => $window['ends_at'],
                        'sort_order' => $index,
                    ]);
                }
            }

            $audit->record('therapist.availability_updated', $therapist, [
                'weekly_schedule_updated' => true,
            ], $actor);
        });
    }

    public function validateWeeklySchedule(array $hours): array
    {
        $normalized = [];
        $centerHours = $this->center->weeklyHours();

        foreach ($hours as $day => $windows) {
            $day = (int) $day;

            if ($day < 1 || $day > 7 || ! is_array($windows)) {
                throw ValidationException::withMessages(['availability' => 'La disponibilidad semanal no es válida.']);
            }

            $windows = collect($windows)
                ->filter(fn (array $window): bool => (bool) ($window['is_enabled'] ?? true))
                ->map(fn (array $window): array => [
                    'starts_at' => $this->normalizeTime((string) ($window['starts_at'] ?? '')),
                    'ends_at' => $this->normalizeTime((string) ($window['ends_at'] ?? '')),
                ])
                ->sortBy('starts_at')
                ->values()
                ->all();

            foreach ($windows as $index => $window) {
                if ($window['starts_at'] >= $window['ends_at']) {
                    throw ValidationException::withMessages(['availability' => 'Cada horario del terapeuta debe iniciar antes de terminar.']);
                }

                if ($index > 0 && $window['starts_at'] < $windows[$index - 1]['ends_at']) {
                    throw ValidationException::withMessages(['availability' => 'Los horarios del terapeuta no pueden traslaparse.']);
                }

                $fitsCenter = collect($centerHours[$day] ?? [])->contains(function ($centerWindow) use ($window): bool {
                    return $centerWindow->is_enabled
                        && $centerWindow->opens_at <= $window['starts_at']
                        && $centerWindow->closes_at >= $window['ends_at'];
                });

                if (! $fitsCenter) {
                    throw ValidationException::withMessages(['availability' => 'La disponibilidad del terapeuta debe quedar dentro de una ventana operativa del centro.']);
                }
            }

            $normalized[$day] = $windows;
        }

        return $normalized;
    }

    public function addBlock(Therapist $therapist, CarbonInterface $startsAt, CarbonInterface $endsAt, ?string $reason, User $actor, AuditTrail $audit): TherapistBlock
    {
        if ($endsAt->lessThanOrEqualTo($startsAt)) {
            throw ValidationException::withMessages(['block' => 'El bloqueo debe terminar después de iniciar.']);
        }

        return DB::transaction(function () use ($therapist, $startsAt, $endsAt, $reason, $actor, $audit): TherapistBlock {
            $block = $therapist->blocks()->create([
                'starts_at' => $startsAt,
                'ends_at' => $endsAt,
                'reason' => $reason,
            ]);

            $audit->record('therapist.block_created', $therapist, [
                'block_id' => $block->id,
                'starts_at' => $startsAt->toIso8601String(),
                'ends_at' => $endsAt->toIso8601String(),
            ], $actor);

            return $block;
        });
    }

    public function isAvailableDuring(Therapist $therapist, CarbonInterface $startsAt, CarbonInterface $endsAt): bool
    {
        if (! $therapist->is_active || $endsAt->lessThanOrEqualTo($startsAt) || ! $startsAt->isSameDay($endsAt)) {
            return false;
        }

        if (! $this->center->isOpenDuring($startsAt, $endsAt)) {
            return false;
        }

        $insideWeeklyWindow = $therapist->availabilityWindows()
            ->where('day_of_week', $startsAt->isoWeekday())
            ->where('is_enabled', true)
            ->where('starts_at', '<=', $startsAt->format('H:i:s'))
            ->where('ends_at', '>=', $endsAt->format('H:i:s'))
            ->exists();

        if (! $insideWeeklyWindow) {
            return false;
        }

        return ! $therapist->blocks()
            ->where('starts_at', '<', $endsAt)
            ->where('ends_at', '>', $startsAt)
            ->exists();
    }

    private function normalizeTime(string $time): string
    {
        $time = trim($time);

        if (preg_match('/^([01]\\d|2[0-3]):[0-5]\\d$/', $time) === 1) {
            return $time.':00';
        }

        if (preg_match('/^([01]\\d|2[0-3]):[0-5]\\d:[0-5]\\d$/', $time) === 1) {
            return $time;
        }

        throw ValidationException::withMessages(['availability' => 'Las horas deben tener un formato válido HH:MM.']);
    }
}
