<x-app-shell title="Hoy en URPE" eyebrow="Operación clínica">
    <x-slot:actions>
        @can('appointments.manage')
            <a href="{{ route('appointments.create') }}" class="rounded-xl bg-gradient-to-r from-pink-500 to-fuchsia-500 px-4 py-2.5 text-sm font-bold text-white shadow-sm shadow-pink-200 hover:from-pink-600 hover:to-fuchsia-600">+ Nueva cita</a>
        @endcan
    </x-slot:actions>

    <div class="-mt-2 mb-6 flex flex-wrap items-center gap-3">
        <p class="text-sm font-semibold text-slate-500">{{ $today->translatedFormat('l d \d\e F \d\e Y') }}</p>
        <span class="rounded-full bg-yellow-100 px-3 py-1 text-xs font-bold text-amber-700">☀ Cada paso cuenta</span>
    </div>

    @if($todayAppointmentsCount !== null || $activePatientsCount !== null || $activeTherapistsCount !== null)
        <section class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @if($todayAppointmentsCount !== null)
                <article class="relative overflow-hidden rounded-2xl border border-pink-100 bg-gradient-to-br from-white to-pink-50 p-5 shadow-sm">
                    <div class="absolute -right-4 -top-4 h-16 w-16 rounded-full bg-pink-100/70"></div>
                    <div class="relative flex items-center justify-between gap-4"><div><p class="text-sm font-bold text-pink-600">Citas de hoy</p><p class="mt-1 text-3xl font-extrabold text-slate-950">{{ $todayAppointmentsCount }}</p></div><div class="grid h-12 w-12 place-items-center rounded-2xl bg-pink-100 text-xl">◷</div></div>
                </article>
            @endif
            @if($activePatientsCount !== null)
                <article class="relative overflow-hidden rounded-2xl border border-cyan-100 bg-gradient-to-br from-white to-cyan-50 p-5 shadow-sm">
                    <div class="absolute -right-4 -top-4 h-16 w-16 rounded-full bg-cyan-100/70"></div>
                    <div class="relative flex items-center justify-between gap-4"><div><p class="text-sm font-bold text-cyan-700">Pacientes activos</p><p class="mt-1 text-3xl font-extrabold text-slate-950">{{ $activePatientsCount }}</p></div><div class="grid h-12 w-12 place-items-center rounded-2xl bg-cyan-100 text-xl">♡</div></div>
                </article>
            @endif
            @if($activeTherapistsCount !== null)
                <article class="relative overflow-hidden rounded-2xl border border-violet-100 bg-gradient-to-br from-white to-violet-50 p-5 shadow-sm">
                    <div class="absolute -right-4 -top-4 h-16 w-16 rounded-full bg-violet-100/70"></div>
                    <div class="relative flex items-center justify-between gap-4"><div><p class="text-sm font-bold text-violet-600">Terapeutas activos</p><p class="mt-1 text-3xl font-extrabold text-slate-950">{{ $activeTherapistsCount }}</p></div><div class="grid h-12 w-12 place-items-center rounded-2xl bg-violet-100 text-xl">✦</div></div>
                </article>
            @endif
        </section>
    @endif

    @can('appointments.view')
        <section data-testid="agenda-dashboard-card" class="mt-6 overflow-hidden rounded-3xl border border-cyan-100 bg-white shadow-sm">
            <div class="flex flex-col gap-4 border-b border-cyan-100 bg-gradient-to-r from-cyan-50 via-white to-pink-50 px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
                <div><p class="text-xs font-extrabold uppercase tracking-[0.16em] text-cyan-700">Agenda de hoy</p><h2 class="mt-1 text-xl font-extrabold text-slate-950">Actividad clínica programada</h2></div>
                <a href="{{ route('appointments.index', ['view' => 'day', 'date' => $today->toDateString()]) }}" class="w-fit rounded-xl bg-cyan-600 px-3.5 py-2 text-sm font-bold text-white shadow-sm hover:bg-cyan-700">Abrir agenda clínica →</a>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse($todayAppointments as $appointment)
                    <article class="relative grid gap-3 px-6 py-5 transition hover:bg-cyan-50/30 sm:grid-cols-[88px_minmax(0,1fr)_auto] sm:items-center {{ $appointment->isCancelled() ? 'opacity-55' : '' }}">
                        <span class="absolute inset-y-3 left-0 w-1.5 rounded-r-full" style="background-color: {{ $appointment->therapy->color ?: '#0891b2' }}"></span>
                        <div><p class="text-lg font-extrabold tabular-nums text-slate-950">{{ $appointment->starts_at->format('H:i') }}</p><p class="text-xs font-medium text-slate-400">{{ $appointment->ends_at->format('H:i') }}</p></div>
                        <div class="min-w-0"><h3 class="truncate font-extrabold">{{ $appointment->patient->full_name }}</h3><p class="mt-1 text-sm font-bold" style="color: {{ $appointment->therapy->color ?: '#0891b2' }}">{{ $appointment->therapy->name }}</p><p class="mt-1 text-xs font-medium text-slate-400">{{ $appointment->therapists->pluck('name')->implode(' · ') }}</p></div>
                        @can('appointments.manage')
                            @if(! $appointment->isCancelled())
                                <a href="{{ route('appointments.edit', $appointment) }}" class="text-sm font-bold text-slate-500 hover:text-cyan-700">Editar</a>
                            @endif
                        @endcan
                    </article>
                @empty
                    <div class="bg-gradient-to-br from-white via-cyan-50/40 to-pink-50/40 px-6 py-14 text-center">
                        <div class="mx-auto grid h-12 w-12 place-items-center rounded-2xl bg-emerald-100 text-2xl text-emerald-700">✓</div>
                        <h3 class="mt-4 font-bold">Sin citas programadas hoy</h3>
                        <p class="mt-1 text-sm text-slate-500">La agenda está libre para esta fecha.</p>
                        @can('appointments.manage')
                            <a href="{{ route('appointments.create') }}" class="mt-4 inline-flex rounded-xl bg-pink-500 px-4 py-2 text-sm font-bold text-white">Programar una cita →</a>
                        @endcan
                    </div>
                @endforelse
            </div>
        </section>
    @endcan
</x-app-shell>
