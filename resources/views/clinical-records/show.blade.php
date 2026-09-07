<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Expediente clínico — {{ $patient->full_name }} — URPE Gestión Clínica</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 text-slate-900 antialiased">
<header class="border-b border-slate-200 bg-white">
    <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4 lg:px-8">
        <div class="flex items-center gap-4">
            <img src="{{ asset('images/brand/urpe-logo.png') }}" alt="URPE" class="h-14 w-auto object-contain">
            <div class="hidden sm:block">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-cyan-700">Gestión Clínica</p>
                <h1 class="text-xl font-bold">Expediente clínico base</h1>
            </div>
        </div>
        <div class="flex items-center gap-3">
            @can('patients.view')
                <a href="{{ route('patients.show', $patient) }}" class="text-sm font-semibold text-slate-600 hover:text-cyan-700">Ficha administrativa</a>
            @endcan
            <a href="{{ route('dashboard') }}" class="rounded-xl bg-slate-800 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-900">Dashboard</a>
        </div>
    </div>
</header>

<main class="mx-auto max-w-7xl px-6 py-10 lg:px-8">
    <section class="mb-6 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <p class="font-mono text-xs font-semibold uppercase tracking-[0.16em] text-cyan-700">{{ $patient->folio }}</p>
                <h2 class="mt-2 text-3xl font-bold">{{ $patient->full_name }}</h2>
                <p class="mt-2 text-sm text-slate-500">Información clínica basal. Las notas de evolución por sesión se registrarán en un módulo independiente.</p>
            </div>
            <span class="inline-flex w-fit rounded-full px-3 py-1 text-xs font-semibold {{ $clinicalRecord ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">
                {{ $clinicalRecord ? 'Expediente iniciado' : 'Pendiente de iniciar' }}
            </span>
        </div>

        @if($clinicalRecord)
            <div class="mt-5 flex flex-wrap gap-x-6 gap-y-2 border-t border-slate-100 pt-4 text-xs text-slate-500">
                <span>Creado: {{ $clinicalRecord->created_at?->format('d/m/Y H:i') }}</span>
                <span>Última actualización: {{ $clinicalRecord->updated_at?->format('d/m/Y H:i') }}</span>
                @if($clinicalRecord->updater)<span>Actualizó: {{ $clinicalRecord->updater->name }}</span>@endif
            </div>
        @endif
    </section>

    @can('clinical_records.manage')
        <form method="POST" action="{{ route('clinical-records.update', $patient) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <div class="mb-6">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-cyan-700">Antecedentes</p>
                    <h3 class="mt-1 text-xl font-bold">Historia clínica basal</h3>
                </div>

                <div class="grid gap-6 lg:grid-cols-2">
                    <div>
                        <label for="medical_history" class="mb-2 block text-sm font-semibold text-slate-700">Antecedentes médicos</label>
                        <textarea id="medical_history" name="medical_history" rows="7" maxlength="10000" class="w-full" placeholder="Enfermedades previas, cirugías, hospitalizaciones, medicamentos relevantes...">{{ old('medical_history', $clinicalRecord?->medical_history) }}</textarea>
                        @error('medical_history')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="prenatal_perinatal_history" class="mb-2 block text-sm font-semibold text-slate-700">Antecedentes prenatales y perinatales</label>
                        <textarea id="prenatal_perinatal_history" name="prenatal_perinatal_history" rows="7" maxlength="10000" class="w-full" placeholder="Embarazo, nacimiento, edad gestacional, complicaciones relevantes...">{{ old('prenatal_perinatal_history', $clinicalRecord?->prenatal_perinatal_history) }}</textarea>
                        @error('prenatal_perinatal_history')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="developmental_history" class="mb-2 block text-sm font-semibold text-slate-700">Antecedentes del desarrollo</label>
                        <textarea id="developmental_history" name="developmental_history" rows="7" maxlength="10000" class="w-full" placeholder="Hitos motores, lenguaje, desarrollo funcional y datos relevantes...">{{ old('developmental_history', $clinicalRecord?->developmental_history) }}</textarea>
                        @error('developmental_history')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="family_history" class="mb-2 block text-sm font-semibold text-slate-700">Antecedentes familiares</label>
                        <textarea id="family_history" name="family_history" rows="7" maxlength="10000" class="w-full" placeholder="Antecedentes familiares relevantes para la atención...">{{ old('family_history', $clinicalRecord?->family_history) }}</textarea>
                        @error('family_history')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                    </div>
                </div>
            </section>

            <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <div class="mb-6">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-cyan-700">Plan clínico</p>
                    <h3 class="mt-1 text-xl font-bold">Diagnóstico y objetivos</h3>
                </div>

                <div class="grid gap-6 lg:grid-cols-2">
                    <div>
                        <label for="diagnoses" class="mb-2 block text-sm font-semibold text-slate-700">Diagnósticos</label>
                        <textarea id="diagnoses" name="diagnoses" rows="8" maxlength="10000" class="w-full" placeholder="Diagnósticos clínicos o funcionales relevantes...">{{ old('diagnoses', $clinicalRecord?->diagnoses) }}</textarea>
                        @error('diagnoses')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="therapeutic_objectives" class="mb-2 block text-sm font-semibold text-slate-700">Objetivos terapéuticos</label>
                        <textarea id="therapeutic_objectives" name="therapeutic_objectives" rows="8" maxlength="10000" class="w-full" placeholder="Objetivos generales del proceso terapéutico...">{{ old('therapeutic_objectives', $clinicalRecord?->therapeutic_objectives) }}</textarea>
                        @error('therapeutic_objectives')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                    </div>
                    <div class="lg:col-span-2">
                        <label for="general_observations" class="mb-2 block text-sm font-semibold text-slate-700">Observaciones clínicas generales</label>
                        <textarea id="general_observations" name="general_observations" rows="7" maxlength="10000" class="w-full" placeholder="Observaciones basales relevantes que no correspondan a una sesión específica...">{{ old('general_observations', $clinicalRecord?->general_observations) }}</textarea>
                        @error('general_observations')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="mt-6 flex flex-col gap-3 border-t border-slate-100 pt-5 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-xs leading-5 text-slate-500">El historial de auditoría registra quién modificó el expediente y qué secciones cambiaron, pero no copia el contenido clínico sensible.</p>
                    <button type="submit" class="rounded-xl bg-cyan-700 px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-cyan-800">
                        {{ $clinicalRecord ? 'Guardar cambios' : 'Iniciar expediente clínico' }}
                    </button>
                </div>
            </section>
        </form>
    @else
        <div class="space-y-6">
            @php
                $sections = [
                    'Antecedentes médicos' => $clinicalRecord?->medical_history,
                    'Antecedentes prenatales y perinatales' => $clinicalRecord?->prenatal_perinatal_history,
                    'Antecedentes del desarrollo' => $clinicalRecord?->developmental_history,
                    'Antecedentes familiares' => $clinicalRecord?->family_history,
                    'Diagnósticos' => $clinicalRecord?->diagnoses,
                    'Objetivos terapéuticos' => $clinicalRecord?->therapeutic_objectives,
                    'Observaciones clínicas generales' => $clinicalRecord?->general_observations,
                ];
            @endphp

            @if(!$clinicalRecord)
                <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                    <p class="text-sm text-slate-500">El expediente clínico base todavía no ha sido iniciado.</p>
                </section>
            @else
                @foreach($sections as $label => $value)
                    <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                        <h3 class="text-sm font-bold uppercase tracking-wide text-slate-500">{{ $label }}</h3>
                        <p class="mt-3 whitespace-pre-line text-sm leading-7 text-slate-700">{{ $value ?: 'Sin información registrada.' }}</p>
                    </section>
                @endforeach
            @endif
        </div>
    @endcan
</main>

<x-sweet-alerts />
</body>
</html>
