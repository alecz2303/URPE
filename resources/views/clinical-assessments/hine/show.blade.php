<x-app-shell title="Resultado HINE" eyebrow="{{ $patient->full_name }}">
    <x-slot:actions>
        <script>
    document.addEventListener('DOMContentLoaded', () => {
        const svgNS = 'http://www.w3.org/2000/svg';
        const make = (name, attrs = {}) => {
            const node = document.createElementNS(svgNS, name);
            Object.entries(attrs).forEach(([key, value]) => node.setAttribute(key, value));
            return node;
        };

        const radar = document.getElementById('hine-radar');
        if (radar) {
            const values = [
                Number(@json($hine->cranial_nerves_score ?? 0)) / 15,
                Number(@json($hine->posture_score ?? 0)) / 18,
                Number(@json($hine->movements_score ?? 0)) / 6,
                Number(@json($hine->tone_score ?? 0)) / 24,
                Number(@json($hine->reflexes_reactions_score ?? 0)) / 15
            ].map(value => Math.max(0, Math.min(1, value)));
            const labels = ['Pares craneales', 'Postura', 'Movimientos', 'Tono', 'Reflejos'];
            const cx = 210, cy = 190, radius = 125;
            const point = (index, scale = 1) => {
                const angle = (-90 + index * 72) * Math.PI / 180;
                return [cx + Math.cos(angle) * radius * scale, cy + Math.sin(angle) * radius * scale];
            };
            [0.25, 0.5, 0.75, 1].forEach(scale => {
                radar.appendChild(make('polygon', {points: values.map((_, i) => point(i, scale).join(',')).join(' '), fill: 'none', stroke: '#cbd5e1', 'stroke-width': '1'}));
            });
            values.forEach((_, i) => {
                const [x, y] = point(i);
                radar.appendChild(make('line', {x1: cx, y1: cy, x2: x, y2: y, stroke: '#cbd5e1', 'stroke-width': '1'}));
                const [lx, ly] = point(i, 1.22);
                const label = make('text', {x: lx, y: ly, 'text-anchor': 'middle', 'font-size': '12', 'font-weight': '700', fill: '#475569'});
                label.textContent = labels[i];
                radar.appendChild(label);
            });
            radar.appendChild(make('polygon', {points: values.map((value, i) => point(i, value).join(',')).join(' '), fill: '#06b6d4', 'fill-opacity': '.22', stroke: '#0891b2', 'stroke-width': '3'}));
            values.forEach((value, i) => {
                const [x, y] = point(i, value);
                radar.appendChild(make('circle', {cx: x, cy: y, r: '5', fill: '#0891b2', stroke: '#fff', 'stroke-width': '2'}));
            });
        }

        const gauge = document.getElementById('hine-gauge');
        if (gauge) {
            const score = Math.max(0, Math.min(78, Number(@json($hine->global_score ?? 0))));
            const cx = 120, cy = 122, radius = 82;
            const polar = value => {
                const angle = Math.PI - (value / 78) * Math.PI;
                return [cx + Math.cos(angle) * radius, cy - Math.sin(angle) * radius];
            };
            const arc = (from, to, stroke) => {
                const [x1, y1] = polar(from), [x2, y2] = polar(to);
                gauge.appendChild(make('path', {d: `M ${x1} ${y1} A ${radius} ${radius} 0 0 1 ${x2} ${y2}`, fill: 'none', stroke, 'stroke-width': '28', 'stroke-linecap': 'butt'}));
            };
            arc(0, 40, '#e2e8f0');
            arc(40, 60, '#cbd5e1');
            arc(60, 78, '#94a3b8');
            [0, 40, 60, 78].forEach(value => {
                const [x, y] = polar(value);
                const t = make('text', {x, y: y + (value === 0 || value === 78 ? 22 : -12), 'text-anchor': 'middle', 'font-size': '12', 'font-weight': '800', fill: '#64748b'});
                t.textContent = value;
                gauge.appendChild(t);
            });
            const [nx, ny] = polar(score);
            gauge.appendChild(make('line', {x1: cx, y1: cy, x2: nx, y2: ny, stroke: '#7c3aed', 'stroke-width': '5', 'stroke-linecap': 'round'}));
            gauge.appendChild(make('circle', {cx, cy, r: '10', fill: '#7c3aed'}));
            const value = make('text', {x: cx, y: 78, 'text-anchor': 'middle', 'font-size': '38', 'font-weight': '900', fill: '#5b21b6'});
            value.textContent = score.toFixed(1);
            gauge.appendChild(value);
            const max = make('text', {x: cx, y: 96, 'text-anchor': 'middle', 'font-size': '13', 'font-weight': '700', fill: '#64748b'});
            max.textContent = 'de 78';
            gauge.appendChild(max);
        }
    });
