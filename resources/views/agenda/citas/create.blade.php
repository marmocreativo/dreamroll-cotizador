<x-layouts::app :title="__('Nueva cita')">

    {{-- Encabezado --}}
    <div class="mb-6 flex items-center gap-4">
        <a href="{{ route('agenda.calendario') }}"
           class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Nueva cita</h1>
            <p class="text-sm text-gray-500">Completa los 3 pasos para registrar la cita</p>
        </div>
    </div>

    <div x-data="wizardCita({{ session('paso_error', 1) }})" class="max-w-2xl">

        {{-- Indicador de pasos --}}
        <div class="mb-8 flex items-center gap-2">
            <template x-for="(label, i) in pasos" :key="i">
                <div class="flex items-center gap-2">
                    <div class="flex items-center gap-2">
                        <div class="flex size-7 items-center justify-center rounded-full text-xs font-semibold transition-colors"
                             :class="paso > i + 1 ? 'bg-green-500 text-white' :
                                     paso === i + 1 ? 'text-white' : 'bg-gray-100 text-gray-400'"
                             :style="paso === i + 1 ? 'background-color:#002745' : ''">
                            <span x-show="paso <= i + 1" x-text="i + 1"></span>
                            <svg x-show="paso > i + 1" xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                            </svg>
                        </div>
                        <span class="text-sm font-medium"
                              :class="paso === i + 1 ? 'text-gray-900' : 'text-gray-400'"
                              x-text="label"></span>
                    </div>
                    <div x-show="i < pasos.length - 1"
                         class="h-px w-8 bg-gray-200"></div>
                </div>
            </template>
        </div>

        <form method="POST" action="{{ route('agenda.citas.store') }}">
            @csrf

            {{-- ══════════════════════════════════════ --}}
            {{-- PASO 1 — Paciente                      --}}
            {{-- ══════════════════════════════════════ --}}
            <div x-show="paso === 1" class="space-y-4">
                <div class="rounded-xl border border-gray-200 bg-white p-6">
                    <h2 class="mb-4 text-sm font-semibold uppercase tracking-wide text-gray-700">¿Quién es el paciente?</h2>

                    {{-- Tipo --}}
                    <div class="mb-4 flex rounded-lg border border-gray-200 overflow-hidden">
                        <button type="button"
                                @click="pacienteTipo = 'existente'"
                                :class="pacienteTipo === 'existente' ? 'text-white' : 'bg-white text-gray-600 hover:bg-gray-50'"
                                :style="pacienteTipo === 'existente' ? 'background-color:#002745' : ''"
                                class="flex-1 px-4 py-2 text-sm font-medium transition-colors">
                            Paciente existente
                        </button>
                        <button type="button"
                                @click="pacienteTipo = 'nuevo'"
                                :class="pacienteTipo === 'nuevo' ? 'text-white' : 'bg-white text-gray-600 hover:bg-gray-50'"
                                :style="pacienteTipo === 'nuevo' ? 'background-color:#002745' : ''"
                                class="flex-1 px-4 py-2 text-sm border-l border-gray-200 font-medium transition-colors">
                            Registrar nuevo
                        </button>
                    </div>

                    {{-- Paciente existente --}}
                    <div x-show="pacienteTipo === 'existente'" class="space-y-3">
                        <input type="text"
                               x-model="busquedaPaciente"
                               placeholder="Buscar por nombre, folio o iniciales..."
                               class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500" />

                        <div class="max-h-64 overflow-y-auto rounded-lg border border-gray-200 divide-y divide-gray-100">
                            @foreach($pacientes as $p)
                                <label :class="pacienteId == {{ $p->id }} ? 'bg-blue-50 border-l-2 border-blue-600' : 'hover:bg-gray-50'"
                                       class="flex cursor-pointer items-center gap-3 px-4 py-3 transition-colors"
                                       x-show="busquedaPaciente === '' || '{{ strtolower($p->nombre_display . ' ' . $p->folio) }}'.includes(busquedaPaciente.toLowerCase())">
                                    <input type="radio"
                                           name="paciente_id"
                                           value="{{ $p->id }}"
                                           x-model="pacienteId"
                                           class="text-blue-600 focus:ring-blue-500" />
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $p->nombre_display }}</div>
                                        <div class="text-xs text-gray-400">{{ $p->folio }}</div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Paciente nuevo --}}
                    <div x-show="pacienteTipo === 'nuevo'" class="space-y-4">

                        <div class="flex items-center gap-3">
                            <input type="checkbox"
                                   id="anonimo"
                                   x-model="anonimo"
                                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" />
                            <label for="anonimo" class="text-sm font-medium text-gray-700">
                                Paciente anónimo
                                <span class="text-xs text-gray-400 font-normal ml-1">— solo se generará el folio</span>
                            </label>
                        </div>

                        <div x-show="!anonimo" class="space-y-4">
                            <div class="grid grid-cols-3 gap-3">
                                <div>
                                    <label class="mb-1 block text-xs font-medium text-gray-700">Iniciales</label>
                                    <input type="text" name="iniciales" value="{{ old('iniciales') }}" maxlength="10" placeholder="J.G.M."
                                           class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500" />
                                </div>
                                <div class="col-span-2">
                                    <label class="mb-1 block text-xs font-medium text-gray-700">Nombre</label>
                                    <input type="text" name="nombre" value="{{ old('nombre') }}"
                                           class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500" />
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="mb-1 block text-xs font-medium text-gray-700">Apellido paterno</label>
                                    <input type="text" name="apellido_paterno" value="{{ old('apellido_paterno') }}"
                                           class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500" />
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs font-medium text-gray-700">Apellido materno</label>
                                    <input type="text" name="apellido_materno" value="{{ old('apellido_materno') }}"
                                           class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500" />
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="mb-1 block text-xs font-medium text-gray-700">Edad</label>
                                    <input type="number" name="edad" value="{{ old('edad') }}" min="0" max="120" placeholder="años"
                                        class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500" />
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs font-medium text-gray-700">Sexo</label>
                                    <select name="sexo"
                                            class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                        <option value="">—</option>
                                        <option value="M" @selected(old('sexo') === 'M')>Masculino</option>
                                        <option value="F" @selected(old('sexo') === 'F')>Femenino</option>
                                        <option value="O" @selected(old('sexo') === 'O')>Otro</option>
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="mb-1 block text-xs font-medium text-gray-700">WhatsApp</label>
                                    <input type="tel" name="whatsapp" placeholder="10 dígitos" value="{{ old('whatsapp') }}"
                                           class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500" />
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs font-medium text-gray-700">Correo</label>
                                    <input type="email" name="correo" value="{{ old('correo') }}"
                                           class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Hidden fields estado paciente --}}
                <input type="hidden" name="paciente_tipo" :value="pacienteTipo" />
                <input type="hidden" name="anonimo" :value="anonimo ? 1 : 0" />
            </div>

            {{-- ══════════════════════════════════════ --}}
            {{-- PASO 2 — Centro                        --}}
            {{-- ══════════════════════════════════════ --}}
            <div x-show="paso === 2" class="space-y-4">
                <div class="rounded-xl border border-gray-200 bg-white p-6">
                    <h2 class="mb-4 text-sm font-semibold uppercase tracking-wide text-gray-700">¿En qué centro es la cita?</h2>

                    <div class="mb-4 flex rounded-lg border border-gray-200 overflow-hidden">
                        <button type="button"
                                @click="centroTipo = 'existente'"
                                :class="centroTipo === 'existente' ? 'text-white' : 'bg-white text-gray-600 hover:bg-gray-50'"
                                :style="centroTipo === 'existente' ? 'background-color:#002745' : ''"
                                class="flex-1 px-4 py-2 text-sm font-medium transition-colors">
                            Centro existente
                        </button>
                        <button type="button"
                                @click="centroTipo = 'nuevo'"
                                :class="centroTipo === 'nuevo' ? 'text-white' : 'bg-white text-gray-600 hover:bg-gray-50'"
                                :style="centroTipo === 'nuevo' ? 'background-color:#002745' : ''"
                                class="flex-1 px-4 py-2 text-sm border-l border-gray-200 font-medium transition-colors">
                            Registrar nuevo
                        </button>
                    </div>

                    {{-- Centro existente --}}
                    <div x-show="centroTipo === 'existente'" class="space-y-3">
                        <input type="text"
       x-model="busquedaCentro"
       placeholder="Buscar por nombre, ciudad o estado..."
       class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500" />

                        <div class="max-h-64 overflow-y-auto rounded-lg border border-gray-200 divide-y divide-gray-100">
                            @foreach($centros as $c)
                                <label :class="centroId == {{ $c->id }} ? 'bg-blue-50 border-l-2 border-blue-600' : 'hover:bg-gray-50'"
                                       class="flex cursor-pointer items-center gap-3 px-4 py-3 transition-colors"
                                       x-show="busquedaCentro === '' || '{{ strtolower($c->nombre . ' ' . $c->direccion) }}'.includes(busquedaCentro.toLowerCase())">
                                    <input type="radio"
                                           name="centro_id"
                                           value="{{ $c->id }}"
                                           x-model="centroId"
                                           class="text-blue-600 focus:ring-blue-500" />
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $c->nombre }}</div>
                                        @if($c->direccion)
                                            <div class="text-xs text-gray-400">{{ $c->direccion }}</div>
                                        @endif
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Centro nuevo --}}
                    <div x-show="centroTipo === 'nuevo'" class="space-y-4">
                        <div>
                            <label class="mb-1 block text-xs font-medium text-gray-700">Nombre del centro</label>
                            <input type="text" name="centro_nombre"  value="{{ old('centro_nombre') }}"
                                   class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500" />
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-medium text-gray-700">Dirección</label>
                            <textarea name="centro_direccion" rows="2"
                                      class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                            {{ old('centro_direccion') }}
                            </textarea>
                        </div>
                    </div>
                </div>

                <input type="hidden" name="centro_tipo" :value="centroTipo" />
            </div>

            {{-- ══════════════════════════════════════ --}}
            {{-- PASO 3 — Fecha y hora                  --}}
            {{-- ══════════════════════════════════════ --}}
            <div x-show="paso === 3" class="space-y-4">
                <div class="rounded-xl border border-gray-200 bg-white p-6">
                    <h2 class="mb-4 text-sm font-semibold uppercase tracking-wide text-gray-700">¿Cuándo es la cita?</h2>

                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700">
                                    Fecha <span class="text-red-500">*</span>
                                </label>
                                <input type="date"
                                       name="fecha"
                                       value="{{ $fecha }}"
                                       x-model="citaFecha"
                                       class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500" />
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700">
                                    A partir de las: <span class="text-red-500">*</span>
                                </label>
                                <input type="time"
                                       name="hora"
                                       x-model="citaHora"
                                       class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500" />
                            </div>
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Estado</label>
                            <select name="estado"
                                    class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                <option value="programada" @selected(old('estado', 'programada') === 'programada')>Programada</option>
                                <option value="confirmada" @selected(old('estado') === 'confirmada')>Confirmada</option>
                                <option value="realizada"  @selected(old('estado') === 'realizada')>Realizada</option>
                                <option value="cancelada"  @selected(old('estado') === 'cancelada')>Cancelada</option>
                            </select>
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Notas</label>
                            <textarea name="notas" rows="2"
                                      class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">{{ old('notas') }}</textarea>
                        </div>

                        {{-- Resumen --}}
                        <div class="rounded-lg bg-gray-50 p-4 text-sm space-y-1">
                            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 mb-2">Resumen</p>
                            <div class="grid grid-cols-[5rem_1fr] gap-1 text-sm">
                                <span class="text-gray-400">Paciente</span>
                                <span class="font-medium text-gray-900" x-text="resumenPaciente()"></span>
                                <span class="text-gray-400">Centro</span>
                                <span class="font-medium text-gray-900" x-text="resumenCentro()"></span>
                                <span class="text-gray-400">Fecha</span>
                                <span class="font-medium text-gray-900" x-text="citaFecha || '—'"></span>
                                <span class="text-gray-400">Hora</span>
                                <span class="font-medium text-gray-900" x-text="citaHora || '—'"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Botones de navegación ────────────── --}}
            <div class="mt-6 flex justify-between">
                <button type="button"
                        @click="paso === 1 ? window.location.href='{{ route('agenda.calendario') }}' : paso--"
                        class="rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                    <span x-text="paso === 1 ? 'Cancelar' : '← Anterior'"></span>
                </button>

                <button x-show="paso < 3"
                        type="button"
                        @click="siguientePaso()"
                        class="rounded-lg px-4 py-2 text-sm font-medium text-white hover:opacity-90 transition-colors"
                        style="background-color:#002745;">
                    Siguiente →
                </button>

                <button x-show="paso === 3"
                        type="submit"
                        class="rounded-lg px-4 py-2 text-sm font-medium text-white hover:opacity-90 transition-colors"
                        style="background-color:#002745;">
                    Crear cita
                </button>
            </div>
        </form>
    </div>

    {{-- Datos de pacientes y centros para Alpine --}}
    <script>
        @php
            $pacientesJs = $pacientes->map(fn($p) => ['id' => $p->id, 'label' => $p->nombre_display, 'folio' => $p->folio]);
            $centrosJs   = $centros->map(fn($c) => ['id' => $c->id, 'label' => $c->nombre]);
        @endphp

        const _pacientes = @json($pacientesJs);
        const _centros   = @json($centrosJs);

        function wizardCita(pasoInicial = 1) {
            return {
                paso: pasoInicial,
                pasos: ['Paciente', 'Centro', 'Fecha y hora'],

                // Paso 1
                pacienteTipo: '{{ old("paciente_tipo", "nuevo") }}',
                pacienteId: '{{ old("paciente_id") }}',
                busquedaPaciente: '',
                anonimo: {{ old('anonimo', 0) ? 'true' : 'false' }},

                // Paso 2
                centroTipo: '{{ old("centro_tipo", "existente") }}',
                centroId: '{{ old("centro_id") }}',
                busquedaCentro: '',

                // Paso 3
                citaFecha: '{{ old("fecha", $fecha) }}',
                citaHora: '{{ old("hora") }}',

                siguientePaso() {
                    if (this.paso === 1 && this.pacienteTipo === 'existente' && !this.pacienteId) {
                        alert('Selecciona un paciente para continuar.');
                        return;
                    }
                    if (this.paso === 2 && this.centroTipo === 'existente' && !this.centroId) {
                        alert('Selecciona un centro para continuar.');
                        return;
                    }
                    if (this.paso < 3) this.paso++;
                },

                resumenPaciente() {
                    if (this.pacienteTipo === 'existente' && this.pacienteId) {
                        const p = _pacientes.find(p => p.id == this.pacienteId);
                        return p ? p.label : '—';
                    }
                    if (this.anonimo) return 'Anónimo';
                    return 'Paciente nuevo';
                },

                resumenCentro() {
                    if (this.centroTipo === 'existente' && this.centroId) {
                        const c = _centros.find(c => c.id == this.centroId);
                        return c ? c.label : '—';
                    }
                    return 'Centro nuevo';
                },
            }
        }
    </script>

</x-layouts::app>