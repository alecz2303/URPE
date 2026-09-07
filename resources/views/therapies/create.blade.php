<x-app-shell title="Nueva terapia" eyebrow="Catálogo clínico">
    <x-slot:actions>
        <a href="{{ route('therapies.index') }}" class="rounded-xl border border-cyan-200 bg-white px-4 py-2.5 text-sm font-bold text-cyan-800 shadow-sm hover:bg-cyan-50">Cancelar</a>
    </x-slot:actions>

    <div class="mx-auto max-w-4xl">
        <section class="overflow-hidden rounded-3xl border border-violet-200 bg-white shadow-sm">
            <div class="h-1.5 bg-gradient-to-r from-violet-500 via-fuchsia-400 to-cyan-400"></div>
            <div class="border-b border-violet-100 bg-gradient-to-r from-violet-50 via-fuchsia-50/70 to-cyan-50 px-6 py-6 sm:px-8">
                <span class="inline-flex rounded-full bg-white/80 px-3 py-1 text-xs font-bold uppercase tracking-[0.14em] text-violet-700 ring-1 ring-violet-200">Nueva configuración</span>
                <h2 class="mt-3 text-xl font-black text-slate-900">Configura cómo funcionará esta terapia en agenda</h2>
                <p class="mt-2 text-sm leading-6 text-slate-600">Duración, número de terapeutas, color e información operativa se usarán directamente al programar citas.</p>
            </div>
            <div class="p-6 sm:p-8">
                <form method="POST" action="{{ route('therapies.store') }}">
                    @csrf
                    @include('therapies._form')
                </form>
            </div>
        </section>
    </div>
</x-app-shell>
