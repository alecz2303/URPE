<x-app-shell title="Hoy en URPE" eyebrow="Operación clínica">
    <x-slot:actions>
        @can('appointments.manage')
            <a href="{{ route('appointments.create') }}" class="rounded-xl bg-cyan-700 px-4 py-2.5 text-sm font-bold text-white hover:bg-cyan-800">+ Nueva cita</a>
        @endcan
    </x-slot:actions>

    <p class="-mt-3 mb-6 text-sm text-slate-500">{{ $today->translatedFormat('l d \d\e F \d\e Y') }}</p>

    @if($todayAppointmentsCount !== null || $activePatientsCount !== null || $activeTherapistsCount !== null)
        <section class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @if($todayAppointmentsCount !== null)
                <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><p class="text-sm font-semibold text-slate-500">Citas de hoy</p><p class="mt-1 text-3xl font-bold">{{ $todayAppointmentsCount }}</p></article>
            @endif
            @if($activePatientsCount !== null)
                <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><p class="text-sm font-semibold text-slate-500">Pacientes activos</p><p class="mt-1 text-3xl font-bold">{{ $activePatientsCount }}</p></article>
            @endif
            @if($activeTherapistsCount !== null)
                <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><p class="text-sm font-semibold text-slate-500">Terapeutas activos</p><p class="mt-1 text-3xl font-bold">{{ $activeTherapistsCount }}</p></article>
            @endif
        </section>
    @endif

    @can('appointments.view')
        <section data-testid="agenda-dashboard-card" class="mt-6 overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between gap-4 border-b border-slate-100 px-6 py-5">
                <div><p class="text-xs font-bold uppercase tracking-[0.16em] text-cyan-700">Agenda de hoy</p><h2 class="mt-1 text-xl font-bold">Actividad clínica programada</h2></div>
                <a href="{{ route('appointments.index', ['view' => 'day', 'date' => $today->toDateString()]) }}" class="text-sm font-bold text-cyan-700">Abrir agenda clínica →</a>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse($todayAppointments as $appointment)
                    <article class="relative grid gap-3 px-6 py-5 sm:grid-cols-[88px_minmax(0,1fr)_auto] sm:items-center {{ $appointment->isCancelled() ? 'opacity-55' : '' }}">
                        <span class="absolute inset-y-3 left-0 w-1 rounded-r-full" style="background-color: {{ $appointment->therapy->color ?: '#0891b2' }}"></span>
                        <div><p class="text-lg font-bold tabular-nums">{{ $appointment->starts_at->format('H:i') }}</p><p class="text-xs text-slate-400">{{ $appointment->ends_at->format('H:i') }}</p></div>
                        <div class="min-w-0"><h3 class="truncate font-bold">{{ $appointment->patient->full_name }}</h3><p class="mt-1 text-sm font-semibold text-slate-600">{{ $appointment->therapy->name }}</p><p class="mt-1 text-xs text-slate-400">{{ $appointment->therapists->pluck('name')->implode(' · ') }}</p></div>
                        @can('appointments.manage')
                            @if(! $appointment->isCancelled())
                                <a href="{{ route('appointments.edit', $appointment) }}" class="text-sm font-bold text-slate-500 hover:text-cyan-700">Editar</a>
                            @endif
                        @endcan
                    </article>
                @empty
                    <div class="px-6 py-14 text-center">
                        <div class="mx-auto grid h-12 w-12 place-items-center rounded-2xl bg-cyan-50 text-2xl text-cyan-700">✓</div>
                        <h3 class="mt-4 font-bold">Sin citas programadas hoy</h3>
                        <p class="mt-1 text-sm text-slate-500">La agenda está libre para esta fecha.</p>
                        @can('appointments.manage')
                            <a href="{{ route('appointments.create') }}" class="mt-4 inline-flex text-sm font-bold text-cyan-700">Programar una cita →</a>
                        @endcan
                    </div>
                @endforelse
            </div>
        </section>
    @endcan
</x-app-shell>
