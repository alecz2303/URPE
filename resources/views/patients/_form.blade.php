<div class="grid gap-5 md:grid-cols-2">
    <div>
        <label for="first_name" class="text-sm font-semibold text-slate-700">Nombre *</label>
        <input id="first_name" name="first_name" value="{{ old('first_name', $patient->first_name ?? '') }}" required class="mt-2 block w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-slate-900 shadow-sm placeholder:text-slate-400 focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-600/20">
        @error('first_name')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="middle_name" class="text-sm font-semibold text-slate-700">Segundo nombre</label>
        <input id="middle_name" name="middle_name" value="{{ old('middle_name', $patient->middle_name ?? '') }}" class="mt-2 block w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-slate-900 shadow-sm placeholder:text-slate-400 focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-600/20">
        @error('middle_name')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="last_name" class="text-sm font-semibold text-slate-700">Apellido paterno *</label>
        <input id="last_name" name="last_name" value="{{ old('last_name', $patient->last_name ?? '') }}" required class="mt-2 block w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-slate-900 shadow-sm placeholder:text-slate-400 focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-600/20">
        @error('last_name')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="second_last_name" class="text-sm font-semibold text-slate-700">Apellido materno</label>
        <input id="second_last_name" name="second_last_name" value="{{ old('second_last_name', $patient->second_last_name ?? '') }}" class="mt-2 block w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-slate-900 shadow-sm placeholder:text-slate-400 focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-600/20">
        @error('second_last_name')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="date_of_birth" class="text-sm font-semibold text-slate-700">Fecha de nacimiento *</label>
        <input id="date_of_birth" type="date" name="date_of_birth" value="{{ old('date_of_birth', isset($patient) && $patient->date_of_birth ? $patient->date_of_birth->format('Y-m-d') : '') }}" required class="mt-2 block w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-slate-900 shadow-sm focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-600/20">
        @error('date_of_birth')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="sex" class="text-sm font-semibold text-slate-700">Sexo administrativo</label>
        <select id="sex" name="sex" class="mt-2 block w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-slate-900 shadow-sm focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-600/20">
            <option value="">No especificado</option>
            <option value="female" @selected(old('sex', $patient->sex ?? '') === 'female')>Femenino</option>
            <option value="male" @selected(old('sex', $patient->sex ?? '') === 'male')>Masculino</option>
            <option value="other" @selected(old('sex', $patient->sex ?? '') === 'other')>Otro</option>
            <option value="unspecified" @selected(old('sex', $patient->sex ?? '') === 'unspecified')>Prefiere no especificar</option>
        </select>
        @error('sex')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="phone" class="text-sm font-semibold text-slate-700">Teléfono</label>
        <input id="phone" name="phone" value="{{ old('phone', $patient->phone ?? '') }}" class="mt-2 block w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-slate-900 shadow-sm placeholder:text-slate-400 focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-600/20">
        @error('phone')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="email" class="text-sm font-semibold text-slate-700">Correo electrónico</label>
        <input id="email" type="email" name="email" value="{{ old('email', $patient->email ?? '') }}" class="mt-2 block w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-slate-900 shadow-sm placeholder:text-slate-400 focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-600/20">
        @error('email')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
    </div>

    <div class="md:col-span-2">
        <label for="address_line" class="text-sm font-semibold text-slate-700">Dirección</label>
        <textarea id="address_line" name="address_line" rows="2" class="mt-2 block w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-slate-900 shadow-sm placeholder:text-slate-400 focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-600/20">{{ old('address_line', $patient->address_line ?? '') }}</textarea>
        @error('address_line')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
    </div>

    <div class="md:col-span-2">
        <label for="administrative_notes" class="text-sm font-semibold text-slate-700">Notas administrativas</label>
        <textarea id="administrative_notes" name="administrative_notes" rows="4" class="mt-2 block w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-slate-900 shadow-sm placeholder:text-slate-400 focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-600/20">{{ old('administrative_notes', $patient->administrative_notes ?? '') }}</textarea>
        @error('administrative_notes')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
    </div>

    <div class="md:col-span-2">
        <label class="inline-flex items-center gap-3 text-sm font-semibold text-slate-700">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', isset($patient) ? $patient->is_active : true)) class="rounded border-slate-300 text-cyan-700 focus:ring-cyan-600">
            Paciente activo
        </label>
    </div>
</div>
