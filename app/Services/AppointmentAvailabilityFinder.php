<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Therapist;
use App\Models\Therapy;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;

class AppointmentAvailabilityFinder
{
    public function __construct(
        private readonly CenterConfiguration $center,
        private readonly TherapistAvailability $availability,
    ) {
    }

    public function forDate(Therapy $therapy, CarbonInterface $date): array
    {
        $day = CarbonImmutable::instance($date)->startOfDay();
        $windows = collect($this->center->weeklyHours()[$day->isoWeekday()] ?? [])
            ->filter(fn ($window): bool => $window->is_enabled && filled($window->opens_at) && filled($window->closes_at))
            ->values();

        $therapists = Therapist::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $slots = collect();

        foreach ($windows as $window) {
            $windowStart = $day->setTimeFromTimeString($window->opens_at);
            $windowEnd = $day->setTimeFromTimeString($window->closes_at);

            for ($start = $windowStart; $start->addMinutes($therapy->duration_minutes)->lte($windowEnd); $start = $start->addMinutes($therapy->duration_minutes)) {
                $end = $start->addMinutes($therapy->duration_minutes);
                $therapistStates = $therapists->map(function (Therapist $therapist) use ($start, $end): array {
                    if (! $this->availability->isAvailableDuring($therapist, $start, $end)) {
                        return [
                            'id' => $therapist->id,
                            'name' => $therapist->name,
                            'available' => false,
                            'reason' => 'Fuera de horario o con ausencia/bloqueo',
                        ];
                    }

                    $overlap = Appointment::query()
                        ->where('status', '!=', Appointment::STATUS_CANCELLED)
                        ->where('starts_at', '<', $end)
                        ->where('ends_at', '>', $start)
                        ->whereHas('therapists', fn ($query) => $query->whereKey($therapist->id))
                        ->exists();

                    return [
                        'id' => $therapist->id,
                        'name' => $therapist->name,
                        'available' => ! $overlap,
                        'reason' => $overlap ? 'Con otra cita' : null,
                    ];
                })->values();

                $availableCount = $therapistStates->where('available', true)->count();

                $slots->push([
                    'starts_at' => $start->format('Y-m-d\\TH:i'),
                    'ends_at' => $end->format('Y-m-d\\TH:i'),
                    'label' => $start->format('H:i').' – '.$end->format('H:i'),
                    'available_count' => $availableCount,
                    'required_therapists' => $therapy->required_therapists,
                    'selectable' => $availableCount >= $therapy->required_therapists,
                    'therapists' => $therapistStates->all(),
                ]);
            }
        }

        return [
            'date' => $day->toDateString(),
            'therapy' => [
                'id' => $therapy->id,
                'name' => $therapy->name,
                'duration_minutes' => $therapy->duration_minutes,
                'required_therapists' => $therapy->required_therapists,
            ],
            'center_windows' => $windows->map(fn ($window): array => [
                'opens_at' => substr((string) $window->opens_at, 0, 5),
                'closes_at' => substr((string) $window->closes_at, 0, 5),
            ])->all(),
            'slots' => $slots->all(),
        ];
    }
}
