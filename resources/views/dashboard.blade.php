<x-layouts::app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 p-6">

        {{-- Filtro de fechas --}}
        <div class="flex flex-wrap items-end gap-3">
            <form method="GET" action="{{ route('dashboard') }}" class="flex flex-wrap items-end gap-3">
                <div class="flex flex-col gap-1">
                    <label class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Desde</label>
                    <input
                        type="date"
                        name="desde"
                        value="{{ $desde->toDateString() }}"
                        class="rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm text-zinc-800 shadow-sm dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100"
                    >
                </div>
                <div class="flex flex-col gap-1">
                    <label class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Hasta</label>
                    <input
                        type="date"
                        name="hasta"
                        value="{{ $hasta->toDateString() }}"
                        class="rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm text-zinc-800 shadow-sm dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100"
                    >
                </div>
                <flux:button type="submit" variant="primary">Filtrar</flux:button>
            </form>
        </div>

        {{-- Tarjetas de totales --}}
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-2">
            <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                <p class="text-sm text-zinc-500 dark:text-zinc-400">Cotizaciones en el período</p>
                <p class="mt-1 text-3xl font-semibold text-zinc-900 dark:text-zinc-100">
                    {{ $totalCotizaciones }}
                </p>
                <p class="mt-1 text-xs text-zinc-400 dark:text-zinc-500">
                    {{ $desde->translatedFormat('d M Y') }} — {{ $hasta->translatedFormat('d M Y') }}
                </p>
            </div>
            <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                <p class="text-sm text-zinc-500 dark:text-zinc-400">Citas en el período</p>
                <p class="mt-1 text-3xl font-semibold text-zinc-900 dark:text-zinc-100">
                    {{ $totalCitas }}
                </p>
                <p class="mt-1 text-xs text-zinc-400 dark:text-zinc-500">
                    {{ $desde->translatedFormat('d M Y') }} — {{ $hasta->translatedFormat('d M Y') }}
                </p>
            </div>
        </div>

        {{-- Gráfica de serie temporal --}}
        @php
            $labelsChart    = $periodos->map(fn($p) => $p['label'])->values();
            $cotizChart     = $periodos->map(fn($p) => $p['cotizaciones'])->values();
            $citasChart     = $periodos->map(fn($p) => $p['citas'])->values();
        @endphp

        <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <p class="mb-4 text-sm font-medium text-zinc-700 dark:text-zinc-300">
                Actividad por {{ $agrupar === 'dia' ? 'día' : 'semana' }}
            </p>
            <div class="mb-4 flex gap-4 text-xs text-zinc-500 dark:text-zinc-400">
                <span class="flex items-center gap-1.5">
                    <span class="inline-block h-2.5 w-2.5 rounded-full bg-blue-500"></span>
                    Cotizaciones
                </span>
                <span class="flex items-center gap-1.5">
                    <span class="inline-block h-2.5 w-2.5 rounded-full bg-emerald-500"></span>
                    Citas
                </span>
            </div>
            <div style="position:relative; height:220px;">
                <canvas id="actividadChart"></canvas>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
        <script>
        (function() {
            const labels    = @json($labelsChart);
            const cotizData = @json($cotizChart);
            const citasData = @json($citasChart);

            const ctx = document.getElementById('actividadChart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Cotizaciones',
                            data: cotizData,
                            borderColor: '#3b82f6',
                            backgroundColor: 'rgba(59,130,246,0.1)',
                            borderWidth: 2,
                            pointRadius: 3,
                            tension: 0.3,
                            fill: true,
                        },
                        {
                            label: 'Citas',
                            data: citasData,
                            borderColor: '#10b981',
                            backgroundColor: 'rgba(16,185,129,0.1)',
                            borderWidth: 2,
                            pointRadius: 3,
                            tension: 0.3,
                            fill: true,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { font: { size: 11 }, color: '#a1a1aa' }
                        },
                        y: {
                            beginAtZero: true,
                            ticks: { stepSize: 1, precision: 0, color: '#a1a1aa', font: { size: 11 } },
                            grid: { color: 'rgba(0,0,0,0.05)' }
                        }
                    }
                }
            });
        })();
        </script>

        {{-- Top centros e instituciones --}}
        <div class="grid gap-4 lg:grid-cols-2">

            {{-- Top centros --}}
            <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                <p class="mb-3 text-sm font-medium text-zinc-700 dark:text-zinc-300">Top centros de agenda</p>
                @forelse($topCentros as $centro)
                    <div class="flex items-center justify-between py-2 {{ !$loop->last ? 'border-b border-zinc-100 dark:border-zinc-800' : '' }}">
                        <span class="text-sm text-zinc-700 dark:text-zinc-300">{{ $centro->nombre }}</span>
                        <span class="rounded-full bg-blue-50 px-2.5 py-0.5 text-xs font-medium text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">
                            {{ $centro->citas_count }} cita{{ $centro->citas_count !== 1 ? 's' : '' }}
                        </span>
                    </div>
                @empty
                    <p class="text-sm text-zinc-400 dark:text-zinc-500">Sin datos en el período</p>
                @endforelse
            </div>

            {{-- Top instituciones --}}
            <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                <p class="mb-3 text-sm font-medium text-zinc-700 dark:text-zinc-300">Top instituciones</p>
                @forelse($topInstituciones as $hospital)
                    <div class="flex items-center justify-between py-2 {{ !$loop->last ? 'border-b border-zinc-100 dark:border-zinc-800' : '' }}">
                        <span class="text-sm text-zinc-700 dark:text-zinc-300">{{ $hospital->nombre_corto ?? $hospital->nombre }}</span>
                        <span class="rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-medium text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300">
                            {{ $hospital->cotizaciones_count }} cot.
                        </span>
                    </div>
                @empty
                    <p class="text-sm text-zinc-400 dark:text-zinc-500">Sin datos en el período</p>
                @endforelse
            </div>

        </div>
    </div>
</x-layouts::app>