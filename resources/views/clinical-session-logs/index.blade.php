<x-app-shell title="Sesiones clínicas" eyebrow="Operación clínica">
    <section class="mb-6 overflow-hidden rounded-3xl border border-violet-100 bg-gradient-to-r from-violet-50 via-white to-cyan-50 p-5 shadow-sm sm:p-6">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-xs font-extrabold uppercase tracking-[0.16em] text-violet-600">Atención terapéutica</p>
                <h2 class="mt-1 text-xl font-extrabold text-slate-950">Historial y sesiones en curso</h2>
                <p class="mt-2 max-w-2xl text-sm text-slate-500">Localiza borradores, continúa capturas pendientes y consulta sesiones completadas sin depender de la Agenda.</p>
            </div>
            <a href="{{ route('appointments.index', ['view' => 'day', 'date' => now()->toDateString()]) }}" class="rounded-xl border border-cyan-200 bg-white px-4 py-2.5 text-sm font-bold text-cyan-800 shadow-sm hover:bg-cyan-50">Ir a Agenda de hoy</a>
        </div>
    </section>

    <form method="GET" action="{{ route('session-logs.index') }}" class="mb-6 rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        <div class="mb-4 flex items-center justify-between gap-3">
            <div>
                <p class="text-xs font-extrabold uppercase tracking-[0.14em] text-slate-400">Filtros</p>
                <h3 class="mt-1 font-extrabold text-slate-900">Buscar sesiones clínicas</h3>
            </div>
            @if(collect($filters)->filter()->isNotEmpty())
                <a href="{{ route('session-logs.index') }}" class="text-sm font-bold text-violet-700 hover:text-violet-900">Limpiar filtros</a>
            @endif
        </div>

        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-6">
            <label class="block xl:col-span-2">
                <span class="text-xs font-bold text-slate-600">Paciente</span>
                <input type="search" name="search" value="{{ $filters['search'] }}" placeholder="Nombre del paciente..." class="mt-1.5 w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm focus:border-violet-500 focus:ring-violet-500">
            </label>
            <label class="block">
                <span class="text-xs font-bold text-slate-600">Desde</span>
                <input type="date" name="date_from" value="{{ $filters['dateFrom'] }}" class="mt-1.5 w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm focus:border-violet-500 focus:ring-violet-500">
            </label>
            <label class="block">
                <span class="text-xs font-bold text-slate-600">Hasta</span>
                <input type="date" name="date_to" value="{{ $filters['dateTo'] }}" class="mt-1.5 w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm focus:border-violet-500 focus:ring-violet-500">
            </label>
            <label class="block">
                <span class="text-xs font-bold text-slate-600">Estado</span>
                <select name="status" class="mt-1.5 w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm focus:border-violet-500 focus:ring-violet-500">
                    <option value="">Todos</option>
                    <option value="draft" @selected($filters['status'] === 'draft')>Borrador</option>
                    <option value="completed" @selected($filters['status'] === 'completed')>Completada</option>
                </select>
            </label>
            <div class="flex items-end">
                <button class="w-full rounded-xl bg-gradient-to-r from-violet-600 to-fuchsia-600 px-4 py-2.5 text-sm font-bold text-white shadow-sm hover:from-violet-700 hover:to-fuchsia-700">Aplicar filtros</button>
            </div>
        </div>

        <div class="mt-4 grid gap-4 md:grid-cols-2">
            <label class="block">
                <span class="text-xs font-bold text-slate-600">Terapia</span>
                <select name="therapy_id" class="mt-1.5 w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm focus:border-cyan-500 focus:ring-cyan-500">
                    <option value="">Todas las terapias</option>
                    @foreach($therapies as $therapy)
                        <option value="{{ $therapy->id }}" @selected($filters['therapyId'] === $therapy->id)>{{ $therapy->name }}</option>
                    @endforeach
                </select>
            </label>
            <label class="block">
                <span class="text-xs font-bold text-slate-600">Terapeuta</span>
                <select name="therapist_id" class="mt-1.5 w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm focus:border-cyan-500 focus:ring-cyan-500">
                    <option value="">Todos los terapeutas</option>
                    @foreach($therapists as $therapist)
                        <option value="{{ $therapist->id }}" @selected($filters['therapistId'] === $therapist->id)>{{ $therapist->name }}</option>
                    @endforeach
                </select>
            </label>
        </div>
    </form>

    <section class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
        <div class="hidden overflow-x-auto md:block">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50/80">
                    <tr class="text-left text-xs font-extrabold uppercase tracking-wide text-slate-500">
                        <th class="px-5 py-3">Fecha</th>
                        <th class="px-5 py-3">Paciente</th>
                        <th class="px-5 py-3">Terapia</th>
                        <th class="px-5 py-3">Terapeutas</th>
                        <th class="px-5 py-3">Estado</th>
                        <th class="px-5 py-3 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($sessions as $session)
                        @php
                            $appointment = $session->appointment;
                            $assigned = $appointment?->therapists?->pluck('name')->all() ?? [];
                            $participants = $session->participatingTherapists->pluck('name')->all();
                            $therapistNames = ! empty($participants) ? $participants : $assigned;
                        @endphp
                        <tr class="{{ $session->isCompleted() ? '' : 'bg-amber-50/25' }}">
                            <td class="whitespace-nowrap px-5 py-4">
                                <p class="text-sm font-extrabold text-slate-900">{{ $appointment?->starts_at?->format('d/m/Y') }}</p>
                                <p class="mt-0.5 text-xs text-slate-500">{{ $appointment?->starts_at?->format('H:i') }}</p>
                            </td>
                            <td class="px-5 py-4">
                                <p class="font-bold text-slate-900">{{ $session->patient->full_name }}</p>
                            </td>
                            <td class="px-5 py-4">
                                <p class="text-sm font-bold" style="color: {{ $session->therapy->color ?: '#0891b2' }}">{{ $session->therapy->name }}</p>
                            </td>
                            <td class="px-5 py-4">
                                <p class="max-w-xs text-sm text-slate-600">{{ implode(' · ', $therapistNames) ?: 'Sin registro' }}</p>
                            </td>
                            <td class="px-5 py-4">
                                @if($session->isCompleted())
                                    <span class="inline-flex rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-bold text-emerald-700 ring-1 ring-emerald-100">Completada</span>
                                @else
                                    <span class="inline-flex rounded-full bg-amber-50 px-2.5 py-1 text-xs font-bold text-amber-700 ring-1 ring-amber-100">Borrador</span>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex flex-wrap justify-end gap-2">
                                    @if($session->isCompleted())
                                        <a href="{{ route('session-logs.show', $appointment) }}" class="rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-1.5 text-xs font-bold text-emerald-700 hover:bg-emerald-100">Ver bitácora</a>
                                    @else
                                        <a href="{{ route('session-logs.edit', $appointment) }}" class="rounded-lg bg-violet-600 px-3 py-1.5 text-xs font-bold text-white hover:bg-violet-700">Continuar captura</a>
                                    @endif
                                    <a href="{{ route('appointments.index', ['view' => 'day', 'date' => $appointment->starts_at->toDateString()]) }}" class="rounded-lg border border-cyan-200 bg-white px-3 py-1.5 text-xs font-bold text-cyan-700 hover:bg-cyan-50">Ver en agenda</a>
                                    <a href="{{ route('patients.show', $session->patient) }}" class="rounded-lg px-2 py-1.5 text-xs font-bold text-slate-500 hover:bg-slate-100 hover:text-slate-900">Paciente</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-6 py-14 text-center text-sm text-slate-500">No hay sesiones clínicas que coincidan con los filtros.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="space-y-3 p-4 md:hidden">
            @forelse($sessions as $session)
                @php($appointment = $session->appointment)
                <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <div class="h-1 {{ $session->isCompleted() ? 'bg-emerald-500' : 'bg-amber-400' }}"></div>
                    <div class="p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="font-extrabold text-slate-900">{{ $session->patient->full_name }}</p>
                                <p class="mt-1 text-sm font-bold" style="color: {{ $session->therapy->color ?: '#0891b2' }}">{{ $session->therapy->name }}</p>
                                <p class="mt-1 text-xs text-slate-500">{{ $appointment?->starts_at?->translatedFormat('d M Y · H:i') }}</p>
                            </div>
                            <span class="rounded-full px-2.5 py-1 text-xs font-bold {{ $session->isCompleted() ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">{{ $session->isCompleted() ? 'Completada' : 'Borrador' }}</span>
                        </div>
                        <div class="mt-4 flex flex-wrap gap-2">
                            @if($session->isCompleted())
                                <a href="{{ route('session-logs.show', $appointment) }}" class="rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-xs font-bold text-emerald-700">Ver bitácora</a>
                            @else
                                <a href="{{ route('session-logs.edit', $appointment) }}" class="rounded-lg bg-violet-600 px-3 py-2 text-xs font-bold text-white">Continuar captura</a>
                            @endif
                            <a href="{{ route('patients.show', $session->patient) }}" class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-bold text-slate-600">Paciente</a>
                        </div>
                    </div>
                </article>
            @empty
                <div class="rounded-2xl bg-slate-50 py-12 text-center text-sm text-slate-500">No hay sesiones clínicas que coincidan con los filtros.</div>
            @endforelse
        </div>

        @if($sessions->hasPages())
            <div class="border-t border-slate-100 px-5 py-4">{{ $sessions->links() }}</div>
        @endif
    </section>
</x-app-shell>
