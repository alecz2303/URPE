<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\AuditTrail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserPasswordResetController extends Controller
{
    public function __invoke(Request $request, User $user, AuditTrail $audit): RedirectResponse
    {
        $this->authorize('users.update');

        if ($request->user()->is($user)) {
            return redirect()->route('users.index')->with('error', 'Para cambiar tu propia contraseña utiliza el flujo de cambio de contraseña.');
        }

        $temporaryPassword = Str::password(16, true, true, true, false);

        $user->forceFill([
            'password' => Hash::make($temporaryPassword),
            'must_change_password' => true,
            'remember_token' => Str::random(60),
        ])->save();

        $audit->record('user.password_reset', $user, [
            'forced_change' => true,
        ], $request->user(), $request);

        return redirect()->route('users.index')
            ->with('status', 'Contraseña temporal generada. Entrégala al usuario por un canal seguro.')
            ->with('temporary_password', $temporaryPassword)
            ->with('temporary_password_user_id', $user->id);
    }
}
