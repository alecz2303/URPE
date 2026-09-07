@php
    $relationValue = old('relationship', $linkedGuardian?->pivot?->relationship ?? '');
    $isPrimary = old('is_primary', (bool) ($linkedGuardian?->pivot?->is_primary ?? false));
@endphp
<div class="grid gap-5 md:grid-cols-2">
    <div><label class="mb-2 block text-sm font-semibold text-slate-700">Nombre *</label><input name="first_name" value="{{ old('first_name', $guardian?->first_name) }}" required class="w-full">@error('first_name')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror</div>
    <div><label class="mb-2 block text-sm font-semibold text-slate-700">Segundo nombre</label><input name="middle_name" value="{{ old('middle_name', $guardian?->middle_name) }}" class="w-full"></div>
    <div><label class="mb-2 block text-sm font-semibold text-slate-700">Apellido paterno *</label><input name="last_name" value="{{ old('last_name', $guardian?->last_name) }}" required class="w-full">@error('last_name')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror</div>
    <div><label class="mb-2 block text-sm font-semibold text-slate-700">Apellido materno</label><input name="second_last_name" value="{{ old('second_last_name', $guardian?->second_last_name) }}" class="w-full"></div>
    <div><label class="mb-2 block text-sm font-semibold text-slate-700">Teléfono principal *</label><input name="phone" value="{{ old('phone', $guardian?->phone) }}" required class="w-full">@error('phone')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror</div>
    <div><label class="mb-2 block text-sm font-semibold text-slate-700">Teléfono secundario</label><input name="secondary_phone" value="{{ old('secondary_phone', $guardian?->secondary_phone) }}" class="w-full"></div>
    <div><label class="mb-2 block text-sm font-semibold text-slate-700">Correo electrónico</label><input type="email" name="email" value="{{ old('email', $guardian?->email) }}" class="w-full">@error('email')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror</div>
    <div><label class="mb-2 block text-sm font-semibold text-slate-700">Parentesco o relación</label><input name="relationship" value="{{ $relationValue }}" class="w-full"></div>
    <div class="md:col-span-2 rounded-2xl bg-slate-50 px-4 py-4"><input type="hidden" name="is_primary" value="0"><label class="inline-flex items-center gap-3 text-sm font-semibold text-slate-700"><input type="checkbox" name="is_primary" value="1" @checked($isPrimary)> Responsable principal</label><p class="mt-1 pl-7 text-xs text-slate-500">Solo puede existir un responsable principal por paciente.</p></div>
    <div class="md:col-span-2"><label class="mb-2 block text-sm font-semibold text-slate-700">Notas administrativas</label><textarea name="administrative_notes" rows="4" class="w-full">{{ old('administrative_notes', $guardian?->administrative_notes) }}</textarea></div>
</div>
