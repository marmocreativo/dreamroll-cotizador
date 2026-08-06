<x-layouts::app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 p-6">

        {{-- Filtro de fechas --}}
        <div class="flex flex-wrap items-end gap-3">
            <form method="GET" action="{{ route('dashboard') }}" class="flex flex-wrap items-end gap-3">
                <div class="flex flex-col gap-1">
                    <label class="text-xs font-medium text-gray-500">Desde</label>
                    <input
                        type="date"
                        name="desde"
                        value="{{ $desde->toDateString() }}"
                        class="rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-800 shadow-sm"
                    >
                </div>
                <div class="flex flex-col gap-1">
                    <label class="text-xs font-medium text-gray-500">Hasta</label>
                    <input
                        type="date"
                        name="hasta"
                        value="{{ $hasta->toDateString() }}"
                        class="rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-800 shadow-sm"
                    >
                </div>
                <button type="submit"
                        class="rounded-lg bg-secondary px-4 py-2 text-sm font-medium text-white transition-colors hover:opacity-90">
                    Filtrar
                </button>
            </form>
        </div>

        {{-- Tarjetas de totales --}}
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-2">
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <p class="text-sm text-gray-500">Cotizaciones en el período</p>
                <p class="mt-1 text-3xl font-semibold text-secondary">
                    {{ $totalCotizaciones }}
                </p>
                <p class="mt-1 text-xs text-gray-400">
                    {{ $desde->translatedFormat('d M Y') }} — {{ $hasta->translatedFormat('d M Y') }}
                </p>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <p class="text-sm text-gray-500">Total facturado en el período</p>
                <p class="mt-1 text-3xl font-semibold text-secondary">
                    ${{ number_format($totalFacturado, 2) }}
                </p>
                <p class="mt-1 text-xs text-gray-400">
                    {{ $desde->translatedFormat('d M Y') }} — {{ $hasta->translatedFormat('d M Y') }}
                </p>
            </div>
        </div>

        {{-- Gráfica de serie temporal --}}
        @php
            $labelsChart = $periodos->map(fn($p) => $p['label'])->values();
            $cotizChart  = $periodos->map(fn($p) => $p['cotizaciones'])->values();
            $montoChart  = $periodos->map(fn($p) => $p['monto'])->values();
        @endphp

        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <p class="mb-4 text-sm font-medium text-gray-700">
                Actividad por {{ $agrupar === 'dia' ? 'día' : 'semana' }}
            </p>
            <div class="mb-4 flex gap-4 text-xs text-gray-500">
                <span class="flex items-center gap-1.5">
                    <span class="inline-block h-2.5 w-2.5 rounded-full bg-secondary"></span>
                    Cotizaciones
                </span>
                <span class="flex items-center gap-1.5">
                    <span class="inline-block h-2.5 w-2.5 rounded-full bg-primary"></span>
                    Monto ($)
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
            const montoData = @json($montoChart);

            const ctx = document.getElementById('actividadChart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Cotizaciones',
                            data: cotizData,
                            borderColor: '#1b2d4f',
                            backgroundColor: 'rgba(27,45,79,0.08)',
                            borderWidth: 2,
                            pointRadius: 3,
                            tension: 0.3,
                            fill: true,
                            yAxisID: 'y',
                        },
                        {
                            label: 'Monto',
                            data: montoData,
                            borderColor: '#f5a623',
                            backgroundColor: 'rgba(245,166,35,0.12)',
                            borderWidth: 2,
                            pointRadius: 3,
                            tension: 0.3,
                            fill: true,
                            yAxisID: 'y1',
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
                            position: 'left',
                            ticks: { stepSize: 1, precision: 0, color: '#a1a1aa', font: { size: 11 } },
                            grid: { color: 'rgba(0,0,0,0.05)' }
                        },
                        y1: {
                            beginAtZero: true,
                            position: 'right',
                            ticks: { color: '#a1a1aa', font: { size: 11 } },
                            grid: { display: false }
                        }
                    }
                }
            });
        })();
        </script>

        {{-- Estado de cotizaciones y top productos --}}
        <div class="grid gap-4 lg:grid-cols-2">

            {{-- Cotizaciones por estado --}}
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <p class="mb-3 text-sm font-medium text-gray-700">Cotizaciones por estado</p>
                @php
                    $badgeColores = [
                        'borrador'  => 'bg-gray-100 text-gray-600',
                        'enviada'   => 'bg-blue-50 text-blue-700',
                        'aceptada'  => 'bg-emerald-50 text-emerald-700',
                        'rechazada' => 'bg-red-50 text-red-700',
                        'expirada'  => 'bg-amber-50 text-amber-700',
                    ];
                @endphp
                @foreach($estados as $estado => $total)
                    <div class="flex items-center justify-between py-2 {{ !$loop->last ? 'border-b border-gray-100' : '' }}">
                        <span class="text-sm text-gray-700 capitalize">{{ $estado }}</span>
                        <span class="rounded-full px-2.5 py-0.5 text-xs font-medium {{ $badgeColores[$estado] ?? 'bg-gray-100 text-gray-600' }}">
                            {{ $total }}
                        </span>
                    </div>
                @endforeach
            </div>

            {{-- Top productos --}}
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <p class="mb-3 text-sm font-medium text-gray-700">Productos más cotizados</p>
                @forelse($topProductos as $item)
                    <div class="flex items-center justify-between py-2 {{ !$loop->last ? 'border-b border-gray-100' : '' }}">
                        <span class="text-sm text-gray-700">
                            {{ $item->producto->nombre ?? 'Producto eliminado' }}
                        </span>
                        <span class="rounded-full bg-primary/10 px-2.5 py-0.5 text-xs font-medium text-primary-content">
                            {{ $item->cantidad_total }} uds · {{ $item->veces_cotizado }} cot.
                        </span>
                    </div>
                @empty
                    <p class="text-sm text-gray-400">Sin datos en el período</p>
                @endforelse
            </div>

        </div>
    </div>
</x-layouts::app>