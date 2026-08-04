<x-layouts::app :title="'Editar ' . $cotizacion->folio">

    {{-- Encabezado --}}
    <div class="mb-6 flex items-center gap-4">
        <a href="{{ route('cotizaciones.show', $cotizacion) }}"
           class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Editar cotización</h1>
            <p class="text-sm text-gray-500">{{ $cotizacion->folio }}</p>
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

    <form method="POST" action="{{ route('cotizaciones.update', $cotizacion) }}"
          x-data="cotizacionEdit()"
          x-init="init()">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

            {{-- Columna izquierda --}}
            <div class="space-y-6 lg:col-span-2">

                {{-- Médico --}}
                <div class="rounded-xl border border-gray-200 bg-white p-6">
                    <h2 class="mb-4 text-sm font-semibold text-gray-700">Médico solicitante</h2>
                    <div>
                        <label class="mb-1 block text-xs font-medium text-gray-600">Médico *</label>
                        <select name="medico_id"
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                required>
                            <option value="">Seleccionar médico...</option>
                            @foreach($medicos as $m)
                                <option value="{{ $m->id }}"
                                    {{ old('medico_id', $cotizacion->medico_id) == $m->id ? 'selected' : '' }}>
                                    {{ $m->nombre_completo }}
                                    @if($m->especialidad) — {{ $m->especialidad }} @endif
                                </option>
                            @endforeach
                        </select>
                        @error('medico_id')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Institución --}}
                <div class="rounded-xl border border-gray-200 bg-white p-6">
                    <h2 class="mb-4 text-sm font-semibold text-gray-700">Institución (opcional)</h2>
                    <div>
                        <label class="mb-1 block text-xs font-medium text-gray-600">Hospital / Institución</label>
                        <select name="hospital_id"
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                            <option value="">Sin institución</option>
                            @foreach($hospitales as $h)
                                <option value="{{ $h->id }}"
                                    {{ old('hospital_id', $cotizacion->hospital_id) == $h->id ? 'selected' : '' }}>
                                    {{ $h->nombre }}
                                    @if($h->procedencia) ({{ $h->procedencia }}) @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Estudios --}}
                <div class="rounded-xl border border-gray-200 bg-white p-6">
                    <div class="mb-4 flex items-center justify-between">
                        <h2 class="text-sm font-semibold text-gray-700">Estudios</h2>
                        <button type="button"
                                @click="agregarEstudio()"
                                class="inline-flex items-center gap-1 rounded-lg px-3 py-1.5 text-xs font-medium text-white hover:opacity-90 transition-colors"
                                style="background-color:#002745;">
                            + Agregar estudio
                        </button>
                    </div>

                    @error('estudios')
                        <p class="mb-3 text-xs text-red-500">{{ $message }}</p>
                    @enderror

                    <div class="space-y-3">
                        <template x-for="(item, index) in renglones" :key="index">
                            <div class="flex items-start gap-3 rounded-lg border border-gray-100 bg-gray-50 p-3">
                                {{-- Estudio --}}
                                <div class="flex-1">
                                    <label class="mb-1 block text-xs font-medium text-gray-500">Estudio</label>
                                    <select :name="'estudios[' + index + '][id]'"
                                            x-model="item.id"
                                            @change="autocompletarPrecio(index)"
                                            class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                            required>
                                        <option value="">Seleccionar...</option>
                                        @foreach($estudios as $e)
                                            <option value="{{ $e->id }}"
                                                    data-precio="{{ $e->precio_unitario }}">
                                                {{ $e->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                {{-- Cantidad --}}
                                <div class="w-20">
                                    <label class="mb-1 block text-xs font-medium text-gray-500">Cant.</label>
                                    <input type="number"
                                           :name="'estudios[' + index + '][cantidad]'"
                                           x-model.number="item.cantidad"
                                           @input="recalcular()"
                                           min="1"
                                           class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                           required />
                                </div>
                                {{-- Precio --}}
                                <div class="w-32">
                                    <label class="mb-1 block text-xs font-medium text-gray-500">Precio unit.</label>
                                    <input type="number"
                                           :name="'estudios[' + index + '][precio]'"
                                           x-model.number="item.precio"
                                           @input="recalcular()"
                                           min="0"
                                           step="0.01"
                                           class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                           required />
                                </div>
                                {{-- Subtotal --}}
                                <div class="w-28 text-right">
                                    <label class="mb-1 block text-xs font-medium text-gray-500">Subtotal</label>
                                    <span class="block pt-2 text-sm font-medium text-gray-900"
                                          x-text="'$' + (item.cantidad * item.precio).toFixed(2)"></span>
                                </div>
                                {{-- Quitar --}}
                                <div class="pt-6">
                                    <button type="button"
                                            @click="quitarEstudio(index)"
                                            class="rounded-lg p-1.5 text-gray-400 hover:bg-red-50 hover:text-red-500 transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </template>

                        <div x-show="renglones.length === 0"
                             class="py-6 text-center text-sm text-gray-400">
                            Agrega al menos un estudio
                        </div>
                    </div>
                </div>

            </div>

            {{-- Columna derecha --}}
            <div class="space-y-6">

                {{-- Totales --}}
                <div class="rounded-xl border border-gray-200 bg-white p-6">
                    <h2 class="mb-4 text-sm font-semibold text-gray-700">Resumen</h2>
                    <div class="space-y-3">
                        <div class="flex justify-between text-sm text-gray-600">
                            <span>Subtotal</span>
                            <span x-text="'$' + subtotal.toFixed(2)"></span>
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-medium text-gray-600">Descuento (%)</label>
                            <input type="number"
                                   name="descuento"
                                   x-model.number="descuento"
                                   @input="recalcular()"
                                   min="0"
                                   max="100"
                                   step="0.01"
                                   value="{{ old('descuento', $cotizacion->descuento) }}"
                                   class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500" />
                        </div>
                        <div class="flex justify-between border-t border-gray-200 pt-3 text-base font-semibold text-gray-900">
                            <span>Total</span>
                            <span x-text="'$' + total.toFixed(2)"></span>
                        </div>
                    </div>
                </div>

                {{-- Opciones --}}
                <div class="rounded-xl border border-gray-200 bg-white p-6">
                    <h2 class="mb-4 text-sm font-semibold text-gray-700">Opciones</h2>
                    <div class="space-y-4">
                        <div>
                            <label class="mb-1 block text-xs font-medium text-gray-600">Válida hasta</label>
                            <input type="date"
                                   name="valida_hasta"
                                   value="{{ old('valida_hasta', $cotizacion->valida_hasta?->format('Y-m-d')) }}"
                                   class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500" />
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-medium text-gray-600">Notas</label>
                            <textarea name="notas"
                                      rows="3"
                                      class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">{{ old('notas', $cotizacion->notas) }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Guardar --}}
                <button type="submit"
                        class="w-full rounded-lg px-4 py-3 text-sm font-medium text-white hover:opacity-90 transition-colors"
                        style="background-color:#002745;">
                    Guardar cambios
                </button>

                <a href="{{ route('cotizaciones.show', $cotizacion) }}"
                   class="block w-full rounded-lg border border-gray-200 px-4 py-3 text-center text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancelar
                </a>

            </div>
        </div>
    </form>

    @php
        $catalogoJson = $estudios->map(fn($e) => [
            'id'     => $e->id,
            'nombre' => $e->nombre,
            'precio' => (float) $e->precio_unitario,
        ]);

        $renglonesJson = $cotizacion->estudios->map(fn($r) => [
            'id'       => $r->estudio_id,
            'cantidad' => $r->cantidad,
            'precio'   => (float) $r->precio_unitario,
        ]);

        $descuentoInicial = (float) old('descuento', $cotizacion->descuento);
    @endphp

    <script>
    const catalogoEstudios = @json($catalogoJson);
    const renglonesIniciales = @json($renglonesJson);

    function cotizacionEdit() {
        return {
            renglones: [],
            subtotal: 0,
            descuento: {{ $descuentoInicial }},
            total: 0,

            init() {
                this.renglones = renglonesIniciales.length
                    ? renglonesIniciales.map(r => ({ ...r }))
                    : [{ id: '', cantidad: 1, precio: 0 }];
                this.recalcular();
            },

            agregarEstudio() {
                this.renglones.push({ id: '', cantidad: 1, precio: 0 });
            },

            quitarEstudio(index) {
                if (this.renglones.length === 1) return;
                this.renglones.splice(index, 1);
                this.recalcular();
            },

            autocompletarPrecio(index) {
                const id = parseInt(this.renglones[index].id);
                const estudio = catalogoEstudios.find(e => e.id === id);
                if (estudio) {
                    this.renglones[index].precio = estudio.precio;
                }
                this.recalcular();
            },

            recalcular() {
                this.subtotal = this.renglones.reduce((sum, r) => sum + (r.cantidad * r.precio), 0);
                const desc = this.subtotal * (this.descuento / 100);
                this.total = this.subtotal - desc;
            },
        };
    }
    </script>

</x-layouts::app>