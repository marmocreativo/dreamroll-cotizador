<x-layouts::app :title="__('Editar cita')">

    <div class="mb-6 flex items-center gap-4">
        <a href="{{ route('agenda.calendario', ['fecha' => $cita->fecha->toDateString()]) }}"
           class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Editar cita</h1>
            <p class="text-sm text-gray-500">{{ $cita->fecha->translatedFormat('l d \d\e F \d\e Y') }}</p>
        </div>
    </div>

    @if($errors->any())
        <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('agenda.citas.update', $cita) }}"
          x-data="editCita()"
          class="max-w-2xl space-y-6">
        @csrf
        @method('PUT')

        {{-- Paciente --}}
        <div class="rounded-xl border border-gray-200 bg-white p-6">
            <h2 class="mb-4 text-sm font-semibold uppercase tracking-wide text-gray-700">Paciente</h2>

            <input type="text"
                   x-model="busqueda"
                   placeholder="Buscar por nombre, folio o iniciales..."
                   class="mb-3 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500" />

            <div class="max-h-56 overflow-y-auto rounded-lg border border-gray-200 divide-y divide-gray-100">
                @foreach($pacientes as $p)
                    @php
                        $nombreCompleto = $p->anonimo
                            ? 'Anónimo — ' . $p->folio
                            : (trim(implode(' ', array_filter([$p->nombre, $p->apellido_paterno, $p->apellido_materno]))) ?: ($p->iniciales ?: $p->folio));
                    @endphp
                    <label :class="pacienteId == {{ $p->id }} ? 'bg-blue-50 border-l-2 border-blue-600' : 'hover:bg-gray-50'"
                           class="flex cursor-pointer items-center gap-3 px-4 py-3 transition-colors"
                           x-show="busqueda === '' || '{{ strtolower($nombreCompleto . ' ' . $p->folio) }}'.includes(busqueda.toLowerCase())">
                        <input type="radio"
                               name="paciente_id"
                               value="{{ $p->id }}"
                               x-model="pacienteId"
                               class="text-blue-600 focus:ring-blue-500" />
                        <div>
                            <div class="text-sm font-medium text-gray-900">{{ $nombreCompleto }}</div>
                            <div class="text-xs text-gray-400">{{ $p->folio }}</div>
                        </div>
                    </label>
                @endforeach
            </div>
            @error('paciente_id')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>

        {{-- Centro --}}
        <div class="rounded-xl border border-gray-200 bg-white p-6">
            <h2 class="mb-4 text-sm font-semibold uppercase tracking-wide text-gray-700">Centro</h2>

            <input type="text"
                   x-model="busquedaCentro"
                   placeholder="Buscar centro..."
                   class="mb-3 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500" />

            <div class="max-h-56 overflow-y-auto rounded-lg border border-gray-200 divide-y divide-gray-100">
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
            @error('centro_id')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>

        {{-- Fecha, hora, estado, notas --}}
        <div class="rounded-xl border border-gray-200 bg-white p-6">
            <h2 class="mb-4 text-sm font-semibold uppercase tracking-wide text-gray-700">Detalles</h2>

            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Fecha *</label>
                        <input type="date"
                               name="fecha"
                               value="{{ old('fecha', $cita->fecha->format('Y-m-d')) }}"
                               class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                               required />
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Hora *</label>
                        <input type="time"
                               name="hora"
                               value="{{ old('hora', \Carbon\Carbon::parse($cita->hora)->format('H:i')) }}"
                               class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                               required />
                    </div>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Estado</label>
                    <select name="estado"
                            class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                        <option value="programada" @selected(old('estado', $cita->estado) === 'programada')>Programada</option>
                        <option value="confirmada" @selected(old('estado', $cita->estado) === 'confirmada')>Confirmada</option>
                        <option value="realizada"  @selected(old('estado', $cita->estado) === 'realizada')>Realizada</option>
                        <option value="cancelada"  @selected(old('estado', $cita->estado) === 'cancelada')>Cancelada</option>
                    </select>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Notas</label>
                    <textarea name="notas"
                              rows="3"
                              class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">{{ old('notas', $cita->notas) }}</textarea>
                </div>
            </div>
        </div>

        {{-- Botones --}}
        <div class="flex gap-3">
            <button type="submit"
                    class="rounded-lg px-6 py-2.5 text-sm font-medium text-white hover:opacity-90 transition-colors"
                    style="background-color:#002745;">
                Guardar cambios
            </button>
            <a href="{{ route('agenda.calendario', ['fecha' => $cita->fecha->toDateString()]) }}"
               class="rounded-lg border border-gray-200 px-6 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                Cancelar
            </a>
        </div>
    </form>

    <script>
    function editCita() {
        return {
            pacienteId: '{{ old('paciente_id', $cita->paciente_id) }}',
            centroId:   '{{ old('centro_id', $cita->centro_id) }}',
            busqueda:      '',
            busquedaCentro: '',
        }
    }
    </script>

</x-layouts::app>