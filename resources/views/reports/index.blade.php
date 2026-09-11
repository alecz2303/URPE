<x-app-shell title="Reportes" eyebrow="Operación y seguimiento">
    <section class="mb-6 rounded-3xl border border-cyan-100 bg-white p-5 shadow-sm">
        <div class="mb-4 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-extrabold uppercase tracking-[0.14em] text-cyan-700">Filtros</p>
                <h2 class="mt-1 text-lg font-extrabold text-slate-950">Actividad del periodo</h2>
                <p class="mt-1 text-sm text-slate-500">Los indicadores y listados usan exactamente el mismo conjunto de filtros.</p>
            </div>
            <a href="{{ route('reports.index') }}" class="text-sm font-bold text-cyan-700 hover:text-cyan-900">Restablecer periodo</a>
        </div>

        <form method="GET" action="{{ route('reports.index') }}" class="grid gap-4 md:grid-cols-2 xl:grid-cols-6">
            <label class="text-sm font-semibold text-slate-700">Desde
                <input type="date" name="date_from" value="{{ $filters['dateFrom'] }}" class="mt-1 w-full rounded-xl border-slate-200 text-sm focus:border-cyan-400 focus:ring-cyan-400">
            </label>
            <label class="text-sm font-semibold text-slate-700">Hasta
                <input type="date" name="date_to" value="{{ $filters['dateTo'] }}" class="mt-1 w-full rounded-xl border-slate-200 text-sm focus:border-cyan-400 focus:ring-cyan-400">
            </label>
            <label class="text-sm font-semibold text-slate-700">Estado
                <select name="status" class="mt-1 w-full rounded-xl border-slate-200 text-sm focus:border-cyan-400 focus:ring-cyan-400">
                    <option value="">Todos</option>
                    @foreach($statuses as $value => $label)
                        <option value="{{ $value }}" @selected($filters['status'] === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </label>
            <label class="text-sm font-semibold text-slate-700">Terapia
                <select name="therapy_id" class="mt-1 w-full rounded-xl border-slate-200 text-sm focus:border-cyan-400 focus:ring-cyan-400">
                    <option value="0">Todas</option>
                    @foreach($therapies as $therapy)
                        <option value="{{ $therapy->id }}" @selected($filters['therapyId'] === $therapy->id)>{{ $therapy->name }}</option>
                    @endforeach
                </select>
            </label>
            <label class="text-sm font-semibold text-slate-700">Terapeuta
                <select name="therapist_id" class="mt-1 w-full rounded-xl border-slate-200 text-sm focus:border-cyan-400 focus:ring-cyan-400">
                    <option value="0">Todos</option>
                    @foreach($therapists as $therapist)
                        <option value="{{ $therapist->id }}" @selected($filters['therapistId'] === $therapist->id)>{{ $therapist->name }}</option>
                    @endforeach
                </select>
            </label>
            <label class="text-sm font-semibold text-slate-700">Paciente
                <select name="patient_id" class="mt-1 w-full rounded-xl border-slate-200 text-sm focus:border-cyan-400 focus:ring-cyan-400">
                    <option value="0">Todos</option>
                    @foreach($patients as $patient)
                        <option value="{{ $patient->id }}" @selected($filters['patientId'] === $patient->id)>{{ $patient->full_name }}</option>
                    @endforeach
                </select>
            </label>
            <div class="md:col-span-2 xl:col-span-6 flex justify-end">
                <button class="rounded-xl bg-gradient-to-r from-cyan-500 to-sky-500 px-5 py-2.5 text-sm font-extrabold text-white shadow-sm hover:from-cyan-600 hover:to-sky-600">Aplicar filtros</button>
            </div>
        </form>
    </section>

    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
        <article class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-extrabold uppercase tracking-[0.14em] text-slate-400">Citas</p>
            <p class="mt-2 text-3xl font-black text-slate-950">{{ $totalAppointments }}</p>
            <p class="mt-1 text-sm text-slate-500">Total del periodo filtrado</p>
        </article>
        <article class="rounded-3xl border border-emerald-100 bg-emerald-50/60 p-5 shadow-sm">
            <p class="text-xs font-extrabold uppercase tracking-[0.14em] text-emerald-700">Completadas</p>
            <p class="mt-2 text-3xl font-black text-emerald-950">{{ $completedAppointments }}</p>
            <p class="mt-1 text-sm text-emerald-800/70">Atenciones cerradas</p>
        </article>
        <article class="rounded-3xl border border-amber-100 bg-amber-50/60 p-5 shadow-sm">
            <p class="text-xs font-extrabold uppercase tracking-[0.14em] text-amber-700">No asistió</p>
            <p class="mt-2 text-3xl font-black text-amber-950">{{ $noShowAppointments }}</p>
            <p class="mt-1 text-sm text-amber-800/70">Ausencias registradas</p>
        </article>
        <article class="rounded-3xl border border-rose-100 bg-rose-50/60 p-5 shadow-sm">
            <p class="text-xs font-extrabold uppercase tracking-[0.14em] text-rose-700">Canceladas</p>
            <p class="mt-2 text-3xl font-black text-rose-950">{{ $cancelledAppointments }}</p>
            <p class="mt-1 text-sm text-rose-800/70">Citas canceladas</p>
        </article>
        <article class="rounded-3xl border border-violet-100 bg-violet-50/60 p-5 shadow-sm">
            <p class="text-xs font-extrabold uppercase tracking-[0.14em] text-violet-700">Asistencia efectiva</p>
            <p class="mt-2 text-3xl font-black text-violet-950">{{ $attendanceRate === null ? '—' : number_format($attendanceRate, 1).'%' }}</p>
            <p class="mt-1 text-sm text-violet-800/70">Completadas / (completadas + no asistió)</p>
        </article>
    </section>

    <section class="mt-6 rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="mb-4">
            <p class="text-xs font-extrabold uppercase tracking-[0.14em] text-slate-400">Distribución</p>
            <h2 class="mt-1 text-lg font-extrabold text-slate-950">Estados operativos</h2>
        </div>
        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6">
            @foreach($statuses as $value => $label)
                <div class="rounded-2xl border border-slate-100 bg-slate-50 p-4">
                    <p class="text-sm font-bold text-slate-600">{{ $label }}</p>
                    <p class="mt-1 text-2xl font-black text-slate-950">{{ $statusCounts[$value] ?? 0 }}</p>
                </div>
            @endforeach
        </div>
    </section>

    <section class="mt-6 overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 px-5 py-4">
            <p class="text-xs font-extrabold uppercase tracking-[0.14em] text-cyan-700">Operación</p>
            <h2 class="mt-1 text-lg font-extrabold text-slate-950">Citas del periodo</h2>
        </div>
        @if($appointments->isEmpty())
            <div class="p-10 text-center text-sm font-semibold text-slate-500">No hay citas que coincidan con los filtros seleccionados.</div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-sm">
                    <thead class="bg-slate-50 text-left text-xs font-extrabold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-5 py-3">Fecha y hora</th>
                        <th class="px-5 py-3">Paciente</th>
                        <th class="px-5 py-3">Terapia</th>
                        <th class="px-5 py-3">Terapeutas</th>
                        <th class="px-5 py-3">Estado</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                    @foreach($appointments as $appointment)
                        <tr>
                            <td class="whitespace-nowrap px-5 py-4 font-semibold text-slate-800">{{ $appointment->starts_at->format('d/m/Y H:i') }}</td>
                            <td class="px-5 py-4 text-slate-800">{{ $appointment->patient->full_name }}</td>
                            <td class="px-5 py-4 text-slate-700">{{ $appointment->therapy->name }}</td>
                            <td class="px-5 py-4 text-slate-600">{{ $appointment->therapists->pluck('name')->join(', ') }}</td>
                            <td class="px-5 py-4"><span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-extrabold text-slate-700">{{ $appointment->statusLabel() }}</span></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="border-t border-slate-100 px-5 py-4">{{ $appointments->links() }}</div>
        @endif
    </section>

    <section class="mt-6 overflow-hidden rounded-3xl border border-violet-100 bg-white shadow-sm">
        <div class="border-b border-violet-100 bg-gradient-to-r from-violet-50 via-fuchsia-50 to-cyan-50 px-5 py-4">
            <p class="text-xs font-extrabold uppercase tracking-[0.14em] text-violet-700">Actividad clínica</p>
            <h2 class="mt-1 text-lg font-extrabold text-slate-950">Sesiones completadas: {{ $completedSessionsCount }}</h2>
            <p class="mt-1 text-sm text-slate-500">Este reporte muestra únicamente metadatos operativos. No expone notas, evolución, recomendaciones ni contenido de la bitácora.</p>
        </div>
        @if($sessions->isEmpty())
            <div class="p-10 text-center text-sm font-semibold text-slate-500">No hay sesiones completadas que coincidan con los filtros.</div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-sm">
                    <thead class="bg-slate-50 text-left text-xs font-extrabold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-5 py-3">Sesión</th>
                        <th class="px-5 py-3">Paciente</th>
                        <th class="px-5 py-3">Terapia</th>
                        <th class="px-5 py-3">Participantes</th>
                        <th class="px-5 py-3">Cierre</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                    @foreach($sessions as $session)
                        <tr>
                            <td class="whitespace-nowrap px-5 py-4 font-semibold text-slate-800">{{ $session->appointment->starts_at->format('d/m/Y H:i') }}</td>
                            <td class="px-5 py-4 text-slate-800">{{ $session->patient->full_name }}</td>
                            <td class="px-5 py-4 text-slate-700">{{ $session->therapy->name }}</td>
                            <td class="px-5 py-4 text-slate-600">{{ $session->participatingTherapists->pluck('name')->join(', ') ?: $session->appointment->therapists->pluck('name')->join(', ') }}</td>
                            <td class="whitespace-nowrap px-5 py-4 text-slate-600">{{ $session->completed_at?->format('d/m/Y H:i') ?? '—' }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="border-t border-slate-100 px-5 py-4">{{ $sessions->links() }}</div>
        @endif
    </section>
</x-app-shell>
