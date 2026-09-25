<x-app-shell title="Captura HINE" eyebrow="{{ $patient->full_name }}">
    <x-slot:actions>
        <a href="{{ route('patients.hine-assessments.index', $patient) }}" class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-700">Historial HINE</a>
    </x-slot:actions>

    <form method="POST" action="{{ route('patients.hine-assessments.update', [$patient, $assessment]) }}" class="space-y-6" data-hine-wizard data-hine-initial-step="{{ session('hine_step', 0) }}">
        <input type="hidden" name="return_step" value="0" data-hine-return-step>
        @csrf
        @method('PUT')

        <section class="rounded-3xl border border-cyan-100 bg-gradient-to-r from-cyan-50 via-white to-violet-50 p-6 shadow-sm">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.16em] text-cyan-700">HINE {{ $assessment->instrument_version }}</p>
                    <h2 class="mt-1 text-xl font-black text-slate-900">Evaluación neurológica</h2>
                    <p class="mt-2 text-sm text-slate-600">Escala URPE: 0 a 3 en incrementos de 0.5. Los medios puntos representan juicio clínico y no agregan criterios que no estén en la fuente.</p>
                </div>
                <div class="rounded-2xl bg-white px-4 py-3 text-right ring-1 ring-slate-100">
                    <p class="text-xs font-bold uppercase text-slate-400" data-hine-score-label>Puntuación actual</p>
                    <p class="text-2xl font-black text-violet-700"><span data-hine-live-score>{{ number_format((float) ($hine->global_score ?? 0), 1) }}</span> <span class="text-sm text-slate-400">/ 78</span></p>
                    <p class="mt-1 text-xs font-semibold text-slate-500"><span data-hine-live-asymmetries>{{ $hine->asymmetry_count }}</span> asimetrías registradas</p>
                    <p class="mt-1 hidden text-[11px] font-semibold text-amber-700" data-hine-provisional>Provisional · guarda el borrador para registrar los cambios</p>
                </div>
            </div>
        </section>

        @php($stepLabels = collect($sections)->pluck('label')->values()->all())
        <nav class="sticky top-3 z-20 rounded-2xl border border-slate-200 bg-white/95 p-3 shadow-lg backdrop-blur">
            <div class="flex gap-2 overflow-x-auto pb-1">
                <button type="button" data-hine-go="0" data-hine-active-class="bg-cyan-600 text-white" data-hine-inactive-class="bg-slate-100 text-slate-600" class="shrink-0 rounded-xl px-3 py-2 text-xs font-bold">Datos</button>
                @foreach($stepLabels as $stepIndex => $stepLabel)
                    <button type="button" data-hine-go="{{ $stepIndex + 1 }}" data-hine-active-class="bg-violet-600 text-white" data-hine-inactive-class="bg-slate-100 text-slate-600" class="shrink-0 rounded-xl px-3 py-2 text-xs font-bold">{{ $stepIndex + 1 }}. {{ $stepLabel }}</button>
                @endforeach
                <button type="button" data-hine-go="{{ count($stepLabels) + 1 }}" data-hine-active-class="bg-emerald-600 text-white" data-hine-inactive-class="bg-slate-100 text-slate-600" class="shrink-0 rounded-xl px-3 py-2 text-xs font-bold">Hitos</button>
                <button type="button" data-hine-go="{{ count($stepLabels) + 2 }}" data-hine-active-class="bg-amber-500 text-white" data-hine-inactive-class="bg-slate-100 text-slate-600" class="shrink-0 rounded-xl px-3 py-2 text-xs font-bold">Comportamiento</button>
            </div>
        </nav>

        <div data-hine-step="0">
        <section class="rounded-3xl border border-slate-100 bg-white p-6 shadow-sm">
            <h3 class="text-lg font-black text-slate-900">Datos del examen</h3>
            <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <div class="rounded-xl bg-slate-50 p-3 text-sm"><span class="font-bold">Nombre y apellidos:</span> {{ $patient->full_name }}</div>
                <div class="rounded-xl bg-slate-50 p-3 text-sm"><span class="font-bold">Fecha de nacimiento:</span> {{ $patient->date_of_birth?->format('d/m/Y') ?: '—' }}</div>
                <label class="text-sm font-bold text-slate-700">Fecha de examen<input type="date" name="examination_date" required value="{{ old('examination_date', $assessment->examination_date->toDateString()) }}" class="mt-2 w-full rounded-xl border-slate-200"></label>
                <label class="text-sm font-bold text-slate-700">Edad gestacional<input type="text" name="gestational_age" maxlength="100" value="{{ old('gestational_age', $hine->gestational_age) }}" class="mt-2 w-full rounded-xl border-slate-200"></label>
                <label class="text-sm font-bold text-slate-700">Edad cronológica<input type="text" name="chronological_age" maxlength="100" value="{{ old('chronological_age', $hine->chronological_age) }}" class="mt-2 w-full rounded-xl border-slate-200"></label>
                <label class="text-sm font-bold text-slate-700">Edad corregida<input type="text" name="corrected_age" maxlength="100" value="{{ old('corrected_age', $hine->corrected_age) }}" class="mt-2 w-full rounded-xl border-slate-200"></label>
                <label class="text-sm font-bold text-slate-700">Perímetro cefálico<input type="text" name="head_circumference" maxlength="100" value="{{ old('head_circumference', $hine->head_circumference) }}" class="mt-2 w-full rounded-xl border-slate-200"></label>
                <label class="text-sm font-bold text-slate-700 sm:col-span-2 lg:col-span-3">Comentarios<textarea name="general_comments" rows="3" maxlength="5000" class="mt-2 w-full rounded-xl border-slate-200">{{ old('general_comments', $hine->general_comments) }}</textarea></label>
            </div>
            <p class="mt-4 rounded-xl bg-amber-50 px-4 py-3 text-xs leading-5 text-amber-900">{{ \App\Support\HineInstrument::SOURCE_SCORING_NOTE }}</p>
        </section>
        <div class="mt-4 flex justify-end"><button type="button" data-hine-go="1" class="rounded-xl bg-cyan-600 px-5 py-2.5 text-sm font-bold text-white">Comenzar evaluación →</button></div>
        </div>

        @foreach($sections as $sectionKey => $section)
            <div data-hine-step="{{ $loop->iteration }}">
            <section class="overflow-hidden rounded-3xl border border-slate-100 bg-white shadow-sm">
                <div class="flex items-center justify-between gap-4 border-b border-slate-100 bg-slate-50/70 px-6 py-4">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.14em] text-violet-600">Sección</p>
                        <h3 class="mt-1 text-lg font-bold text-slate-900">{{ $section['label'] }}</h3>
                        @isset($section['note'])<p class="mt-1 text-xs text-slate-500">{{ $section['note'] }}</p>@endisset
                    </div>
                    <span class="rounded-full bg-violet-100 px-3 py-1 text-xs font-bold text-violet-800">Máx. {{ $section['maximum'] }}</span>
                </div>

                <div class="divide-y divide-slate-100">
                    @foreach($section['items'] as $item)
                        @php($response = $responses->get($item['key']))
                        <article class="p-5 sm:p-6">
                            <div class="grid gap-5 xl:grid-cols-[minmax(0,1.15fr)_minmax(360px,.85fr)]">
                                <div>
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h4 class="font-bold text-slate-800">{{ $item['label'] }}</h4>
                                        @if($item['laterality'] ?? false)<span class="rounded-full bg-amber-50 px-2 py-0.5 text-[11px] font-bold text-amber-700">D / I</span>@endif
                                    </div>
                                    @isset($item['instruction'])<p class="mt-1 text-sm text-slate-500">{{ $item['instruction'] }}</p>@endisset
                                    @isset($item['age_note'])<p class="mt-1 text-xs font-semibold text-violet-700">{{ $item['age_note'] }}</p>@endisset
                                    @isset($item['sites'])<p class="mt-1 text-xs font-semibold text-slate-600">{{ implode(' · ', $item['sites']) }}</p>@endisset

                                    @if($item['key'] === 'head_sitting')
                                        <div class="mt-4 overflow-x-auto rounded-2xl border border-slate-300 bg-white">
                                            <table class="min-w-[760px] w-full table-fixed text-xs text-slate-700">
                                                <caption class="sr-only">Referencia de la proforma HINE para Cabeza en sedestación</caption>
                                                <thead class="bg-slate-100 font-bold text-slate-900">
                                                    <tr>
                                                        <th class="border-b border-r border-slate-300 px-3 py-2">Puntuación 3</th>
                                                        <th class="w-14 border-b border-r border-slate-300 px-2 py-2">2</th>
                                                        <th class="border-b border-r border-slate-300 px-3 py-2">Puntuación 1</th>
                                                        <th class="border-b border-slate-300 px-3 py-2">Puntuación 0</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr class="align-top">
                                                        <td class="border-r border-slate-300 px-3 py-3 text-center">
                                                            <img src="{{ asset('images/hine/head_sitting_score_3.png') }}" alt="Cabeza recta en la línea media" class="mx-auto h-20 w-auto max-w-full object-contain">
                                                            <p class="mt-2">Recta; en la línea media</p>
                                                        </td>
                                                        <td class="border-r border-slate-300"></td>
                                                        <td class="border-r border-slate-300 px-3 py-3 text-center">
                                                            <img src="{{ asset('images/hine/head_sitting_score_1.png') }}" alt="Cabeza ligeramente inclinada" class="mx-auto h-20 w-auto max-w-full object-contain">
                                                            <p class="mt-2">Ligeramente inclinada hacia un lado o hacia atrás o delante</p>
                                                        </td>
                                                        <td class="px-3 py-3 text-center">
                                                            <img src="{{ asset('images/hine/head_sitting_score_0.png') }}" alt="Cabeza marcadamente inclinada" class="mx-auto h-20 w-auto max-w-full object-contain">
                                                            <p class="mt-2">Marcadamente inclinada hacia un lado o atrás o delante</p>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <p class="border-t border-slate-200 bg-cyan-50/50 px-3 py-2 text-[11px] font-semibold text-cyan-800">Referencia visual organizada según la proforma HINE fuente.</p>
                                        </div>
                                    @elseif($item['key'] === 'trunk_sitting')
                                        <div class="mt-4 overflow-x-auto rounded-2xl border border-slate-300 bg-white">
                                            <table class="min-w-[760px] w-full table-fixed text-xs text-slate-700">
                                                <caption class="sr-only">Referencia de la proforma HINE para Tronco en sedestación</caption>
                                                <thead class="bg-slate-100 font-bold text-slate-900">
                                                    <tr>
                                                        <th class="border-b border-r border-slate-300 px-3 py-2">Puntuación 3</th>
                                                        <th class="w-14 border-b border-r border-slate-300 px-2 py-2">2</th>
                                                        <th class="border-b border-r border-slate-300 px-3 py-2">Puntuación 1</th>
                                                        <th class="border-b border-slate-300 px-3 py-2">Puntuación 0</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr class="align-top">
                                                        <td class="border-r border-slate-300 px-3 py-3 text-center">
                                                            <img src="{{ asset('images/hine/trunk_sitting_score_3.png') }}" alt="Tronco recto en sedestación" class="mx-auto h-20 w-auto max-w-full object-contain">
                                                            <p class="mt-2">Recto</p>
                                                        </td>
                                                        <td class="border-r border-slate-300"></td>
                                                        <td class="border-r border-slate-300 px-3 py-3 text-center">
                                                            <img src="{{ asset('images/hine/trunk_sitting_score_1.png') }}" alt="Tronco ligeramente curvado o inclinado lateralmente" class="mx-auto h-20 w-auto max-w-full object-contain">
                                                            <p class="mt-2">Ligeramente curvado o inclinado lateralmente</p>
                                                        </td>
                                                        <td class="px-3 py-3 text-center">
                                                            <div class="mx-auto h-20 w-full max-w-[260px] overflow-hidden">
                                                                <img src="{{ asset('images/hine/trunk_sitting.png') }}" alt="Tronco muy curvado, hiperextendido o inclinado lateralmente" class="h-full max-w-none" style="width: 166.667%; transform: translateX(-40%); transform-origin: left center;">
                                                            </div>
                                                            <p class="mt-2">Muy curvado · Hiperextendido · Inclinado lateralmente</p>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <p class="border-t border-slate-200 bg-cyan-50/50 px-3 py-2 text-[11px] font-semibold text-cyan-800">Referencia visual organizada según la proforma HINE fuente.</p>
                                        </div>
                                    @else
                                        @if(isset($anchors[$item['key']]))
                                            <div class="mt-3 grid gap-2 md:grid-cols-2">
                                                @foreach($anchors[$item['key']] as $anchorScore => $anchorText)
                                                    <div class="rounded-xl bg-slate-50 px-3 py-2 text-xs leading-5 text-slate-600"><strong class="text-slate-800">{{ $anchorScore }}:</strong> {{ $anchorText }}</div>
                                                @endforeach
                                            </div>
                                        @endif

                                        @if(isset($visuals[$item['key']]))
                                            <figure class="mt-4 rounded-2xl border border-cyan-100 bg-cyan-50/40 p-4">
                                                <img src="{{ asset('images/hine/'.$visuals[$item['key']]) }}" alt="Referencia visual HINE: {{ $item['label'] }}" class="max-h-52 w-auto max-w-full object-contain" loading="lazy" decoding="async">
                                                <figcaption class="mt-2 text-[11px] font-semibold text-cyan-800">Referencia visual del instrumento HINE.</figcaption>
                                            </figure>
                                        @endif
                                    @endif
                                </div>

                                <div class="space-y-4 rounded-2xl border border-slate-100 bg-slate-50/50 p-4">
                                    <fieldset>
                                        <legend class="text-xs font-bold uppercase tracking-wide text-slate-500">Puntuación</legend>
                                        <div class="mt-2 grid grid-cols-4 gap-2 sm:grid-cols-7">
                                            @foreach($scores as $score)
                                                <label class="cursor-pointer">
                                                    <input type="radio" name="responses[{{ $item['key'] }}][score]" value="{{ $score }}" class="peer sr-only" @checked((float) old('responses.'.$item['key'].'.score', $response?->score) === (float) $score)>
                                                    <span class="block overflow-hidden rounded-xl border border-slate-200 text-center text-sm font-bold text-slate-600 peer-checked:border-cyan-500 peer-checked:bg-cyan-50 peer-checked:text-cyan-800">
                                                        <span class="block px-2 py-2">{{ $score }}</span>
                                                    </span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </fieldset>

                                    @if($item['laterality'] ?? false)
                                        <label class="flex items-center gap-2 text-sm font-semibold text-amber-800">
                                            <input type="hidden" name="responses[{{ $item['key'] }}][asymmetry]" value="0">
                                            <input type="checkbox" name="responses[{{ $item['key'] }}][asymmetry]" value="1" class="rounded border-amber-300 text-amber-600" @checked((bool) old('responses.'.$item['key'].'.asymmetry', $response?->asymmetry))>
                                            Registrar asimetría
                                        </label>
                                    @endif

                                    <details class="group rounded-xl border border-slate-200 bg-white" @if(old('responses.'.$item['key'].'.comments', $response?->comments)) open @endif>
                                        <summary class="cursor-pointer list-none px-3 py-2 text-xs font-bold text-slate-600">+ Agregar observación clínica</summary>
                                        <div class="border-t border-slate-100 p-3">
                                            <textarea name="responses[{{ $item['key'] }}][comments]" rows="2" maxlength="2000" placeholder="Observaciones de este reactivo…" class="w-full rounded-xl border-slate-200 text-sm normal-case tracking-normal">{{ old('responses.'.$item['key'].'.comments', $response?->comments) }}</textarea>
                                        </div>
                                    </details>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>
            <div class="mt-4 flex justify-between gap-3">
                <button type="button" data-hine-prev class="rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-bold text-slate-700">← Anterior</button>
                <button type="button" data-hine-next class="rounded-xl bg-violet-600 px-5 py-2.5 text-sm font-bold text-white">Siguiente →</button>
            </div>
            </div>
        @endforeach

        <div data-hine-step="{{ count($stepLabels) + 1 }}">
        <section class="overflow-hidden rounded-3xl border border-emerald-100 bg-white shadow-sm">
            <div class="border-b border-emerald-100 bg-emerald-50/70 px-6 py-4">
                <p class="text-xs font-bold uppercase tracking-[0.14em] text-emerald-700">Sección 2 · No puntúa</p>
                <h3 class="mt-1 text-lg font-bold text-slate-900">Hitos motores</h3>
                <p class="mt-1 text-sm text-slate-500">Registrar lo observado y la edad de adquisición. Observe las asimetrías según las indicaciones del instrumento.</p>
            </div>
            <div class="divide-y divide-slate-100">
                @foreach($motorMilestones as $item)
                    @php($response = $responses->get($item['key']))
                    <article class="grid gap-4 p-6 md:grid-cols-[minmax(0,1fr)_minmax(0,1.4fr)]">
                        <div>
                            <h4 class="font-bold text-slate-800">{{ $item['label'] }}</h4>
                            @isset($item['instruction'])<p class="mt-1 text-sm text-slate-500">{{ $item['instruction'] }}</p>@endisset
                            @isset($item['options'])
                                <div class="mt-3 space-y-2">
                                    @foreach($item['options'] as $optionIndex => $option)
                                        <div class="rounded-xl bg-emerald-50/60 px-3 py-2 text-xs leading-5 text-slate-700">
                                            <span class="font-bold text-emerald-800">{{ $option }}</span>
                                            @if(isset($item['normal_ages'][$optionIndex]))
                                                <span class="ml-1 text-slate-500">· {{ $item['normal_ages'][$optionIndex] }}</span>
                                            @endif
                                        </div>
                                    @endforeach
                                    @isset($item['age_note'])<p class="text-xs font-semibold text-emerald-800">{{ $item['age_note'] }}</p>@endisset
                                </div>
                            @endisset
                            @if(isset($visuals[$item['key']]))<figure class="mt-3 rounded-2xl border border-cyan-100 bg-cyan-50/40 p-3">
                                <img src="{{ asset('images/hine/'.$visuals[$item['key']]) }}" alt="Referencia visual HINE: {{ $item['label'] }}" class="max-h-48 w-auto max-w-full object-contain" loading="lazy" decoding="async">
                            </figure>@endif
                        </div>
                        <div class="grid gap-3 sm:grid-cols-2">
                            <label class="text-xs font-bold uppercase tracking-wide text-slate-500">Observado
                                <input type="text" name="motor[{{ $item['key'] }}][observed]" maxlength="500" value="{{ old('motor.'.$item['key'].'.observed', data_get($response?->response_data, 'observed')) }}" class="mt-2 w-full rounded-xl border-slate-200 text-sm normal-case tracking-normal">
                            </label>
                            <label class="text-xs font-bold uppercase tracking-wide text-slate-500">Edad de adquisición
                                <input type="text" name="motor[{{ $item['key'] }}][acquisition_age]" maxlength="100" value="{{ old('motor.'.$item['key'].'.acquisition_age', data_get($response?->response_data, 'acquisition_age')) }}" class="mt-2 w-full rounded-xl border-slate-200 text-sm normal-case tracking-normal">
                            </label>
                            <label class="text-xs font-bold uppercase tracking-wide text-slate-500 sm:col-span-2">Comentarios
                                <textarea name="motor[{{ $item['key'] }}][comments]" rows="2" maxlength="2000" class="mt-2 w-full rounded-xl border-slate-200 text-sm normal-case tracking-normal">{{ old('motor.'.$item['key'].'.comments', $response?->comments) }}</textarea>
                            </label>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
        <div class="mt-4 flex justify-between gap-3">
            <button type="button" data-hine-prev class="rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-bold text-slate-700">← Anterior</button>
            <button type="button" data-hine-next class="rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-bold text-white">Comportamiento →</button>
        </div>
        </div>

        <div data-hine-step="{{ count($stepLabels) + 2 }}">
        <section class="overflow-hidden rounded-3xl border border-amber-100 bg-white shadow-sm">
            <div class="border-b border-amber-100 bg-amber-50/70 px-6 py-4">
                <p class="text-xs font-bold uppercase tracking-[0.14em] text-amber-700">Sección 3 · No puntúa</p>
                <h3 class="mt-1 text-lg font-bold text-slate-900">Comportamiento</h3>
                <p class="mt-1 text-sm text-slate-500">Estas respuestas se registran sin incorporarse a la puntuación neurológica global.</p>
            </div>
            <div class="divide-y divide-slate-100">
                @foreach($behaviorItems as $item)
                    @php($response = $responses->get($item['key']))
                    <article class="p-6">
                        <h4 class="font-bold text-slate-800">{{ $item['label'] }}</h4>
                        <div class="mt-3 grid gap-2 md:grid-cols-2 xl:grid-cols-3">
                            @foreach($item['options'] as $optionIndex => $option)
                                <label class="cursor-pointer">
                                    <input type="radio" name="behavior[{{ $item['key'] }}][option]" value="{{ $optionIndex }}" class="peer sr-only" @checked((string) old('behavior.'.$item['key'].'.option', data_get($response?->response_data, 'option')) === (string) $optionIndex)>
                                    <span class="block h-full rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-600 peer-checked:border-amber-500 peer-checked:bg-amber-50 peer-checked:font-semibold peer-checked:text-amber-900">{{ $option }}</span>
                                </label>
                            @endforeach
                        </div>
                        <label class="mt-3 block text-xs font-bold uppercase tracking-wide text-slate-500">Comentarios
                            <textarea name="behavior[{{ $item['key'] }}][comments]" rows="2" maxlength="2000" class="mt-2 w-full rounded-xl border-slate-200 text-sm normal-case tracking-normal">{{ old('behavior.'.$item['key'].'.comments', $response?->comments) }}</textarea>
                        </label>
                    </article>
                @endforeach
            </div>
        </section>
        <div class="mt-4 flex justify-start"><button type="button" data-hine-prev class="rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-bold text-slate-700">← Anterior</button></div>
        </div>

        @if($errors->any())
            <div class="rounded-2xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800">{{ $errors->first() }}</div>
        @endif

        <div class="sticky bottom-4 flex flex-wrap justify-end gap-3 rounded-2xl bg-white/90 p-3 shadow-lg backdrop-blur">
            <button class="rounded-xl border border-cyan-200 bg-white px-6 py-3 text-sm font-bold text-cyan-800">Guardar borrador</button>
        </div>
    </form>

    <form method="POST" action="{{ route('patients.hine-assessments.finalize', [$patient, $assessment]) }}" class="mt-3 flex justify-end" onsubmit="return confirm('¿Finalizar esta evaluación HINE? Después quedará cerrada para edición.');">
        @csrf
        <button class="rounded-xl bg-gradient-to-r from-violet-600 to-fuchsia-600 px-6 py-3 text-sm font-bold text-white shadow-lg">Finalizar evaluación HINE</button>
    </form>
</x-app-shell>
