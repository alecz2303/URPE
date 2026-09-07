<?php

namespace App\Http\Controllers;

use App\Models\Therapist;
use Illuminate\View\View;

class TherapistShowController extends Controller
{
    public function __invoke(Therapist $therapist): View
    {
        $this->authorize('therapists.manage');

        $therapist->load([
            'user',
            'availabilityWindows' => fn ($query) => $query->orderBy('day_of_week')->orderBy('starts_at'),
            'blocks' => fn ($query) => $query->latest('starts_at')->limit(10),
            'appointments' => fn ($query) => $query->with(['patient', 'therapy'])->where('starts_at', '>=', now())->orderBy('starts_at')->limit(8),
        ]);

        return view('therapists.show', compact('therapist'));
    }
}
