<x-app-shell title="Pacientes" eyebrow="Operación clínica">
    <x-slot:actions>
        @can('patients.manage')<a href="{{ route('patients.create') }}" class="rounded-xl bg-cyan-700 px-4 py-2.5 text-sm font-bold text-white hover:bg-cyan-800">+ Nuevo paciente</a>@endcan
    </x-slot:actions>

    <div class="mb-6 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
        <div><p class="text-sm text-slate-500">Consulta rápida de pacientes, responsables y estado administrativo.</p></div>
        <p class="text-xs font-bold uppercase tracking-[0.16em] text-slate-400">{{ $patients->count() }} {{ $patients->count() === 1 ? 'paciente' : 'pacientes' }}</p>
    </div>

    <section class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
        <div class="divide-y divide-slate-100">
            @forelse($patients as $patient)
                @php($primaryGuardian = $patient->guardians->first(fn ($guardian) => (bool) $guardian->pivot->is_primary))
                <a href="{{ route('patients.show', $patient) }}" class="group grid gap-4 px-5 py-5 transition hover:bg-slate-50 sm:grid-cols-[minmax(0,1.2fr)_minmax(0,.9fr)_auto] sm:items-center lg:px-6">
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2"><h2 class="truncate text-base font-bold text-slate-900 group-hover:text-cyan-800">{{ $patient->full_name }}</h2><span class="rounded-full px-2 py-0.5 text-[11px] font-bold {{ $patient->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">{{ $patient->is_active ? 'Activo' : 'Inactivo' }}</span></div>
                        <p class="mt-1 font-mono text-xs text-slate-400">{{ $patient->folio }}</p>
                        <p class="mt-2 text-sm text-slate-500">{{ $patient->date_of_birth?->format('d/m/Y') }}@if($patient->date_of_birth) · {{ $patient->date_of_birth->age }} años @endif</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wide text-slate-400">Responsable principal</p>
                        @if($primaryGuardian)<p class="mt-1 text-sm font-bold text-slate-700">{{ $primaryGuardian->full_name }}</p><p class="mt-1 text-xs text-slate-500">{{ $primaryGuardian->pivot->relationship ?: 'Relación no especificada' }} · {{ $primaryGuardian->phone }}</p>@else<p class="mt-1 text-sm text-slate-400">Sin responsable principal</p>@endif
                    </div>
                    <div class="text-sm font-bold text-cyan-700">Ver ficha →</div>
                </a>
            @empty
                <div class="px-6 py-16 text-center"><div class="mx-auto grid h-12 w-12 place-items-center rounded-2xl bg-slate-100 text-xl text-slate-500">♙</div><h2 class="mt-4 font-bold">Aún no hay pacientes registrados</h2><p class="mt-1 text-sm text-slate-500">Crea el primer paciente para comenzar su seguimiento administrativo y clínico.</p>@can('patients.manage')<a href="{{ route('patients.create') }}" class="mt-4 inline-flex text-sm font-bold text-cyan-700">+ Nuevo paciente</a>@endcan</div>
            @endforelse
        </div>
    </section>
</x-app-shell>
