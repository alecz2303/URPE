<?php

namespace App\Http\Controllers;

use App\Models\Therapy;
use App\Services\AppointmentAvailabilityFinder;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class AppointmentAvailabilityController extends Controller
{
    public function __invoke(Request $request, AppointmentAvailabilityFinder $finder): JsonResponse
    {
        Gate::authorize('appointments.manage');

        $data = $request->validate([
            'therapy_id' => ['required', 'integer', 'exists:therapies,id'],
            'date' => ['required', 'date_format:Y-m-d'],
        ]);

        $therapy = Therapy::query()->findOrFail($data['therapy_id']);

        if (! $therapy->is_active) {
            abort(422, 'La terapia seleccionada está inactiva.');
        }

        return response()->json(
            $finder->forDate($therapy, CarbonImmutable::createFromFormat('Y-m-d', $data['date'])),
        );
    }
}
