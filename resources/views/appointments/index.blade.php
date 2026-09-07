<x-app-shell title="Agenda clínica" eyebrow="Operación clínica">
    <x-slot:actions>
        @can('appointments.manage')
            <a href="{{ route('appointments.create') }}" class="rounded-xl bg-gradient-to-r from-pink-500 to-fuchsia-500 px-4 py-2.5 text-sm font-bold text-white shadow-sm shadow-pink-200 hover:from-pink-600 hover:to-fuchsia-600">+ Nueva cita</a>
        @endcan
    </x-slot:actions>

    @php
        $previousDate = match ($mode) {
            'day' => $date->copy()->subDay(),
            'month' => $date->copy()->subMonth(),
            default => $date->copy()->subWeek(),
        };
        $nextDate = match ($mode) {
            'day' => $date->copy()->addDay(),
            'month' => $date->copy()->addMonth(),
            default => $date->copy()->addWeek(),
        };
    @endphp

    <section class="mb-6 overflow-hidden rounded-3xl border border-cyan-100 bg-gradient-to-r from-white via-cyan-50/80 to-pink-50/70 p-4 shadow-sm sm:p-5">
        <div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
            <div>
                <p class="text-sm font-extrabold text-slate-800">{{ $rangeStart->translatedFormat('d M Y') }} — {{ $rangeEnd->translatedFormat('d M Y') }}</p>
                <p class="mt-1 text-xs font-medium text-slate-500">{{ $appointments->count() }} {{ $appointments->count() === 1 ? 'cita' : 'citas' }} en el periodo</p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('appointments.index', ['view' => $mode, 'date' => $previousDate->toDateString()]) }}" class="grid h-10 w-10 place-items-center rounded-xl border border-cyan-100 bg-white text-lg font-bold text-cyan-700 shadow-sm hover:bg-cyan-50" aria-label="Periodo anterior">‹</a>
                <a href="{{ route('appointments.index', ['view' => $mode, 'date' => now()->toDateString()]) }}" class="rounded-xl border border-cyan-100 bg-white px-3.5 py-2.5 text-sm font-bold text-cyan-700 shadow-sm hover:bg-cyan-50">Hoy</a>
                <a href="{{ route('appointments.index', ['view' => $mode, 'date' => $nextDate->toDateString()]) }}" class="grid h-10 w-10 place-items-center rounded-xl border border-cyan-100 bg-white text-lg font-bold text-cyan-700 shadow-sm hover:bg-cyan-50" aria-label="Periodo siguiente">›</a>

                <form method="GET" action="{{ route('appointments.index') }}" class="flex items-center gap-2 rounded-xl border border-violet-100 bg-white p-1 shadow-sm">
                    <input type="hidden" name="view" value="{{ $mode }}">
                    <input type="date" name="date" value="{{ $date->toDateString() }}" class="rounded-lg border-0 bg-transparent px-2 py-1.5 text-sm font-semibold text-slate-700 focus:outline-none focus:ring-0">
                    <button class="rounded-lg bg-violet-500 px-3 py-2 text-xs font-bold text-white hover:bg-violet-600">Ir</button>
                </form>

                <div class="inline-flex rounded-xl border border-pink-100 bg-white p-1 shadow-sm">
                    @foreach(['day' => 'Día', 'week' => 'Semana', 'month' => 'Mes'] as $key => $label)
                        <a href="{{ route('appointments.index', ['view' => $key, 'date' => $date->toDateString()]) }}" class="rounded-lg px-4 py-2 text-sm font-bold transition {{ $mode === $key ? 'bg-gradient-to-r from-pink-500 to-fuchsia-500 text-white shadow-sm' : 'text-slate-600 hover:bg-pink-50 hover:text-pink-700' }}">{{ $label }}</a>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    @if($mode === 'day')
        <section class="rounded-3xl border border-sky-100 bg-white p-5 shadow-sm sm:p-6">
            <div class="mb-5 flex items-center justify-between gap-4 border-b border-sky-100 pb-4">
                <div>
                    <p class="text-xs font-extrabold uppercase tracking-[0.16em] text-sky-600">Vista diaria</p>
                    <h2 class="mt-1 text-xl font-extrabold text-slate-950">{{ $date->translatedFormat('l d \d\e F') }}</h2>
                </div>
                <div class="hidden h-12 w-12 place-items-center rounded-2xl bg-yellow-100 text-2xl sm:grid">☀</div>
            </div>
            <div class="space-y-2">
                @forelse($appointments as $appointment)
                    <article class="relative grid gap-4 rounded-2xl border border-slate-100 bg-gradient-to-r from-white to-slate-50/60 py-4 pl-7 pr-4 shadow-sm sm:grid-cols-[90px_minmax(0,1fr)_auto] sm:items-start {{ $appointment->isCancelled() ? 'opacity-55' : '' }}">
                        <span class="absolute inset-y-3 left-0 w-1.5 rounded-r-full" style="background-color: {{ $appointment->therapy->color ?: '#0891b2' }}"></span>
                        <div>
                            <p class="text-xl font-extrabold tabular-nums text-slate-950">{{ $appointment->starts_at->format('H:i') }}</p>
                            <p class="text-xs font-medium text-slate-400">{{ $appointment->ends_at->format('H:i') }}</p>
                        </div>
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <h3 class="font-extrabold text-slate-900">{{ $appointment->patient->full_name }}</h3>
                                <span class="rounded-full px-2.5 py-1 text-[11px] font-bold {{ $appointment->isCancelled() ? 'bg-rose-100 text-rose-700' : 'bg-emerald-100 text-emerald-700' }}">{{ $appointment->isCancelled() ? 'Cancelada' : 'Programada' }}</span>
                            </div>
                            <p class="mt-1 text-sm font-bold" style="color: {{ $appointment->therapy->color ?: '#0891b2' }}">{{ $appointment->therapy->name }} · {{ $appointment->duration_minutes }} min</p>
                            <p class="mt-1 text-xs font-medium text-slate-500">{{ $appointment->therapists->pluck('name')->implode(' · ') }}</p>
                        </div>
                        @can('appointments.manage')
                            @if(! $appointment->isCancelled())
                                <div class="flex gap-3 sm:justify-end">
                                    <a href="{{ route('appointments.edit', $appointment) }}" class="text-sm font-bold text-cyan-700">Editar</a>
                                    <form method="POST" action="{{ route('appointments.cancel', $appointment) }}" data-confirm="¿Cancelar esta cita?">
                                        @csrf
                                        @method('PATCH')
                                        <button class="text-sm font-bold text-rose-600">Cancelar</button>
                                    </form>
                                </div>
                            @endif
                        @endcan
                    </article>
                @empty
                    <div class="rounded-2xl bg-gradient-to-br from-cyan-50 to-pink-50 py-16 text-center">
                        <div class="mx-auto grid h-12 w-12 place-items-center rounded-2xl bg-white text-xl shadow-sm">♡</div>
                        <h3 class="mt-4 font-bold">Sin citas para este día</h3>
                        <p class="mt-1 text-sm text-slate-500">Elige otra fecha o programa una nueva cita.</p>
                    </div>
                @endforelse
            </div>
        </section>
    @elseif($mode === 'week')
        <section class="overflow-x-auto rounded-3xl border border-cyan-100 bg-white shadow-sm">
            <div class="grid min-w-[980px] grid-cols-7 divide-x divide-cyan-50">
                @foreach($weekDays as $weekDay)
                    @php($dayAppointments = $appointments->filter(fn ($appointment) => $appointment->starts_at->isSameDay($weekDay)))
                    @php($isToday = $weekDay->isToday())
                    <div class="min-h-[470px] p-4 {{ $isToday ? 'bg-cyan-50/60' : 'bg-white' }}">
                        <div class="mb-4 rounded-2xl px-3 py-3 {{ $isToday ? 'bg-gradient-to-br from-cyan-500 to-sky-500 text-white shadow-sm' : 'bg-slate-50' }}">
                            <p class="text-xs font-bold uppercase tracking-wide {{ $isToday ? 'text-cyan-50' : 'text-slate-400' }}">{{ $weekDay->translatedFormat('D') }}</p>
                            <p class="mt-1 text-2xl font-extrabold">{{ $weekDay->format('d') }}</p>
                        </div>
                        <div class="space-y-3">
                            @forelse($dayAppointments as $appointment)
                                <article class="relative overflow-hidden rounded-2xl border border-slate-100 bg-white p-3 pl-4 shadow-sm {{ $appointment->isCancelled() ? 'opacity-50' : '' }}">
                                    <span class="absolute inset-y-0 left-0 w-1.5" style="background-color: {{ $appointment->therapy->color ?: '#0891b2' }}"></span>
                                    <p class="text-sm font-extrabold tabular-nums text-slate-900">{{ $appointment->starts_at->format('H:i') }}</p>
                                    <p class="mt-1 truncate text-sm font-bold">{{ $appointment->patient->full_name }}</p>
                                    <p class="mt-1 truncate text-xs font-bold" style="color: {{ $appointment->therapy->color ?: '#0891b2' }}">{{ $appointment->therapy->name }}</p>
                                    <p class="mt-2 line-clamp-2 text-[11px] font-medium text-slate-400">{{ $appointment->therapists->pluck('name')->implode(', ') }}</p>
                                    @can('appointments.manage')
                                        @if(! $appointment->isCancelled())
                                            <a href="{{ route('appointments.edit', $appointment) }}" class="mt-3 inline-flex text-xs font-bold text-cyan-700">Editar</a>
                                        @endif
                                    @endcan
                                </article>
                            @empty
                                <a href="{{ route('appointments.index', ['view' => 'day', 'date' => $weekDay->toDateString()]) }}" class="block rounded-xl border border-dashed border-slate-200 bg-slate-50/70 px-3 py-5 text-center text-xs font-medium text-slate-400 hover:border-cyan-200 hover:bg-cyan-50 hover:text-cyan-700">Sin citas</a>
                            @endforelse
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @else
        <section class="overflow-x-auto rounded-3xl border border-violet-100 bg-white shadow-sm">
            <div class="min-w-[900px]">
                <div class="grid grid-cols-7 border-b border-violet-100 bg-gradient-to-r from-violet-50 via-pink-50 to-amber-50">
                    @foreach(['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'] as $dayLabel)
                        <div class="px-3 py-3 text-center text-xs font-extrabold uppercase tracking-wide text-violet-500">{{ $dayLabel }}</div>
                    @endforeach
                </div>
                <div class="grid grid-cols-7">
                    @foreach($calendarDays as $calendarDay)
                        @php($dayAppointments = $appointments->filter(fn ($appointment) => $appointment->starts_at->isSameDay($calendarDay)))
                        @php($isToday = $calendarDay->isToday())
                        <div class="min-h-[150px] border-b border-r border-slate-100 p-3 {{ $calendarDay->month !== $date->month ? 'bg-slate-50/60 text-slate-400' : ($isToday ? 'bg-cyan-50/70' : 'bg-white') }}">
                            <div class="flex items-center justify-between gap-2">
                                <a href="{{ route('appointments.index', ['view' => 'day', 'date' => $calendarDay->toDateString()]) }}" class="grid h-8 min-w-8 place-items-center rounded-full px-2 text-sm font-extrabold transition {{ $isToday ? 'bg-gradient-to-br from-pink-500 to-fuchsia-500 text-white shadow-sm' : 'hover:bg-cyan-100 hover:text-cyan-800' }}">{{ $calendarDay->format('d') }}</a>
                                @if($dayAppointments->isNotEmpty())
                                    <span class="rounded-full bg-violet-100 px-2 py-0.5 text-[10px] font-bold text-violet-600">{{ $dayAppointments->count() }}</span>
                                @endif
                            </div>
                            <div class="mt-2 space-y-1.5">
                                @foreach($dayAppointments->take(3) as $appointment)
                                    @if(auth()->user()->can('appointments.manage') && ! $appointment->isCancelled())
                                        <a href="{{ route('appointments.edit', $appointment) }}" class="block truncate rounded-lg border-l-4 bg-slate-50 px-2 py-1.5 text-[11px] font-semibold shadow-sm {{ $appointment->isCancelled() ? 'opacity-50' : '' }}" style="border-left-color: {{ $appointment->therapy->color ?: '#0891b2' }}">{{ $appointment->starts_at->format('H:i') }} · {{ $appointment->patient->full_name }}</a>
                                    @else
                                        <div class="block truncate rounded-lg border-l-4 bg-slate-50 px-2 py-1.5 text-[11px] font-semibold {{ $appointment->isCancelled() ? 'opacity-50' : '' }}" style="border-left-color: {{ $appointment->therapy->color ?: '#0891b2' }}">{{ $appointment->starts_at->format('H:i') }} · {{ $appointment->patient->full_name }}</div>
                                    @endif
                                @endforeach
                                @if($dayAppointments->count() > 3)
                                    <a href="{{ route('appointments.index', ['view' => 'day', 'date' => $calendarDay->toDateString()]) }}" class="inline-flex text-[11px] font-bold text-cyan-700">+ {{ $dayAppointments->count() - 3 }} más</a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
</x-app-shell>
