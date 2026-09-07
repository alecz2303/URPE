<?php

namespace App\Http\Controllers;

use App\Models\Therapy;
use Illuminate\View\View;

class TherapyShowController extends Controller
{
    public function __invoke(Therapy $therapy): View
    {
        $this->authorize('therapies.manage');

        $therapy->loadCount('appointments');

        return view('therapies.show', compact('therapy'));
    }
}
