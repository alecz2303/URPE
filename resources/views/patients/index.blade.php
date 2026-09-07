<x-app-shell title="Pacientes" eyebrow="Operación clínica">
    <x-slot:actions>
        @can('patients.manage')
            <a href="{{ route('patients.create') }}" class="rounded-xl bg-gradient-to-r from-emerald-500 to-teal-500 px-4 py-2.5 text-sm font-bold text-white shadow-sm hover:from-emerald-600 hover:to-teal-600">+ Nuevo paciente</a>
        @endcan
    </x-slot:actions>

    <div class="mb-6 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
        <p class="text-sm font-medium text-slate-500">Consulta rápida de pacientes, responsables y estado administrativo.</p>
        <p class="w-fit rounded-full bg-cyan-100 px-3 py-1 text-xs font-bold uppercase tracking-[0.12em] text-cyan-700">{{ $patients->count() }} {{ $patients->count() === 1 ? 'paciente' : 'pacientes' }}</p>
    </div>

    <section class="overflow-hidden rounded-3xl border border-cyan-100 bg-white shadow-sm">
        <div class="divide-y divide-cyan-50">
            @forelse($patients as $patient)
                @php($primaryGuardian = $patient->guardians->first(fn ($guardian) => (bool) $guardian->pivot->is_primary))
                <a href="{{ route('patients.show', $patient) }}" class="group relative grid gap-4 px-5 py-5 transition hover:bg-gradient-to-r hover:from-cyan-50/70 hover:to-pink-50/40 sm:grid-cols-[minmax(0,1.2fr)_minmax(0,.9fr)_auto] sm:items-center lg:px-6">
                    <span class="absolute inset-y-4 left-0 w-1 rounded-r-full {{ $patient->is_active ? 'bg-emerald-400' : 'bg-slate-300' }}"></span>
                    <div class="min-w-0 pl-2">
                        <div class="flex flex-wrap items-center gap-2">
                            <h2 class="truncate text-base font-extrabold text-slate-900 group-hover:text-cyan-800">{{ $patient->full_name }}</h2>
                            <span class="rounded-full px-2.5 py-1 text-[11px] font-bold {{ $patient->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">{{ $patient->is_active ? 'Activo' : 'Inactivo' }}</span>
                        </div>
                        <p class="mt-1 w-fit rounded-full bg-sky-50 px-2 py-0.5 font-mono text-xs font-bold text-sky-600">{{ $patient->folio }}</p>
                        <p class="mt-2 text-sm font-medium text-slate-500">{{ $patient->date_of_birth?->format('d/m/Y') }}@if($patient->date_of_birth) · {{ $patient->date_of_birth->age }} años @endif</p>
                    </div>
                    <div class="rounded-2xl bg-violet-50/70 px-4 py-3">
                        <p class="text-xs font-extrabold uppercase tracking-wide text-violet-500">Responsable principal</p>
                        @if($primaryGuardian)
                            <p class="mt-1 text-sm font-bold text-slate-700">{{ $primaryGuardian->full_name }}</p>
                            <p class="mt-1 text-xs font-medium text-slate-500">{{ $primaryGuardian->pivot->relationship ?: 'Relación no especificada' }} · {{ $primaryGuardian->phone }}</p>
                        @else
                            <p class="mt-1 text-sm text-slate-400">Sin responsable principal</p>
                        @endif
                    </div>
                    <div class="rounded-xl bg-cyan-50 px-3 py-2 text-sm font-bold text-cyan-700 transition group-hover:bg-cyan-600 group-hover:text-white">Ver ficha →</div>
                </a>
            @empty
                <div class="bg-gradient-to-br from-white via-cyan-50/40 to-pink-50/40 px-6 py-16 text-center">
                    <div class="mx-auto grid h-12 w-12 place-items-center rounded-2xl bg-cyan-100 text-xl text-cyan-600">♡</div>
                    <h2 class="mt-4 font-bold">Aún no hay pacientes registrados</h2>
                    <p class="mt-1 text-sm text-slate-500">Crea el primer paciente para comenzar su seguimiento administrativo y clínico.</p>
                    @can('patients.manage')
                        <a href="{{ route('patients.create') }}" class="mt-4 inline-flex rounded-xl bg-emerald-500 px-4 py-2 text-sm font-bold text-white">+ Nuevo paciente</a>
                    @endcan
                </div>
            @endforelse
        </div>
    </section>
</x-app-shell>
