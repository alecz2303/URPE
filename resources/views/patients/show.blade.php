<x-app-shell title="{{ $patient->full_name }}" eyebrow="Ficha del paciente">
    <x-slot:actions>
        @can('clinical_records.view')
            <a href="{{ route('clinical-records.show', $patient) }}" class="rounded-xl border border-cyan-200 bg-cyan-50 px-4 py-2.5 text-sm font-bold text-cyan-800 hover:bg-cyan-100">Expediente clínico</a>
        @endcan
        @can('patients.manage')
            <a href="{{ route('patients.edit', $patient) }}" class="rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-bold text-white hover:bg-slate-800">Editar paciente</a>
        @endcan
    </x-slot:actions>

    <div class="mb-6 flex flex-wrap items-center gap-2">
        <span class="rounded-full bg-slate-100 px-3 py-1 font-mono text-xs font-bold text-slate-600">{{ $patient->folio }}</span>
        <span class="rounded-full px-3 py-1 text-xs font-bold {{ $patient->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">{{ $patient->is_active ? 'Activo' : 'Inactivo' }}</span>
        @if($patient->date_of_birth)
            <span class="rounded-full bg-cyan-50 px-3 py-1 text-xs font-bold text-cyan-800">{{ $patient->date_of_birth->age }} años</span>
        @endif
    </div>

    <div class="grid gap-6 xl:grid-cols-[minmax(0,1.05fr)_minmax(0,.95fr)]">
        <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between gap-4 border-b border-slate-100 pb-4">
                <div><p class="text-xs font-bold uppercase tracking-[0.16em] text-cyan-700">Datos generales</p><h2 class="mt-1 text-lg font-bold">Información del paciente</h2></div>
            </div>
            <dl class="mt-6 grid gap-x-6 gap-y-5 sm:grid-cols-2">
                <div><dt class="text-xs font-bold uppercase tracking-wide text-slate-400">Fecha de nacimiento</dt><dd class="mt-1 text-sm font-semibold text-slate-700">{{ $patient->date_of_birth?->format('d/m/Y') ?: '—' }}</dd></div>
                <div><dt class="text-xs font-bold uppercase tracking-wide text-slate-400">Sexo</dt><dd class="mt-1 text-sm font-semibold text-slate-700">{{ ['female' => 'Femenino', 'male' => 'Masculino', 'other' => 'Otro', 'unspecified' => 'No especificado'][$patient->sex] ?? 'No especificado' }}</dd></div>
                <div><dt class="text-xs font-bold uppercase tracking-wide text-slate-400">Teléfono</dt><dd class="mt-1 text-sm font-semibold text-slate-700">{{ $patient->phone ?: '—' }}</dd></div>
                <div><dt class="text-xs font-bold uppercase tracking-wide text-slate-400">Correo</dt><dd class="mt-1 break-all text-sm font-semibold text-slate-700">{{ $patient->email ?: '—' }}</dd></div>
                <div class="sm:col-span-2"><dt class="text-xs font-bold uppercase tracking-wide text-slate-400">Dirección</dt><dd class="mt-1 text-sm leading-6 text-slate-700">{{ $patient->address_line ?: '—' }}</dd></div>
                <div class="sm:col-span-2"><dt class="text-xs font-bold uppercase tracking-wide text-slate-400">Notas administrativas</dt><dd class="mt-1 whitespace-pre-line text-sm leading-6 text-slate-700">{{ $patient->administrative_notes ?: 'Sin notas administrativas.' }}</dd></div>
            </dl>
            @can('patients.manage')
                <div class="mt-6 border-t border-slate-100 pt-5">
                    <form method="POST" action="{{ route('patients.toggle-active', $patient) }}" data-confirm="¿Cambiar el estado de este paciente?">
                        @csrf
                        @method('PATCH')
                        <button class="text-sm font-bold {{ $patient->is_active ? 'text-rose-600' : 'text-emerald-700' }}">{{ $patient->is_active ? 'Desactivar paciente' : 'Activar paciente' }}</button>
                    </form>
                </div>
            @endcan
        </section>

        <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex items-start justify-between gap-4 border-b border-slate-100 pb-4">
                <div><p class="text-xs font-bold uppercase tracking-[0.16em] text-cyan-700">Responsables</p><h2 class="mt-1 text-lg font-bold">Contactos vinculados</h2></div>
                @can('patients.manage')
                    <a href="{{ route('patients.guardians.create', $patient) }}" class="text-sm font-bold text-cyan-700 hover:text-cyan-900">+ Agregar</a>
                @endcan
            </div>
            <div class="mt-5 space-y-3">
                @forelse($patient->guardians as $guardian)
                    <article class="rounded-2xl border border-slate-200 p-4">
                        <div class="flex items-start justify-between gap-4">
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h3 class="font-bold text-slate-800">{{ $guardian->full_name }}</h3>
                                    @if($guardian->pivot->is_primary)
                                        <span class="rounded-full bg-cyan-50 px-2 py-0.5 text-[11px] font-bold text-cyan-700">Principal</span>
                                    @endif
                                </div>
                                <p class="mt-1 text-sm text-slate-500">{{ $guardian->pivot->relationship ?: 'Relación no especificada' }}</p>
                                <p class="mt-3 text-sm font-semibold text-slate-700">{{ $guardian->phone }}</p>
                                @if($guardian->email)
                                    <p class="mt-1 text-sm text-slate-500">{{ $guardian->email }}</p>
                                @endif
                            </div>
                            @can('patients.manage')
                                <a href="{{ route('patients.guardians.edit', [$patient, $guardian]) }}" class="text-sm font-bold text-slate-500 hover:text-cyan-700">Editar</a>
                            @endcan
                        </div>
                        @can('patients.manage')
                            @unless($guardian->pivot->is_primary)
                                <form method="POST" action="{{ route('patients.guardians.primary', [$patient, $guardian]) }}" class="mt-4 border-t border-slate-100 pt-3" data-confirm="¿Marcar a este responsable como principal?">
                                    @csrf
                                    @method('PATCH')
                                    <button class="text-xs font-bold text-cyan-700">Marcar como principal</button>
                                </form>
                            @endunless
                        @endcan
                    </article>
                @empty
                    <p class="rounded-2xl bg-slate-50 px-4 py-6 text-sm text-slate-500">Aún no hay responsables vinculados.</p>
                @endforelse
            </div>
        </section>
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-2">
        <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex items-start justify-between gap-4">
                <div><p class="text-xs font-bold uppercase tracking-[0.16em] text-cyan-700">Expediente clínico</p><h2 class="mt-1 text-lg font-bold">Resumen clínico</h2></div>
                @can('clinical_records.view')
                    <a href="{{ route('clinical-records.show', $patient) }}" class="text-sm font-bold text-cyan-700">Abrir →</a>
                @endcan
            </div>
            @can('clinical_records.view')
                @if($patient->clinicalRecord)
                    <p class="mt-5 text-sm leading-6 text-slate-600">El expediente clínico base ya está registrado. Accede para consultar antecedentes, diagnósticos, objetivos y observaciones clínicas.</p>
                @else
                    <p class="mt-5 text-sm leading-6 text-slate-600">Este paciente todavía no cuenta con expediente clínico base capturado.</p>
                @endif
            @else
                <p class="mt-5 text-sm leading-6 text-slate-500">La información clínica se encuentra protegida según los permisos de tu cuenta.</p>
            @endcan
        </section>

        <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex items-start justify-between gap-4">
                <div><p class="text-xs font-bold uppercase tracking-[0.16em] text-cyan-700">Próximas citas</p><h2 class="mt-1 text-lg font-bold">Agenda del paciente</h2></div>
                @can('appointments.view')
                    <a href="{{ route('appointments.index', ['view' => 'week']) }}" class="text-sm font-bold text-cyan-700">Agenda →</a>
                @endcan
            </div>
            @can('appointments.view')
                <div class="mt-5 space-y-3">
                    @forelse($patient->appointments as $appointment)
                        <article class="relative rounded-2xl border border-slate-200 p-4 pl-5 {{ $appointment->isCancelled() ? 'opacity-55' : '' }}">
                            <span class="absolute inset-y-3 left-0 w-1 rounded-r-full" style="background-color: {{ $appointment->therapy->color ?: '#0891b2' }}"></span>
                            <div class="flex items-start justify-between gap-4">
                                <div><p class="text-sm font-bold">{{ $appointment->starts_at->translatedFormat('D d M · H:i') }}</p><p class="mt-1 text-sm font-semibold text-slate-600">{{ $appointment->therapy->name }}</p><p class="mt-1 text-xs text-slate-400">{{ $appointment->therapists->pluck('name')->implode(' · ') }}</p></div>
                                @if($appointment->isCancelled())
                                    <span class="rounded-full bg-rose-50 px-2 py-0.5 text-[11px] font-bold text-rose-700">Cancelada</span>
                                @endif
                            </div>
                        </article>
                    @empty
                        <p class="rounded-2xl bg-slate-50 px-4 py-6 text-sm text-slate-500">No hay próximas citas registradas.</p>
                    @endforelse
                </div>
            @else
                <p class="mt-5 text-sm text-slate-500">La agenda está disponible según los permisos de tu cuenta.</p>
            @endcan
        </section>
    </div>
</x-app-shell>
