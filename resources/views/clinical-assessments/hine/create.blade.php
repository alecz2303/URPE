<x-app-shell title="Nueva evaluación HINE" eyebrow="{{ $patient->full_name }}">
    <x-slot:actions>
        <a href="{{ route('patients.hine-assessments.index', $patient) }}" class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-700">Cancelar</a>
    </x-slot:actions>

    <form method="POST" action="{{ route('patients.hine-assessments.store', $patient) }}" class="mx-auto max-w-4xl space-y-6">
        @csrf
        <section class="overflow-hidden rounded-3xl border border-cyan-100 bg-white shadow-sm">
            <div class="border-b border-cyan-100 bg-gradient-to-r from-cyan-50 to-white px-6 py-5">
                <p class="text-xs font-bold uppercase tracking-[0.16em] text-cyan-700">HINE {{ $instrumentVersion }}</p>
                <h2 class="mt-1 text-lg font-bold">Datos del examen</h2>
                <p class="mt-2 text-sm text-slate-500">Esta acción crea un borrador. La puntuación neurológica se capturará después sin modificar evaluaciones anteriores.</p>
            </div>
            <div class="grid gap-5 p-6 sm:grid-cols-2">
                <label class="text-sm font-bold text-slate-700">Fecha de examen
                    <input type="date" name="examination_date" value="{{ old('examination_date', now()->toDateString()) }}" required class="mt-2 w-full rounded-xl border-slate-200">
                </label>
                <label class="text-sm font-bold text-slate-700">Edad gestacional
                    <input type="text" name="gestational_age" value="{{ old('gestational_age') }}" maxlength="100" class="mt-2 w-full rounded-xl border-slate-200">
                </label>
                <label class="text-sm font-bold text-slate-700">Edad cronológica
                    <input type="text" name="chronological_age" value="{{ old('chronological_age') }}" maxlength="100" class="mt-2 w-full rounded-xl border-slate-200">
                </label>
                <label class="text-sm font-bold text-slate-700">Edad corregida
                    <input type="text" name="corrected_age" value="{{ old('corrected_age') }}" maxlength="100" class="mt-2 w-full rounded-xl border-slate-200">
                </label>
                <label class="text-sm font-bold text-slate-700 sm:col-span-2">Perímetro cefálico
                    <input type="text" name="head_circumference" value="{{ old('head_circumference') }}" maxlength="100" class="mt-2 w-full rounded-xl border-slate-200">
                </label>
            </div>
        </section>
        @if($errors->any())
            <div class="rounded-2xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800">{{ $errors->first() }}</div>
        @endif
        <div class="flex justify-end">
            <button class="rounded-xl bg-gradient-to-r from-cyan-600 to-sky-600 px-5 py-3 text-sm font-bold text-white shadow-sm">Crear borrador HINE</button>
        </div>
    </form>
</x-app-shell>
