<x-app-shell title="{{ $patient->full_name }}" eyebrow="Ficha del paciente">
    <x-slot:actions>
        @can('clinical_records.view')
            <a href="{{ route('clinical-records.show', $patient) }}" class="rounded-xl border border-violet-200 bg-violet-50 px-4 py-2.5 text-sm font-bold text-violet-800 shadow-sm hover:bg-violet-100">Expediente clínico</a>
        @endcan
        @can('patients.manage')
            <a href="{{ route('patients.edit', $patient) }}" class="rounded-xl bg-gradient-to-r from-cyan-600 to-sky-600 px-4 py-2.5 text-sm font-bold text-white shadow-sm hover:from-cyan-700 hover:to-sky-700">Editar paciente</a>
        @endcan
    </x-slot:actions>

    <section class="mb-6 overflow-hidden rounded-3xl border border-cyan-200 bg-gradient-to-r from-cyan-50 via-sky-50 to-fuchsia-50 shadow-sm">
        <div class="h-1.5 bg-gradient-to-r from-cyan-500 via-sky-400 to-fuchsia-400"></div>
        <div class="flex flex-col gap-5 px-6 py-6 sm:flex-row sm:items-center sm:justify-between sm:px-7">
            <div>
                <div class="flex flex-wrap items-center gap-2">
                    <span class="rounded-full bg-white/80 px-3 py-1 font-mono text-xs font-bold text-slate-600 ring-1 ring-white">{{ $patient->folio }}</span>
                    <span class="rounded-full px-3 py-1 text-xs font-bold {{ $patient->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-500' }}">{{ $patient->is_active ? 'Activo' : 'Inactivo' }}</span>
                    @if($patient->date_of_birth)
                        <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-bold text-amber-800">{{ $patient->date_of_birth->age }} años</span>
                    @endif
                </div>
                <p class="mt-3 text-sm leading-6 text-slate-600">Información administrativa, responsables, expediente clínico y próximas citas reunidos en una sola vista.</p>
            </div>
            <div class="grid grid-cols-2 gap-2 text-center sm:min-w-[220px]">
                <div class="rounded-2xl bg-white/80 p-3 ring-1 ring-white"><p class="text-[11px] font-bold uppercase tracking-wide text-cyan-700">Responsables</p><p class="mt-1 text-2xl font-black text-slate-900">{{ $patient->guardians->count() }}</p></div>
                <div class="rounded-2xl bg-white/80 p-3 ring-1 ring-white"><p class="text-[11px] font-bold uppercase tracking-wide text-fuchsia-700">Próximas citas</p><p class="mt-1 text-2xl font-black text-slate-900">{{ $patient->appointments->count() }}</p></div>
            </div>
        </div>
    </section>

    <div class="grid gap-6 xl:grid-cols-[minmax(0,1.05fr)_minmax(0,.95fr)]">
        <section class="overflow-hidden rounded-3xl border border-cyan-100 bg-white shadow-sm">
            <div class="border-b border-cyan-100 bg-gradient-to-r from-cyan-50 to-white px-6 py-4">
                <p class="text-xs font-bold uppercase tracking-[0.16em] text-cyan-700">Datos generales</p><h2 class="mt-1 text-lg font-bold">Información del paciente</h2>
            </div>
            <dl class="grid gap-x-6 gap-y-5 p-6 sm:grid-cols-2">
                <div class="rounded-2xl bg-sky-50/70 p-4"><dt class="text-xs font-bold uppercase tracking-wide text-sky-700">Fecha de nacimiento</dt><dd class="mt-1 text-sm font-semibold text-slate-700">{{ $patient->date_of_birth?->format('d/m/Y') ?: '—' }}</dd></div>
                <div class="rounded-2xl bg-fuchsia-50/70 p-4"><dt class="text-xs font-bold uppercase tracking-wide text-fuchsia-700">Sexo</dt><dd class="mt-1 text-sm font-semibold text-slate-700">{{ ['female' => 'Femenino', 'male' => 'Masculino', 'other' => 'Otro', 'unspecified' => 'No especificado'][$patient->sex] ?? 'No especificado' }}</dd></div>
                <div class="rounded-2xl bg-emerald-50/70 p-4"><dt class="text-xs font-bold uppercase tracking-wide text-emerald-700">Teléfono</dt><dd class="mt-1 text-sm font-semibold text-slate-700">{{ $patient->phone ?: '—' }}</dd></div>
                <div class="rounded-2xl bg-amber-50/70 p-4"><dt class="text-xs font-bold uppercase tracking-wide text-amber-700">Correo</dt><dd class="mt-1 break-all text-sm font-semibold text-slate-700">{{ $patient->email ?: '—' }}</dd></div>
                <div class="rounded-2xl bg-violet-50/70 p-4 sm:col-span-2"><dt class="text-xs font-bold uppercase tracking-wide text-violet-700">Dirección</dt><dd class="mt-1 text-sm leading-6 text-slate-700">{{ $patient->address_line ?: '—' }}</dd></div>
                <div class="rounded-2xl bg-rose-50/70 p-4 sm:col-span-2"><dt class="text-xs font-bold uppercase tracking-wide text-rose-700">Notas administrativas</dt><dd class="mt-1 whitespace-pre-line text-sm leading-6 text-slate-700">{{ $patient->administrative_notes ?: 'Sin notas administrativas.' }}</dd></div>
            </dl>
            @can('patients.manage')
                <div class="border-t border-cyan-100 px-6 py-5">
                    <form method="POST" action="{{ route('patients.toggle-active', $patient) }}" data-confirm="¿Cambiar el estado de este paciente?">
                        @csrf
                        @method('PATCH')
                        <button class="text-sm font-bold {{ $patient->is_active ? 'text-rose-600' : 'text-emerald-700' }}">{{ $patient->is_active ? 'Desactivar paciente' : 'Activar paciente' }}</button>
                    </form>
                </div>
            @endcan
        </section>

        <section class="overflow-hidden rounded-3xl border border-fuchsia-100 bg-white shadow-sm">
            <div class="flex items-start justify-between gap-4 border-b border-fuchsia-100 bg-gradient-to-r from-fuchsia-50 to-white px-6 py-4">
                <div><p class="text-xs font-bold uppercase tracking-[0.16em] text-fuchsia-700">Responsables</p><h2 class="mt-1 text-lg font-bold">Contactos vinculados</h2></div>
                @can('patients.manage')
                    <a href="{{ route('patients.guardians.create', $patient) }}" class="rounded-xl bg-fuchsia-600 px-3 py-2 text-sm font-bold text-white shadow-sm hover:bg-fuchsia-700">+ Agregar</a>
                @endcan
            </div>
            <div class="space-y-3 p-6">
                @forelse($patient->guardians as $guardian)
                    <article class="rounded-2xl border border-fuchsia-100 bg-fuchsia-50/40 p-4">
                        <div class="flex items-start justify-between gap-4">
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h3 class="font-bold text-slate-800">{{ $guardian->full_name }}</h3>
                                    @if($guardian->pivot->is_primary)
                                        <span class="rounded-full bg-amber-100 px-2 py-0.5 text-[11px] font-bold text-amber-800">Principal</span>
                                    @endif
                                </div>
                                <p class="mt-1 text-sm text-slate-500">{{ $guardian->pivot->relationship ?: 'Relación no especificada' }}</p>
                                <p class="mt-3 text-sm font-semibold text-slate-700">{{ $guardian->phone }}</p>
                                @if($guardian->email)
                                    <p class="mt-1 text-sm text-slate-500">{{ $guardian->email }}</p>
                                @endif
                            </div>
                            @can('patients.manage')
                                <a href="{{ route('patients.guardians.edit', [$patient, $guardian]) }}" class="text-sm font-bold text-fuchsia-700 hover:text-fuchsia-900">Editar</a>
                            @endcan
                        </div>
                        @can('patients.manage')
                            @unless($guardian->pivot->is_primary)
                                <form method="POST" action="{{ route('patients.guardians.primary', [$patient, $guardian]) }}" class="mt-4 border-t border-fuchsia-100 pt-3" data-confirm="¿Marcar a este responsable como principal?">
                                    @csrf
                                    @method('PATCH')
                                    <button class="text-xs font-bold text-fuchsia-700">Marcar como principal</button>
                                </form>
                            @endunless
                        @endcan
                    </article>
                @empty
                    <p class="rounded-2xl bg-fuchsia-50 px-4 py-6 text-sm text-fuchsia-700">Aún no hay responsables vinculados.</p>
                @endforelse
            </div>
        </section>
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-2">
        <section class="overflow-hidden rounded-3xl border border-violet-100 bg-white shadow-sm">
            <div class="flex items-start justify-between gap-4 border-b border-violet-100 bg-gradient-to-r from-violet-50 to-white px-6 py-4">
                <div><p class="text-xs font-bold uppercase tracking-[0.16em] text-violet-700">Expediente clínico</p><h2 class="mt-1 text-lg font-bold">Resumen clínico</h2></div>
                @can('clinical_records.view')
                    <a href="{{ route('clinical-records.show', $patient) }}" class="text-sm font-bold text-violet-700">Abrir →</a>
                @endcan
            </div>
            <div class="p-6">
                @can('clinical_records.view')
                    @if($patient->clinicalRecord)
                        <div class="rounded-2xl bg-violet-50 p-5"><p class="text-sm leading-6 text-violet-900">El expediente clínico base ya está registrado. Accede para consultar antecedentes, diagnósticos, objetivos y observaciones clínicas.</p></div>
                    @else
                        <div class="rounded-2xl bg-amber-50 p-5"><p class="text-sm leading-6 text-amber-900">Este paciente todavía no cuenta con expediente clínico base capturado.</p></div>
                    @endif
                @else
                    <p class="text-sm leading-6 text-slate-500">La información clínica se encuentra protegida según los permisos de tu cuenta.</p>
                @endcan
            </div>
        </section>

        <section class="overflow-hidden rounded-3xl border border-emerald-100 bg-white shadow-sm">
            <div class="flex items-start justify-between gap-4 border-b border-emerald-100 bg-gradient-to-r from-emerald-50 to-white px-6 py-4">
                <div><p class="text-xs font-bold uppercase tracking-[0.16em] text-emerald-700">Próximas citas</p><h2 class="mt-1 text-lg font-bold">Agenda del paciente</h2></div>
                @can('appointments.view')
                    <a href="{{ route('appointments.index', ['view' => 'week']) }}" class="text-sm font-bold text-emerald-700">Agenda →</a>
                @endcan
            </div>
            @can('appointments.view')
                <div class="space-y-3 p-6">
                    @forelse($patient->appointments as $appointment)
                        <article class="relative rounded-2xl border border-emerald-100 bg-emerald-50/35 p-4 pl-5 {{ $appointment->isCancelled() ? 'opacity-55' : '' }}">
                            <span class="absolute inset-y-3 left-0 w-1 rounded-r-full" style="background-color: {{ $appointment->therapy->color ?: '#0891b2' }}"></span>
                            <div class="flex items-start justify-between gap-4">
                                <div><p class="text-sm font-bold">{{ $appointment->starts_at->translatedFormat('D d M · H:i') }}</p><p class="mt-1 text-sm font-semibold text-slate-600">{{ $appointment->therapy->name }}</p><p class="mt-1 text-xs text-slate-400">{{ $appointment->therapists->pluck('name')->implode(' · ') }}</p></div>
                                @if($appointment->isCancelled())
                                    <span class="rounded-full bg-rose-50 px-2 py-0.5 text-[11px] font-bold text-rose-700">Cancelada</span>
                                @endif
                            </div>
                        </article>
                    @empty
                        <p class="rounded-2xl bg-emerald-50 px-4 py-6 text-sm text-emerald-700">No hay próximas citas registradas.</p>
                    @endforelse
                </div>
            @else
                <p class="p-6 text-sm text-slate-500">La agenda está disponible según los permisos de tu cuenta.</p>
            @endcan
        </section>
    </div>
</x-app-shell>
