@php
    $editing = isset($therapy);
@endphp

<div class="grid gap-6 md:grid-cols-2">
    <div class="md:col-span-2">
        <label for="name" class="block text-sm font-semibold text-slate-700">Nombre de la terapia</label>
        <input id="name" name="name" type="text" maxlength="120" required
               value="{{ old('name', $therapy->name ?? '') }}"
               class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm shadow-sm outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100">
        @error('name')<p class="mt-2 text-sm text-rose-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="duration_minutes" class="block text-sm font-semibold text-slate-700">Duración (minutos)</label>
        <input id="duration_minutes" name="duration_minutes" type="number" min="1" max="1440" required
               value="{{ old('duration_minutes', $therapy->duration_minutes ?? 40) }}"
               class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm shadow-sm outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100">
        @error('duration_minutes')<p class="mt-2 text-sm text-rose-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="required_therapists" class="block text-sm font-semibold text-slate-700">Terapeutas requeridos</label>
        <input id="required_therapists" name="required_therapists" type="number" min="1" max="50" required
               value="{{ old('required_therapists', $therapy->required_therapists ?? 1) }}"
               class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm shadow-sm outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100">
        @error('required_therapists')<p class="mt-2 text-sm text-rose-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="color" class="block text-sm font-semibold text-slate-700">Color de agenda</label>
        <div class="mt-2 flex items-center gap-3">
            <input id="color_picker" type="color" value="{{ old('color', $therapy->color ?? '#3B82F6') }}" class="h-12 w-16 rounded-lg border border-slate-300 bg-white p-1">
            <input id="color" name="color" type="text" maxlength="7" required
                   value="{{ old('color', $therapy->color ?? '#3B82F6') }}"
                   pattern="#[0-9A-Fa-f]{6}"
                   class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm uppercase shadow-sm outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100">
        </div>
        @error('color')<p class="mt-2 text-sm text-rose-600">{{ $message }}</p>@enderror
    </div>

    <div class="flex items-center">
        <label class="mt-7 inline-flex items-center gap-3">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1"
                   @checked(old('is_active', $therapy->is_active ?? true))
                   class="h-5 w-5 rounded border-slate-300 text-cyan-700 focus:ring-cyan-500">
            <span class="text-sm font-semibold text-slate-700">Disponible para nuevas citas</span>
        </label>
    </div>
</div>

<div class="mt-8 flex flex-wrap items-center gap-3">
    <button type="submit" class="rounded-xl bg-cyan-700 px-5 py-3 text-sm font-semibold text-white transition hover:bg-cyan-800 focus:outline-none focus:ring-4 focus:ring-cyan-200">
        {{ $editing ? 'Guardar cambios' : 'Crear terapia' }}
    </button>
    <a href="{{ route('therapies.index') }}" class="rounded-xl border border-slate-300 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Cancelar</a>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const picker = document.getElementById('color_picker');
        const input = document.getElementById('color');
        if (!picker || !input) return;

        picker.addEventListener('input', () => input.value = picker.value.toUpperCase());
        input.addEventListener('input', () => {
            const value = input.value.trim();
            if (/^#[0-9A-Fa-f]{6}$/.test(value)) picker.value = value;
        });
    });
</script>
