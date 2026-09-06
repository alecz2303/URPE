<?php

namespace App\Services;

use App\Models\CenterOperatingHour;
use App\Models\CenterSetting;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CenterConfiguration
{
    public function settings(): CenterSetting
    {
        return CenterSetting::query()->firstOrCreate([], [
            'name' => 'URPE',
            'timezone' => 'America/Mexico_City',
        ]);
    }

    public function ensureDefaultHours(): void
    {
        if (CenterOperatingHour::query()->exists()) {
            return;
        }

        DB::transaction(function (): void {
            foreach (range(1, 7) as $day) {
                CenterOperatingHour::query()->create([
                    'day_of_week' => $day,
                    'is_enabled' => $day <= 5,
                    'opens_at' => $day <= 5 ? '09:00:00' : null,
                    'closes_at' => $day <= 5 ? '18:00:00' : null,
                    'sort_order' => 0,
                ]);
            }
        });
    }

    public function weeklyHours(): array
    {
        $this->ensureDefaultHours();

        return CenterOperatingHour::query()
            ->orderBy('day_of_week')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('day_of_week')
            ->all();
    }

    public function isOpenAt(CarbonInterface $dateTime): bool
    {
        $this->ensureDefaultHours();

        $time = $dateTime->format('H:i:s');

        return CenterOperatingHour::query()
            ->where('day_of_week', $dateTime->isoWeekday())
            ->where('is_enabled', true)
            ->where('opens_at', '<=', $time)
            ->where('closes_at', '>', $time)
            ->exists();
    }

    public function isOpenDuring(CarbonInterface $startsAt, CarbonInterface $endsAt): bool
    {
        $this->ensureDefaultHours();

        if ($endsAt->lessThanOrEqualTo($startsAt)) {
            return false;
        }

        if (! $startsAt->isSameDay($endsAt)) {
            return false;
        }

        return CenterOperatingHour::query()
            ->where('day_of_week', $startsAt->isoWeekday())
            ->where('is_enabled', true)
            ->where('opens_at', '<=', $startsAt->format('H:i:s'))
            ->where('closes_at', '>=', $endsAt->format('H:i:s'))
            ->exists();
    }

    public function update(array $settings, array $hours, User $actor, AuditTrail $audit): void
    {
        $this->validateHours($hours);

        DB::transaction(function () use ($settings, $hours, $actor, $audit): void {
            $center = $this->settings();
            $beforeSettings = $center->only(['name', 'phone', 'email', 'address', 'timezone']);
            $center->update($settings);

            CenterOperatingHour::query()->delete();

            foreach ($hours as $day => $windows) {
                foreach (array_values($windows) as $index => $window) {
                    $enabled = (bool) ($window['is_enabled'] ?? false);

                    CenterOperatingHour::query()->create([
                        'day_of_week' => (int) $day,
                        'is_enabled' => $enabled,
                        'opens_at' => $enabled ? $this->normalizeTime($window['opens_at']) : null,
                        'closes_at' => $enabled ? $this->normalizeTime($window['closes_at']) : null,
                        'sort_order' => $index,
                    ]);
                }
            }

            $audit->record('center.configuration_updated', $center, [
                'before' => $beforeSettings,
                'after' => $center->only(['name', 'phone', 'email', 'address', 'timezone']),
                'operating_hours_updated' => true,
            ], $actor);
        });
    }

    public function validateHours(array $hours): void
    {
        foreach ($hours as $day => $windows) {
            if ((int) $day < 1 || (int) $day > 7 || ! is_array($windows)) {
                throw ValidationException::withMessages(['hours' => 'La configuración de horarios no es válida.']);
            }

            $enabled = collect($windows)
                ->filter(fn (array $window): bool => (bool) ($window['is_enabled'] ?? false))
                ->map(function (array $window): array {
                    $window['opens_at'] = isset($window['opens_at']) ? $this->normalizeTime($window['opens_at']) : null;
                    $window['closes_at'] = isset($window['closes_at']) ? $this->normalizeTime($window['closes_at']) : null;

                    return $window;
                })
                ->sortBy('opens_at')
                ->values();

            foreach ($enabled as $index => $window) {
                $opens = $window['opens_at'] ?? null;
                $closes = $window['closes_at'] ?? null;

                if (! $opens || ! $closes || $opens >= $closes) {
                    throw ValidationException::withMessages(['hours' => 'Cada horario habilitado debe tener una apertura anterior al cierre.']);
                }

                if ($index > 0 && $opens < $enabled[$index - 1]['closes_at']) {
                    throw ValidationException::withMessages(['hours' => 'Los horarios de un mismo día no pueden traslaparse.']);
                }
            }
        }
    }

    private function normalizeTime(string $time): string
    {
        $time = trim($time);

        if (preg_match('/^([01]\d|2[0-3]):[0-5]\d$/', $time) === 1) {
            return $time.':00';
        }

        if (preg_match('/^([01]\d|2[0-3]):[0-5]\d:[0-5]\d$/', $time) === 1) {
            return $time;
        }

        throw ValidationException::withMessages([
            'hours' => 'Las horas deben tener un formato válido HH:MM.',
        ]);
    }
}
