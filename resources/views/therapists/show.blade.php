<x-app-shell :title="$therapist->name" eyebrow="Terapeuta">
    <x-slot:actions>
        <a href="{{ route('therapists.index') }}" class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 hover:border-slate-300">Volver</a>
        <a href="{{ route('therapists.edit', $therapist) }}" class="rounded-xl bg-cyan-700 px-4 py-2.5 text-sm font-bold text-white hover:bg-cyan-800">Editar terapeuta</a>
    </x-slot:actions>

    @php
        $dayNames = [
            0 => 'Domingo',
            1 => 'Lunes',
            2 => 'Martes',
            3 => 'Miércoles',
            4 => 'Jueves',
            5 => 'Viernes',
            6 => 'Sábado',
        ];
    @endphp

    <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_360px]">
        <section class="space-y-6">
            <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <p class="text-sm font-semibold text-slate-500">{{ $therapist->professional_title ?: 'Perfil clínico' }}</p>
                        <h2 class="mt-1 text-2xl font-bold">{{ $therapist->name }}</h2>
                    </div>
                    <span class="rounded-full px-3 py-1 text-xs font-bold {{ $therapist->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">{{ $therapist->is_active ? 'Activo' : 'Inactivo' }}</span>
                </div>

                <dl class="mt-6 grid gap-5 sm:grid-cols-2">
                    <div>
                        <dt class="text-xs font-bold uppercase tracking-wide text-slate-400">Cédula</dt>
                        <dd class="mt-1 text-sm font-semibold text-slate-700">{{ $therapist->license_number ?: '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-bold uppercase tracking-wide text-slate-400">Usuario vinculado</dt>
                        <dd class="mt-1 text-sm font-semibold text-slate-700">{{ $therapist->user?->email ?: 'Sin vínculo' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-bold uppercase tracking-wide text-slate-400">Teléfono</dt>
                        <dd class="mt-1 text-sm font-semibold text-slate-700">{{ $therapist->phone ?: '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-bold uppercase tracking-wide text-slate-400">Correo</dt>
                        <dd class="mt-1 break-all text-sm font-semibold text-slate-700">{{ $therapist->email ?: '—' }}</dd>
                    </div>
                </dl>

                @if($therapist->notes)
                    <div class="mt-6 border-t border-slate-100 pt-5">
                        <p class="text-xs font-bold uppercase tracking-wide text-slate-400">Notas</p>
                        <p class="mt-2 whitespace-pre-line text-sm leading-6 text-slate-600">{{ $therapist->notes }}</p>
                    </div>
                @endif
            </article>

            <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.16em] text-cyan-700">Disponibilidad semanal</p>
                        <h2 class="mt-1 text-xl font-bold">Horario operativo</h2>
                    </div>
                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600">{{ $therapist->availabilityWindows->count() }} ventanas</span>
                </div>

                <div class="mt-5 grid gap-3 sm:grid-cols-2">
                    @forelse($therapist->availabilityWindows as $window)
                        <div class="rounded-2xl border border-slate-100 bg-slate-50 px-4 py-3">
                            <p class="text-xs font-bold uppercase tracking-wide text-slate-400">{{ $dayNames[$window->day_of_week] ?? 'Día '.$window->day_of_week }}</p>
                            <p class="mt-1 text-sm font-bold text-slate-700">{{ substr($window->starts_at, 0, 5) }} — {{ substr($window->ends_at, 0, 5) }}</p>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">No hay disponibilidad configurada.</p>
                    @endforelse
                </div>
            </article>

            <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <p class="text-xs font-bold uppercase tracking-[0.16em] text-cyan-700">Próximas citas</p>
                <h2 class="mt-1 text-xl font-bold">Actividad programada</h2>

                <div class="mt-5 divide-y divide-slate-100">
                    @forelse($therapist->appointments as $appointment)
                        <div class="grid gap-2 py-4 sm:grid-cols-[120px_minmax(0,1fr)]">
                            <div>
                                <p class="text-sm font-bold">{{ $appointment->starts_at->format('d/m/Y') }}</p>
                                <p class="text-xs text-slate-400">{{ $appointment->starts_at->format('H:i') }}</p>
                            </div>
                            <div>
                                <p class="font-bold">{{ $appointment->patient->full_name }}</p>
                                <p class="text-sm text-slate-500">{{ $appointment->therapy->name }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="py-6 text-sm text-slate-500">Sin citas próximas.</p>
                    @endforelse
                </div>
            </article>
        </section>

        <aside class="space-y-6">
            <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <p class="text-xs font-bold uppercase tracking-[0.16em] text-slate-400">Bloqueos recientes</p>
                <div class="mt-4 space-y-3">
                    @forelse($therapist->blocks as $block)
                        <div class="rounded-2xl bg-amber-50 px-4 py-3">
                            <p class="text-sm font-bold text-amber-900">{{ $block->starts_at->format('d/m/Y H:i') }}</p>
                            <p class="mt-1 text-xs text-amber-700">{{ $block->ends_at->format('d/m/Y H:i') }}</p>
                            @if($block->reason)
                                <p class="mt-2 text-sm text-amber-900">{{ $block->reason }}</p>
                            @endif
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">Sin bloqueos registrados.</p>
                    @endforelse
                </div>
            </article>
        </aside>
    </div>
</x-app-shell>
