<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AuditTrail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ForcedPasswordChangeController extends Controller
{
    public function edit(Request $request): View
    {
        return view('auth.change-password');
    }

    public function update(Request $request, AuditTrail $audit): RedirectResponse
    {
        $validated = $request->validate([
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        /** @var User $user */
        $user = $request->user();
        $user->forceFill([
            'password' => Hash::make($validated['password']),
            'must_change_password' => false,
            'remember_token' => null,
        ])->save();

        $request->session()->regenerate();
        $audit->record('user.password_changed', $user, [
            'forced_change_completed' => true,
        ], $user, $request);

        return redirect()->route('dashboard')->with('status', 'Contraseña actualizada correctamente.');
    }
}
