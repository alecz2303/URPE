<x-guest-layout>
    <div class="mb-6">
        <h1 class="text-2xl font-extrabold text-slate-900">Define una nueva contraseña</h1>
        <p class="mt-2 text-sm text-slate-600">La contraseña temporal solo sirve para recuperar el acceso. Antes de continuar en URPE debes reemplazarla.</p>
    </div>

    <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
        @csrf
        @method('PUT')

        <div>
            <label for="password" class="text-sm font-bold text-slate-700">Nueva contraseña</label>
            <input id="password" name="password" type="password" required autocomplete="new-password" class="mt-2 block w-full rounded-xl border-slate-300">
            @error('password') <p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="password_confirmation" class="text-sm font-bold text-slate-700">Confirmar nueva contraseña</label>
            <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password" class="mt-2 block w-full rounded-xl border-slate-300">
        </div>

        <button class="w-full rounded-xl bg-violet-600 px-4 py-3 text-sm font-extrabold text-white hover:bg-violet-700">Guardar nueva contraseña</button>
    </form>

    <form method="POST" action="{{ route('logout') }}" class="mt-4 text-center">
        @csrf
        <button class="text-sm font-bold text-slate-500 hover:text-slate-700">Cerrar sesión</button>
    </form>
</x-guest-layout>
