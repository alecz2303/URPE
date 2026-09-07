<x-app-shell title="Agenda clínica" eyebrow="Operación clínica">
    <x-slot:actions>
        @can('appointments.manage')
            <a href="{{ route('appointments.create') }}" class="rounded-xl bg-cyan-700 px-4 py-2.5 text-sm font-bold text-white hover:bg-cyan-800">+ Nueva cita</a>
        @endcan
    </x-slot:actions>

    <div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div>
            <p class="text-sm font-semibold text-slate-600">{{ $rangeStart->translatedFormat('d M Y') }} — {{ $rangeEnd->translatedFormat('d M Y') }}</p>
            <p class="mt-1 text-xs text-slate-400">{{ $appointments->count() }} {{ $appointments->count() === 1 ? 'cita' : 'citas' }} en el periodo</p>
        </div>
        <div class="inline-flex w-fit rounded-xl border border-slate-200 bg-white p-1 shadow-sm">
            @foreach(['day' => 'Día', 'week' => 'Semana', 'month' => 'Mes'] as $key => $label)
                <a href="{{ route('appointments.index', ['view' => $key, 'date' => $date->toDateString()]) }}" class="rounded-lg px-4 py-2 text-sm font-bold {{ $mode === $key ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-50' }}">{{ $label }}</a>
            @endforeach
        </div>
    </div>

    @if($mode === 'day')
        <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            <div class="mb-5 border-b border-slate-100 pb-4">
                <p class="text-xs font-bold uppercase tracking-[0.16em] text-cyan-700">Vista diaria</p>
                <h2 class="mt-1 text-xl font-bold">{{ $date->translatedFormat('l d \d\e F') }}</h2>
            </div>
            <div class="space-y-1">
                @forelse($appointments as $appointment)
                    <article class="relative grid gap-4 border-l-2 border-slate-200 py-4 pl-6 sm:grid-cols-[90px_minmax(0,1fr)_auto] sm:items-start {{ $appointment->isCancelled() ? 'opacity-55' : '' }}">
                        <span class="absolute -left-[7px] top-7 h-3 w-3 rounded-full ring-4 ring-white" style="background-color: {{ $appointment->therapy->color ?: '#0891b2' }}"></span>
                        <div>
                            <p class="text-xl font-bold tabular-nums">{{ $appointment->starts_at->format('H:i') }}</p>
                            <p class="text-xs text-slate-400">{{ $appointment->ends_at->format('H:i') }}</p>
                        </div>
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <h3 class="font-bold text-slate-900">{{ $appointment->patient->full_name }}</h3>
                                <span class="rounded-full px-2 py-0.5 text-[11px] font-bold {{ $appointment->isCancelled() ? 'bg-rose-50 text-rose-700' : 'bg-emerald-50 text-emerald-700' }}">{{ $appointment->isCancelled() ? 'Cancelada' : 'Programada' }}</span>
                            </div>
                            <p class="mt-1 text-sm font-semibold text-slate-600">{{ $appointment->therapy->name }} · {{ $appointment->duration_minutes }} min</p>
                            <p class="mt-1 text-xs text-slate-400">{{ $appointment->therapists->pluck('name')->implode(' · ') }}</p>
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
                    <div class="py-16 text-center">
                        <h3 class="font-bold">Sin citas para este día</h3>
                        <p class="mt-1 text-sm text-slate-500">Puedes cambiar la fecha o programar una nueva cita.</p>
                    </div>
                @endforelse
            </div>
        </section>
    @elseif($mode === 'week')
        <section class="overflow-x-auto rounded-3xl border border-slate-200 bg-white shadow-sm">
            <div class="grid min-w-[980px] grid-cols-7 divide-x divide-slate-100">
                @foreach($weekDays as $weekDay)
                    @php($dayAppointments = $appointments->filter(fn ($appointment) => $appointment->starts_at->isSameDay($weekDay)))
                    <div class="min-h-[430px] p-4">
                        <div class="mb-4 border-b border-slate-100 pb-3">
                            <p class="text-xs font-bold uppercase tracking-wide text-slate-400">{{ $weekDay->translatedFormat('D') }}</p>
                            <p class="mt-1 text-2xl font-bold">{{ $weekDay->format('d') }}</p>
                        </div>
                        <div class="space-y-3">
                            @forelse($dayAppointments as $appointment)
                                <article class="relative rounded-2xl border border-slate-200 p-3 pl-4 {{ $appointment->isCancelled() ? 'opacity-50' : '' }}">
                                    <span class="absolute inset-y-3 left-0 w-1 rounded-r-full" style="background-color: {{ $appointment->therapy->color ?: '#0891b2' }}"></span>
                                    <p class="text-sm font-bold tabular-nums">{{ $appointment->starts_at->format('H:i') }}</p>
                                    <p class="mt-1 truncate text-sm font-bold">{{ $appointment->patient->full_name }}</p>
                                    <p class="mt-1 truncate text-xs font-semibold text-slate-500">{{ $appointment->therapy->name }}</p>
                                    <p class="mt-2 line-clamp-2 text-[11px] text-slate-400">{{ $appointment->therapists->pluck('name')->implode(', ') }}</p>
                                    @can('appointments.manage')
                                        @if(! $appointment->isCancelled())
                                            <a href="{{ route('appointments.edit', $appointment) }}" class="mt-3 inline-flex text-xs font-bold text-cyan-700">Editar</a>
                                        @endif
                                    @endcan
                                </article>
                            @empty
                                <p class="rounded-xl bg-slate-50 px-3 py-5 text-center text-xs text-slate-400">Sin citas</p>
                            @endforelse
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @else
        <section class="overflow-x-auto rounded-3xl border border-slate-200 bg-white shadow-sm">
            <div class="min-w-[900px]">
                <div class="grid grid-cols-7 border-b border-slate-100 bg-slate-50">
                    @foreach(['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'] as $dayLabel)
                        <div class="px-3 py-3 text-center text-xs font-bold uppercase tracking-wide text-slate-400">{{ $dayLabel }}</div>
                    @endforeach
                </div>
                <div class="grid grid-cols-7">
                    @foreach($calendarDays as $calendarDay)
                        @php($dayAppointments = $appointments->filter(fn ($appointment) => $appointment->starts_at->isSameDay($calendarDay)))
                        <div class="min-h-[145px] border-b border-r border-slate-100 p-3 {{ $calendarDay->month !== $date->month ? 'bg-slate-50/70 text-slate-400' : 'bg-white' }}">
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-sm font-bold">{{ $calendarDay->format('d') }}</span>
                                @if($dayAppointments->isNotEmpty())
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-bold text-slate-500">{{ $dayAppointments->count() }}</span>
                                @endif
                            </div>
                            <div class="mt-2 space-y-1.5">
                                @foreach($dayAppointments->take(3) as $appointment)
                                    <a href="{{ auth()->user()->can('appointments.manage') && ! $appointment->isCancelled() ? route('appointments.edit', $appointment) : '#' }}" class="block truncate rounded-lg border-l-4 bg-slate-50 px-2 py-1.5 text-[11px] font-semibold {{ $appointment->isCancelled() ? 'opacity-50' : '' }}" style="border-left-color: {{ $appointment->therapy->color ?: '#0891b2' }}">{{ $appointment->starts_at->format('H:i') }} · {{ $appointment->patient->full_name }}</a>
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