</script>
@if($assessment->status !== 'finalized')
            @can('clinical_assessments.manage')
                <a href="{{ route('patients.hine-assessments.edit', [$patient, $assessment]) }}" class="rounded-xl bg-cyan-600 px-4 py-2.5 text-sm font-bold text-white shadow-sm">Continuar captura</a>
                <form id="hine-finalize-form" method="POST" action="{{ route('patients.hine-assessments.finalize', [$patient, $assessment]) }}" class="inline">
                    @csrf
                    <button id="hine-finalize-button" type="button" class="rounded-xl bg-violet-600 px-4 py-2.5 text-sm font-bold text-white shadow-sm">Finalizar evaluación</button>
                </form>
            @endcan
        @endif
        <a href="{{ route('patients.hine-assessments.index', $patient) }}" class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-700">Historial HINE</a>
    </x-slot:actions>

    <div class="space-y-6">
        @if($assessment->status !== 'finalized')
            <section class="rounded-2xl border border-amber-200 bg-amber-50 px-5 py-4 text-sm text-amber-900 shadow-sm">
                <p class="font-black">Resultado provisional · evaluación en borrador</p>
                <p class="mt-1">Los cálculos reflejan únicamente la información guardada hasta este momento. La evaluación no forma parte del historial final hasta que se complete y finalice.</p>
                @error('finalize')
                    <p class="mt-2 font-bold text-red-700">{{ $message }}</p>
                @enderror
            </section>
        @endif

        <section class="rounded-3xl border border-violet-100 bg-gradient-to-r from-violet-50 via-white to-cyan-50 p-6 shadow-sm">
            <div class="flex flex-wrap items-start justify-between gap-5">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.16em] text-violet-700">HINE {{ $assessment->instrument_version }}</p>
                    <h2 class="mt-1 text-xl font-black text-slate-900">{{ $assessment->examination_date->format('d/m/Y') }}</h2>
                    <p class="mt-2 text-sm text-slate-500">Evaluación {{ $assessment->status === 'finalized' ? 'finalizada' : 'en borrador' }} · {{ $assessment->author?->name ?: 'Autor no disponible' }}</p>
                </div>
                <div class="rounded-2xl bg-white px-5 py-4 text-center ring-1 ring-violet-100">
                    <p class="text-xs font-bold uppercase text-slate-400">Puntuación global</p>
                    <p class="mt-1 text-4xl font-black text-violet-700">{{ $hine->global_score ?? '—' }}<span class="text-base text-slate-400"> / 78</span></p>
                    <p class="mt-1 text-sm font-semibold text-slate-600">{{ $hine->asymmetry_count }} asimetrías</p>
                </div>
            </div>
        </section>

        <section class="rounded-3xl border border-slate-100 bg-white p-6 shadow-sm">
            <p class="text-xs font-bold uppercase tracking-[0.16em] text-slate-500">Datos del examen</p>
            <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-4 text-sm text-slate-600">
                <p><strong class="text-slate-800">Nombre y apellidos:</strong><br>{{ $patient->full_name }}</p>
                <p><strong class="text-slate-800">Fecha de nacimiento:</strong><br>{{ $patient->date_of_birth?->format('d/m/Y') ?: '—' }}</p>
                <p><strong class="text-slate-800">Edad gestacional:</strong><br>{{ $hine->gestational_age ?: '—' }}</p>
                <p><strong class="text-slate-800">Edad cronológica:</strong><br>{{ $hine->chronological_age ?: '—' }}</p>
                <p><strong class="text-slate-800">Edad corregida:</strong><br>{{ $hine->corrected_age ?: '—' }}</p>
                <p><strong class="text-slate-800">Perímetro cefálico:</strong><br>{{ $hine->head_circumference ?: '—' }}</p>
                <p><strong class="text-slate-800">Puntuación de comportamiento:</strong><br>No puntúa; no forma parte de la puntuación óptima.</p>
                <p><strong class="text-slate-800">Número de asimetrías:</strong><br>{{ $hine->asymmetry_count }}</p>
            </div>
            @if($hine->general_comments)<div class="mt-4 rounded-xl bg-slate-50 p-4 text-sm text-slate-600"><strong class="text-slate-800">Comentarios:</strong> {{ $hine->general_comments }}</div>@endif
        </section>

        <section class="grid gap-3 sm:grid-cols-2 xl:grid-cols-5">
            @foreach($sections as $sectionKey => $section)
                @php($field = match($sectionKey) {'cranial_nerves'=>'cranial_nerves_score','posture'=>'posture_score','movements'=>'movements_score','tone'=>'tone_score','reflexes_reactions'=>'reflexes_reactions_score'})
                <div class="rounded-2xl border border-slate-100 bg-white p-4 shadow-sm">
                    <p class="text-xs font-bold uppercase tracking-wide text-slate-400">{{ $section['label'] }}</p>
                    <p class="mt-2 text-2xl font-black text-slate-800">{{ $hine->{$field} ?? '—' }} <span class="text-sm text-slate-400">/ {{ $section['maximum'] }}</span></p>
                </div>
            @endforeach
        </section>

        <section class="rounded-3xl border border-slate-100 bg-white shadow-sm">
            <div class="border-b border-slate-100 px-6 py-5">
                <p class="text-xs font-bold uppercase tracking-[0.16em] text-slate-500">Detalle clínico</p>
                <h3 class="mt-1 text-lg font-black text-slate-900">Reactivos neurológicos registrados</h3>
            </div>
            <div class="divide-y divide-slate-100">
                @foreach($sections as $sectionKey => $section)
                    <div class="p-6">
                        <h4 class="font-black text-slate-800">{{ $section['label'] }}</h4>
                        <div class="mt-3 grid gap-2">
                            @foreach($section['items'] as $item)
                                @php($response = $responses->get($item['key']))
                                <div class="rounded-2xl bg-slate-50 p-4">
                                    <div class="flex flex-wrap items-center justify-between gap-2">
                                        <span class="font-semibold text-slate-700">{{ $item['label'] }}</span>
                                        <span class="rounded-full bg-white px-3 py-1 text-sm font-black text-violet-700 ring-1 ring-slate-200">{{ $response?->score ?? '—' }}</span>
                                    </div>
                                    @isset($item['instruction'])<p class="mt-2 text-sm text-slate-500">{{ $item['instruction'] }}</p>@endisset
                                    @isset($item['age_note'])<p class="mt-1 text-xs font-semibold text-violet-700">{{ $item['age_note'] }}</p>@endisset
                                    @isset($item['sites'])<p class="mt-1 text-xs font-semibold text-slate-500">{{ implode(' · ', $item['sites']) }}</p>@endisset
                                    @if($response?->asymmetry)<p class="mt-2 text-xs font-bold text-amber-700">Asimetría registrada</p>@endif
                                    @if($response?->comments)<p class="mt-2 text-sm text-slate-600">{{ $response->comments }}</p>@endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="grid gap-6 xl:grid-cols-2">
            <div class="rounded-3xl border border-emerald-100 bg-white p-6 shadow-sm">
                <p class="text-xs font-bold uppercase tracking-[0.16em] text-emerald-700">No puntúa</p>
                <h3 class="mt-1 text-lg font-black text-slate-900">Hitos motores</h3>
                <div class="mt-4 space-y-3">
                    @foreach($motorMilestones as $item)
                        @php($response = $responses->get($item['key']))
                        <div class="rounded-2xl bg-emerald-50/60 p-4">
                            <p class="font-semibold text-slate-800">{{ $item['label'] }}</p>
                            @isset($item['instruction'])<p class="mt-1 text-sm text-slate-500">{{ $item['instruction'] }}</p>@endisset
                            @isset($item['age_note'])<p class="mt-1 text-xs font-semibold text-emerald-700">{{ $item['age_note'] }}</p>@endisset
                            <p class="mt-1 text-sm text-slate-600">Observado: {{ data_get($response?->response_data, 'observed') ?: '—' }}</p>
                            <p class="text-sm text-slate-600">Edad de adquisición: {{ data_get($response?->response_data, 'acquisition_age') ?: '—' }}</p>
                            @if($response?->comments)<p class="mt-2 text-sm text-slate-600">{{ $response->comments }}</p>@endif
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="rounded-3xl border border-amber-100 bg-white p-6 shadow-sm">
                <p class="text-xs font-bold uppercase tracking-[0.16em] text-amber-700">No puntúa</p>
                <h3 class="mt-1 text-lg font-black text-slate-900">Comportamiento</h3>
                <div class="mt-4 space-y-3">
                    @foreach($behaviorItems as $item)
                        @php($response = $responses->get($item['key']))
                        @php($option = data_get($response?->response_data, 'option'))
                        <div class="rounded-2xl bg-amber-50/60 p-4">
                            <p class="font-semibold text-slate-800">{{ $item['label'] }}</p>
                            <p class="mt-1 text-sm text-slate-600">{{ $option !== null ? ($item['options'][$option] ?? '—') : '—' }}</p>
                            @if($response?->comments)<p class="mt-2 text-sm text-slate-600">{{ $response->comments }}</p>@endif
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="rounded-3xl border border-violet-100 bg-white p-6 shadow-sm">
            <div class="flex flex-wrap items-end justify-between gap-4">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.16em] text-violet-700">Resumen de puntuaciones</p>
                    <h3 class="mt-1 text-lg font-black text-slate-900">Resultado neurológico HINE</h3>
                    <p class="mt-1 text-sm text-slate-500">Los porcentajes visuales comparan cada sección únicamente contra su propio máximo; no modifican ni sustituyen la puntuación HINE.</p>
                </div>
                <div class="rounded-2xl bg-violet-50 px-5 py-3 text-right ring-1 ring-violet-100"><p class="text-xs font-bold uppercase text-violet-600">Puntuación global</p><p class="text-3xl font-black text-violet-800">{{ $hine->global_score ?? '—' }} <span class="text-sm text-violet-500">/ 78</span></p></div>
            </div>
            <div class="mt-5 grid gap-3 sm:grid-cols-2 xl:grid-cols-6">
                <div class="rounded-2xl border border-slate-100 bg-slate-50 p-4">
                    <p class="min-h-10 text-xs font-bold uppercase leading-5 tracking-wide text-slate-500">Pares craneales</p>
                    <p class="mt-2 text-xl font-black text-slate-900">{{ $hine->cranial_nerves_score ?? '—' }} <span class="text-xs text-slate-400">/ 15</span></p>
                    <div class="mt-3 h-2.5 overflow-hidden rounded-full bg-slate-200"><div class="h-full rounded-full bg-cyan-500" style="width: {{ $hine->cranial_nerves_score !== null ? number_format(min(100, max(0, ((float) $hine->cranial_nerves_score / 15) * 100)), 2, '.', '') : '0.00' }}%"></div></div>
                    <p class="mt-2 text-xs font-semibold text-slate-500">{{ $hine->cranial_nerves_score !== null ? number_format(((float) $hine->cranial_nerves_score / 15) * 100, 1).'%' : '—' }} del máximo de la sección</p>
                </div>
                <div class="rounded-2xl border border-slate-100 bg-slate-50 p-4">
                    <p class="min-h-10 text-xs font-bold uppercase leading-5 tracking-wide text-slate-500">Postura</p>
                    <p class="mt-2 text-xl font-black text-slate-900">{{ $hine->posture_score ?? '—' }} <span class="text-xs text-slate-400">/ 18</span></p>
                    <div class="mt-3 h-2.5 overflow-hidden rounded-full bg-slate-200"><div class="h-full rounded-full bg-cyan-500" style="width: {{ $hine->posture_score !== null ? number_format(min(100, max(0, ((float) $hine->posture_score / 18) * 100)), 2, '.', '') : '0.00' }}%"></div></div>
                    <p class="mt-2 text-xs font-semibold text-slate-500">{{ $hine->posture_score !== null ? number_format(((float) $hine->posture_score / 18) * 100, 1).'%' : '—' }} del máximo de la sección</p>
                </div>
                <div class="rounded-2xl border border-slate-100 bg-slate-50 p-4">
                    <p class="min-h-10 text-xs font-bold uppercase leading-5 tracking-wide text-slate-500">Movimientos</p>
                    <p class="mt-2 text-xl font-black text-slate-900">{{ $hine->movements_score ?? '—' }} <span class="text-xs text-slate-400">/ 6</span></p>
                    <div class="mt-3 h-2.5 overflow-hidden rounded-full bg-slate-200"><div class="h-full rounded-full bg-cyan-500" style="width: {{ $hine->movements_score !== null ? number_format(min(100, max(0, ((float) $hine->movements_score / 6) * 100)), 2, '.', '') : '0.00' }}%"></div></div>
                    <p class="mt-2 text-xs font-semibold text-slate-500">{{ $hine->movements_score !== null ? number_format(((float) $hine->movements_score / 6) * 100, 1).'%' : '—' }} del máximo de la sección</p>
                </div>
                <div class="rounded-2xl border border-slate-100 bg-slate-50 p-4">
                    <p class="min-h-10 text-xs font-bold uppercase leading-5 tracking-wide text-slate-500">Tono</p>
                    <p class="mt-2 text-xl font-black text-slate-900">{{ $hine->tone_score ?? '—' }} <span class="text-xs text-slate-400">/ 24</span></p>
                    <div class="mt-3 h-2.5 overflow-hidden rounded-full bg-slate-200"><div class="h-full rounded-full bg-cyan-500" style="width: {{ $hine->tone_score !== null ? number_format(min(100, max(0, ((float) $hine->tone_score / 24) * 100)), 2, '.', '') : '0.00' }}%"></div></div>
                    <p class="mt-2 text-xs font-semibold text-slate-500">{{ $hine->tone_score !== null ? number_format(((float) $hine->tone_score / 24) * 100, 1).'%' : '—' }} del máximo de la sección</p>
                </div>
                <div class="rounded-2xl border border-slate-100 bg-slate-50 p-4">
                    <p class="min-h-10 text-xs font-bold uppercase leading-5 tracking-wide text-slate-500">Reflejos y reacciones</p>
                    <p class="mt-2 text-xl font-black text-slate-900">{{ $hine->reflexes_reactions_score ?? '—' }} <span class="text-xs text-slate-400">/ 15</span></p>
                    <div class="mt-3 h-2.5 overflow-hidden rounded-full bg-slate-200"><div class="h-full rounded-full bg-cyan-500" style="width: {{ $hine->reflexes_reactions_score !== null ? number_format(min(100, max(0, ((float) $hine->reflexes_reactions_score / 15) * 100)), 2, '.', '') : '0.00' }}%"></div></div>
                    <p class="mt-2 text-xs font-semibold text-slate-500">{{ $hine->reflexes_reactions_score !== null ? number_format(((float) $hine->reflexes_reactions_score / 15) * 100, 1).'%' : '—' }} del máximo de la sección</p>
                </div>
                <div class="rounded-2xl border border-violet-100 bg-violet-50/40 p-3">
                    <p class="text-xs font-bold uppercase leading-5 tracking-wide text-violet-700">Puntuación global</p>
                    <div class="mt-1 flex justify-center">
                        <svg id="hine-gauge" viewBox="0 0 240 145" class="h-auto w-full max-w-[220px]" role="img" aria-label="Gauge de puntuación global HINE"></svg>
                    </div>
                    <p class="-mt-1 text-center text-xs font-semibold text-violet-700">{{ $hine->global_score ?? '—' }} / 78</p>
                </div>
            </div>
            <div class="mt-5 rounded-2xl border border-violet-100 bg-violet-50/50 p-4">
                <div class="flex items-center justify-between gap-4"><span class="text-sm font-bold text-slate-700">Puntuación global observada</span><span class="text-sm font-black text-violet-800">{{ $hine->global_score ?? '—' }} / 78</span></div>
                <div class="mt-3 h-4 overflow-hidden rounded-full bg-white ring-1 ring-violet-100"><div class="h-full rounded-full bg-violet-600" style="width: {{ $hine->global_score !== null ? number_format(min(100, max(0, ((float) $hine->global_score / 78) * 100)), 2, '.', '') : '0.00' }}%"></div></div>
            </div>
        </section>

        <section class="rounded-3xl border border-cyan-100 bg-white p-6 shadow-sm">
            <p class="text-xs font-bold uppercase tracking-[0.16em] text-cyan-700">Perfil por secciones</p>
            <h3 class="mt-1 text-lg font-black text-slate-900">Radar HINE</h3>
            <p class="mt-1 text-xs leading-5 text-slate-500">Cada eje se normaliza visualmente contra el máximo de su propia sección. Las puntuaciones HINE originales se muestran debajo.</p>
            <div class="mt-4 flex justify-center">
                <svg id="hine-radar" viewBox="0 0 420 380" class="h-auto w-full max-w-[430px]" role="img" aria-label="Radar de las cinco secciones HINE"></svg>
            </div>
            <div class="mx-auto mt-3 grid max-w-4xl grid-cols-2 gap-2 text-xs sm:grid-cols-5">
                <div class="rounded-xl bg-slate-50 p-2 text-center"><strong>{{ $hine->cranial_nerves_score ?? '—' }}</strong>/15<br><span class="text-slate-500">Pares craneales</span></div>
                <div class="rounded-xl bg-slate-50 p-2 text-center"><strong>{{ $hine->posture_score ?? '—' }}</strong>/18<br><span class="text-slate-500">Postura</span></div>
                <div class="rounded-xl bg-slate-50 p-2 text-center"><strong>{{ $hine->movements_score ?? '—' }}</strong>/6<br><span class="text-slate-500">Movimientos</span></div>
                <div class="rounded-xl bg-slate-50 p-2 text-center"><strong>{{ $hine->tone_score ?? '—' }}</strong>/24<br><span class="text-slate-500">Tono</span></div>
                <div class="rounded-xl bg-slate-50 p-2 text-center"><strong>{{ $hine->reflexes_reactions_score ?? '—' }}</strong>/15<br><span class="text-slate-500">Reflejos</span></div>
            </div>
        </section>

        <section class="rounded-3xl border border-cyan-100 bg-white p-6 shadow-sm">
            <p class="text-xs font-bold uppercase tracking-[0.16em] text-cyan-700">Apoyo para la interpretación</p>
            <h3 class="mt-1 text-lg font-black text-slate-900">{{ $interpretation['source_title'] }}</h3>
            <p class="mt-2 text-sm leading-6 text-slate-600">{{ $interpretation['note'] }}</p>

            <div class="mt-5 grid gap-4 lg:grid-cols-3">
                <div class="rounded-2xl bg-slate-50 p-4">
                    <p class="text-xs font-bold uppercase text-slate-500">{{ $interpretation['global_score_heading'] }}</p>
                    <p class="mt-1 text-xs text-slate-500">{{ $interpretation['global_score_context'] }}</p>
                    <div class="mt-3 flex flex-wrap gap-2">
                        @foreach($interpretation['global_score_ranges'] as $range)
                            <span class="rounded-full bg-white px-3 py-1.5 text-sm font-bold text-slate-700 ring-1 ring-slate-200">{{ $range['label'] }}</span>
                        @endforeach
                    </div>
                </div>
                <div class="rounded-2xl bg-amber-50 p-4">
                    <p class="text-xs font-bold uppercase text-amber-700">{{ $interpretation['asymmetry_heading'] }}</p>
                    <p class="mt-3 text-2xl font-black text-amber-900">≥ {{ $interpretation['asymmetry_attention_threshold'] }}</p>
                    <p class="mt-1 text-xs text-amber-800">Referencia destacada en el apoyo de interpretación.</p>
                </div>
                <div class="rounded-2xl bg-violet-50 p-4">
                    <p class="text-xs font-bold uppercase text-violet-700">{{ $interpretation['high_risk_heading'] }}</p>
                    <div class="mt-3 grid grid-cols-2 gap-2 text-sm">
                        @foreach($interpretation['high_risk_cutoffs_by_age_months'] as $months => $cutoff)
                            <div class="rounded-xl bg-white px-3 py-2 font-semibold text-violet-900">{{ $months }} meses <strong class="float-right">{{ $cutoff }}</strong></div>
                        @endforeach
                    </div>
                </div>
            </div>
            <p class="mt-4 text-xs font-semibold text-slate-500">{{ $interpretation['age_rule'] }}</p>
            <div class="mt-5 border-t border-slate-100 pt-4">
                <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Referencias</p>
                <ol class="mt-2 list-decimal space-y-1 pl-5 text-xs leading-5 text-slate-500">
                    @foreach($interpretation['references'] as $reference)
                        <li>{{ $reference }}</li>
                    @endforeach
                </ol>
            </div>
        </section>
        <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <p class="text-xs font-black uppercase tracking-[0.14em] text-slate-600">Referencia original HINE</p>
                    <p class="mt-1 text-xs text-slate-500">Lámina completa proporcionada por URPE. Se conserva íntegra como respaldo documental.</p>
                </div>
                <a href="{{ asset('images/hine/HINE_interpretationaid_SP.png') }}" target="_blank" rel="noopener" class="rounded-xl border border-cyan-200 bg-cyan-50 px-4 py-2.5 text-xs font-bold text-cyan-800 shadow-sm hover:border-cyan-400 hover:bg-cyan-100">Ver en tamaño completo</a>
            </div>
        </section>
    </div>
@if($assessment->status !== 'finalized')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const button = document.getElementById('hine-finalize-button');
        const form = document.getElementById('hine-finalize-form');
        if (! button || ! form) return;

        button.addEventListener('click', async () => {
            const result = await Swal.fire({
                icon: 'warning',
                title: '¿Finalizar evaluación HINE?',
                text: 'Después de finalizarla quedará cerrada para edición.',
                showCancelButton: true,
                confirmButtonText: 'Sí, finalizar',
                cancelButtonText: 'Cancelar',
                reverseButtons: true
            });

            if (result.isConfirmed) form.submit();
        });
    });
</script>
@endif
</x-app-shell>
