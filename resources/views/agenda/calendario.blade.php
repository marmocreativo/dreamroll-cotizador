<x-layouts::app :title="__('Agenda')">

    {{-- Encabezado --}}
    <div class="mb-4 flex items-center justify-between flex-wrap gap-2">
        <div class="flex items-center gap-2">

            {{-- Anterior --}}
            <a href="{{ route('agenda.calendario', [
                    'vista' => $vista,
                    'fecha' => $vista === 'mensual'
                        ? $fechaCarbon->copy()->subMonth()->toDateString()
                        : $fechaCarbon->copy()->subWeek()->toDateString(),
                    'centro' => $centroFiltro,
                ]) }}"
               class="rounded-lg border border-gray-200 p-2 text-gray-500 hover:bg-gray-50 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                </svg>
            </a>

            <h1 class="text-lg font-semibold text-gray-900 min-w-48 text-center">
                @if($vista === 'mensual')
                    {{ $fechaCarbon->translatedFormat('F Y') }}
                @else
                    @php
                        $primerDia = $diasSemana->first();
                        $ultimoDia = $diasSemana->last();
                    @endphp
                    Semana del {{ $primerDia->translatedFormat('d M') }}
                    al {{ $ultimoDia->translatedFormat('d M Y') }}
                @endif
            </h1>

            {{-- Siguiente --}}
            <a href="{{ route('agenda.calendario', [
                    'vista' => $vista,
                    'fecha' => $vista === 'mensual'
                        ? $fechaCarbon->copy()->addMonth()->toDateString()
                        : $fechaCarbon->copy()->addWeek()->toDateString(),
                    'centro' => $centroFiltro,
                ]) }}"
               class="rounded-lg border border-gray-200 p-2 text-gray-500 hover:bg-gray-50 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                </svg>
            </a>

            {{-- Hoy --}}
            <a href="{{ route('agenda.calendario', ['vista' => $vista]) }}"
               class="rounded-lg border border-gray-200 px-3 py-1.5 text-sm text-gray-600 hover:bg-gray-50 transition-colors">
                Hoy
            </a>
        </div>

        <div class="flex items-center gap-2 flex-wrap">

            {{-- Filtro centro --}}
            <form method="GET" action="{{ route('agenda.calendario') }}" id="form-filtros">
                <input type="hidden" name="vista" value="{{ $vista }}">
                <input type="hidden" name="fecha" value="{{ $fecha }}">
                <select name="centro" onchange="document.getElementById('form-filtros').submit()"
                        class="rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-700 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    <option value="">Todos los centros</option>
                    @foreach($centros as $c)
                        <option value="{{ $c->id }}" @selected($centroFiltro == $c->id)>
                            {{ $c->nombre }}
                        </option>
                    @endforeach
                </select>
            </form>

            {{-- Toggle vista --}}
            <div class="flex rounded-lg border border-gray-200 overflow-hidden">
                <a href="{{ route('agenda.calendario', ['vista' => 'mensual', 'fecha' => $fecha, 'centro' => $centroFiltro]) }}"
                   class="px-3 py-2 text-sm transition {{ $vista === 'mensual' ? 'bg-blue-600 text-white' : 'bg-white text-gray-600 hover:bg-gray-50' }}">
                    Mes
                </a>
                <a href="{{ route('agenda.calendario', ['vista' => 'semanal', 'fecha' => $fecha, 'centro' => $centroFiltro]) }}"
                   class="px-3 py-2 text-sm border-l border-gray-200 transition {{ $vista === 'semanal' ? 'bg-blue-600 text-white' : 'bg-white text-gray-600 hover:bg-gray-50' }}">
                    Semana
                </a>
            </div>

            <a href="{{ route('agenda.citas.create', ['fecha' => $fecha]) }}"
               class="inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium text-white hover:opacity-90 transition-colors"
               style="background-color:#002745;">
                + Nueva cita
            </a>
        </div>
    </div>

    {{-- ── Vista Mensual ────────────────────────────────── --}}
    @if($vista === 'mensual')
        <div class="grid grid-cols-7 rounded-xl border border-gray-200 overflow-hidden">
            @foreach(['Lun','Mar','Mié','Jue','Vie','Sáb','Dom'] as $nd)
                <div class="bg-gray-50 text-center text-xs font-semibold py-2 text-gray-500 border-b border-gray-200">
                    {{ $nd }}
                </div>
            @endforeach

            @foreach($diasMes as $dia)
                @php
                    $key   = $dia->toDateString();
                    $esHoy = $dia->isToday();
                    $esMes = $dia->month === $fechaCarbon->month;
                    $citas = $citasPorDia[$key] ?? collect();
                @endphp
                <div class="min-h-[90px] p-1 border-t border-r border-gray-200 cursor-pointer transition hover:bg-gray-50
                        {{ !$esMes ? 'bg-gray-50' : 'bg-white' }}"
                onclick="abrirDia('{{ $key }}')">
                <div class="flex justify-start mb-1">
                    <span class="text-xs font-medium w-6 h-6 flex items-center justify-center rounded-full
                                {{ $esHoy ? 'bg-blue-600 text-white' : ($esMes ? 'text-gray-700' : 'text-gray-400') }}">
                        {{ $dia->day }}
                    </span>
                </div>
                <div class="space-y-0.5">
                    @foreach($citas->take(3) as $cita)
                        <div class="w-full text-left text-[11px] truncate rounded px-1 py-0.5
                            {{ match($cita->estado) {
                                'programada' => 'bg-blue-100 text-blue-800',
                                'confirmada' => 'bg-green-100 text-green-800',
                                'realizada'  => 'bg-gray-200 text-gray-600',
                                'cancelada'  => 'bg-red-100 text-red-700',
                                default      => 'bg-gray-100',
                            } }}">
                            {{ $cita->hora }} — {{ $cita->paciente->nombre_display }}
                        </div>
                    @endforeach
                    @if($citas->count() > 3)
                        <div class="text-[10px] text-gray-400 pl-1">+{{ $citas->count() - 3 }} más</div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    @endif

    {{-- ── Vista Semanal ────────────────────────────────── --}}
    @if($vista === 'semanal')
        <div class="grid grid-cols-7 rounded-xl border border-gray-200 overflow-hidden">
            @foreach($diasSemana as $dia)
                @php
                    $key   = $dia->toDateString();
                    $esHoy = $dia->isToday();
                    $citas = $citasPorDia[$key] ?? collect();
                @endphp
                <div class="flex flex-col border-r border-gray-200 last:border-r-0">
                    <div onclick="abrirDia('{{ $key }}')"
                        class="text-center py-2 border-b border-gray-200 transition cursor-pointer hover:opacity-80
                                {{ $esHoy ? 'bg-blue-600 text-white' : 'bg-gray-50 text-gray-500' }}">
                        <div class="text-xs font-semibold">{{ $dia->translatedFormat('D') }}</div>
                        <div class="text-sm font-bold">{{ $dia->day }}</div>
                    </div>
                    <div class="flex-1 min-h-[300px] p-1 space-y-1 bg-white cursor-pointer"
                        onclick="abrirDia('{{ $key }}')">
                        @foreach($citas as $cita)
                            <button
                                onclick="abrirDetalle({{ $cita->id }})"
                                class="w-full text-left text-[11px] rounded px-1 py-1 cursor-pointer transition
                                    {{ match($cita->estado) {
                                        'programada' => 'bg-blue-100 text-blue-800 hover:bg-blue-200',
                                        'confirmada' => 'bg-green-100 text-green-800 hover:bg-green-200',
                                        'realizada'  => 'bg-gray-200 text-gray-600 hover:bg-gray-300',
                                        'cancelada'  => 'bg-red-100 text-red-700 hover:bg-red-200',
                                        default      => 'bg-gray-100',
                                    } }}">
                                <div class="font-semibold">{{ $cita->hora }}</div>
                                <div class="truncate">{{ $cita->paciente->nombre_display }}</div>
                                <div class="truncate text-[10px] opacity-70">{{ $cita->centro->nombre }}</div>
                            </button>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- ── Modal día ───────────────────────────────────── --}}
    <div x-data="modalDia()" x-show="abierto" x-transition
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
        style="display:none"
        id="modal-dia">
        <div class="w-full max-w-lg rounded-xl bg-white shadow-xl overflow-hidden"
            @click.outside="cerrar()">

            {{-- Header --}}
            <div class="flex items-center justify-between border-b border-gray-200 px-6 py-5">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Agenda</p>
                    <h2 class="text-base font-semibold text-gray-900 mt-0.5" x-text="titulo"></h2>
                </div>
                <button @click="cerrar()" class="rounded-lg p-1.5 text-gray-400 hover:bg-gray-100 hover:text-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="p-6">

                {{-- Sin citas --}}
                <template x-if="citas.length === 0">
                    <div class="flex flex-col items-center justify-center py-8 text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-10 text-gray-200 mb-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                        </svg>
                        <p class="text-sm font-medium text-gray-500">Sin citas para este día</p>
                        <p class="text-xs text-gray-400 mt-1">Puedes agendar una nueva cita con el botón de abajo.</p>
                    </div>
                </template>

                {{-- Lista de citas --}}
                <template x-if="citas.length > 0">
                    <div class="mb-5 flex flex-col divide-y divide-gray-100 rounded-xl border border-gray-200 overflow-hidden">
                        <template x-for="cita in citas" :key="cita.id">
                            <button type="button"
                                    @click="cerrar(); abrirDetalle(cita.id)"
                                    class="flex items-center gap-4 px-4 py-3.5 text-left hover:bg-gray-50 transition-colors">

                                {{-- Hora --}}
                                <div class="shrink-0 w-14 text-center">
                                    <span class="text-sm font-bold text-gray-800" x-text="cita.hora"></span>
                                </div>

                                <div class="h-8 w-px bg-gray-200 shrink-0"></div>

                                {{-- Info --}}
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-900" x-text="cita.paciente"></p>
                                    <p class="text-xs text-gray-400 truncate mt-0.5" x-text="cita.centro"></p>
                                </div>

                                {{-- Estado --}}
                                <span class="shrink-0 rounded-full px-2.5 py-1 text-xs font-medium"
                                    :class="{
                                        'bg-blue-100 text-blue-700':   cita.estado === 'programada',
                                        'bg-green-100 text-green-700': cita.estado === 'confirmada',
                                        'bg-gray-200 text-gray-600':   cita.estado === 'realizada',
                                        'bg-red-100 text-red-700':     cita.estado === 'cancelada',
                                    }"
                                    x-text="cita.estado.charAt(0).toUpperCase() + cita.estado.slice(1)">
                                </span>

                                {{-- Chevron --}}
                                <svg xmlns="http://www.w3.org/2000/svg" class="size-4 text-gray-300 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                                </svg>
                            </button>
                        </template>
                    </div>
                </template>

                {{-- Botón nueva cita --}}
                <a :href="'/agenda/nueva-cita?fecha=' + fecha"
                class="flex w-full items-center justify-center gap-2 rounded-lg px-4 py-2.5 text-sm font-medium text-white hover:opacity-90 transition-colors"
                style="background-color:#002745;">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Nueva cita para este día
                </a>
            </div>
        </div>
    </div>

    {{-- ── Modal detalle (Alpine + fetch) ─────────────── --}}
    <div x-data="modalDetalle()" x-show="abierto" x-transition
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
         style="display:none">
        <div class="w-full max-w-md rounded-xl bg-white shadow-xl overflow-hidden"
             @click.outside="cerrar()">

            {{-- Loading --}}
            <div x-show="cargando" class="p-8 text-center text-gray-400 text-sm">
                Cargando...
            </div>

            {{-- Contenido --}}
            <div x-show="!cargando" x-html="contenido"></div>
        </div>
    </div>

    <script>
        @php
            $todasLasCitas = collect($citasPorDia)->map(fn($citas) =>
                $citas->map(fn($c) => [
                    'id'      => $c->id,
                    'hora'    => $c->hora,
                    'estado'  => $c->estado,
                    'centro'  => $c->centro->nombre,
                    'paciente'=> (function($p) {
                        if ($p->anonimo) return 'Anónimo — ' . $p->folio;
                        $nombre = trim(implode(' ', array_filter([
                            $p->nombre,
                            $p->apellido_paterno,
                            $p->apellido_materno,
                        ])));
                        return $nombre ?: ($p->iniciales ?: $p->folio);
                    })($c->paciente),
                ])
            );
        @endphp
        const _citasPorDia = @json($todasLasCitas);

        function modalDia() {
            return {
                abierto: false,
                fecha:   '',
                titulo:  '',
                citas:   [],

                abrir(fecha) {
                    this.fecha  = fecha;
                    this.citas  = _citasPorDia[fecha] ?? [];
                    const [y, m, d] = fecha.split('-');
                    this.titulo = `Citas del ${d}/${m}/${y}`;
                    this.abierto = true;
                },

                cerrar() {
                    this.abierto = false;
                }
            }
        }

        function modalDetalle() {
            return {
                abierto: false,
                cargando: false,
                contenido: '',

                async abrir(id) {
                    this.abierto   = true;
                    this.cargando  = true;
                    this.contenido = '';

                    const res  = await fetch(`/agenda/citas/${id}`, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    this.contenido = await res.text();
                    this.cargando  = false;
                },

                cerrar() {
                    this.abierto   = false;
                    this.contenido = '';
                }
            }
        }

        function abrirDia(fecha) {
            const el = document.querySelector('#modal-dia');
            if (el && el._x_dataStack) {
                el._x_dataStack[0].abrir(fecha);
            }
        }

        function abrirDetalle(id) {
            const el = document.querySelector('[x-data="modalDetalle()"]');
            if (el && el._x_dataStack) {
                el._x_dataStack[0].abrir(id);
            }
        }
    </script>

</x-layouts::app>