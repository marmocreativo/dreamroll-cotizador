<x-layouts::app :title="__('Nueva cotización')">

    {{-- Encabezado --}}
    <div class="mb-6 flex items-center gap-4">
        <a href="{{ route('cotizaciones.index') }}"
           class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Nueva cotización</h1>
            <p class="text-sm text-gray-500" x-data x-text="`Paso ${$store.wizard.paso} de 3`"></p>
        </div>
    </div>

    <div x-data="wizardCotizacion({{ session('paso_error', 1) }})" class="max-w-2xl">

        {{-- Indicador de pasos --}}
        <div class="mb-8 flex items-center">
            <template x-for="(label, i) in pasos" :key="i">
                <div class="flex items-center">
                    <div class="flex items-center gap-2">
                        <div class="flex size-7 items-center justify-center rounded-full text-xs font-semibold transition-colors"
                             :class="paso > i + 1 ? 'bg-amber-400 text-white' :
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
                    <div x-show="i < pasos.length - 1" class="mx-3 h-px w-8 bg-gray-200"></div>
                </div>
            </template>
        </div>

        <form method="POST" action="{{ route('cotizaciones.store') }}">
            @csrf

            {{-- ══════════════════════════════════════ --}}
            {{-- PASO 1 — Médico                        --}}
            {{-- ══════════════════════════════════════ --}}
            <div x-show="paso === 1">
                <div class="rounded-xl border border-gray-200 bg-white p-6">
                    <h2 class="mb-1 text-sm font-semibold uppercase tracking-wide text-gray-700">Selecciona o registra el médico</h2>
                    <p class="mb-4 text-sm text-gray-400">Busca un médico existente o registra uno nuevo.</p>

                    {{-- Toggle modo --}}
                    <div class="mb-4 flex rounded-lg border border-gray-200 overflow-hidden">
                        <button type="button"
                                @click="medicoModo = 'existente'; medicoId = null"
                                :class="medicoModo === 'existente' ? 'text-white' : 'bg-white text-gray-600 hover:bg-gray-50'"
                                :style="medicoModo === 'existente' ? 'background-color:#002745' : ''"
                                class="flex-1 px-4 py-2 text-sm font-medium transition-colors">
                            Médico existente
                        </button>
                        <button type="button"
                                @click="medicoModo = 'nuevo'; medicoId = null"
                                :class="medicoModo === 'nuevo' ? 'text-white' : 'bg-white text-gray-600 hover:bg-gray-50'"
                                :style="medicoModo === 'nuevo' ? 'background-color:#002745' : ''"
                                class="flex-1 px-4 py-2 text-sm border-l border-gray-200 font-medium transition-colors">
                            Registrar nuevo
                        </button>
                    </div>

                    {{-- Médico existente --}}
                    <div x-show="medicoModo === 'existente'" class="space-y-3">
                        <input type="text"
                               x-model="busquedaMedico"
                               placeholder="Buscar médico por nombre..."
                               class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500" />

                        {{-- Seleccionado --}}
                        <template x-if="medicoId">
                            <div class="flex items-center justify-between rounded-lg border px-4 py-3"
                                 style="border-color:#002745; background-color:#f0f4f8;">
                                <div>
                                    <p class="font-medium text-gray-900" x-text="medicoLabel"></p>
                                    <p class="text-sm text-gray-400" x-text="medicoEspecialidadLabel"></p>
                                </div>
                                <button type="button" @click="medicoId = null; medicoLabel = ''; medicoEspecialidadLabel = ''"
                                        class="rounded-lg p-1 text-gray-400 hover:bg-gray-100 hover:text-gray-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </template>

                        {{-- Lista filtrada --}}
                        <template x-if="!medicoId">
                            <div class="flex flex-col gap-1">
                                <template x-for="m in medicosFiltrados()" :key="m.id">
                                    <button type="button"
                                            @click="seleccionarMedico(m)"
                                            class="flex items-center justify-between rounded-lg border border-gray-200 px-4 py-2.5 text-left hover:border-gray-400 hover:bg-gray-50 transition-colors">
                                        <div>
                                            <span class="font-medium text-gray-900" x-text="m.nombre_completo"></span>
                                            <span class="ml-2 text-sm text-gray-400" x-text="m.especialidad"></span>
                                        </div>
                                        <span class="text-xs text-gray-400" x-text="m.hospital"></span>
                                    </button>
                                </template>
                                <p x-show="medicosFiltrados().length === 0"
                                   class="text-sm text-gray-400 px-1">No se encontraron médicos.</p>
                            </div>
                        </template>

                        <input type="hidden" name="medico_id" :value="medicoId" />
                    </div>

                    {{-- Médico nuevo --}}
                    <div x-show="medicoModo === 'nuevo'" class="space-y-4">
                        <div class="grid grid-cols-3 gap-3">
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700">Prefijo</label>
                                <select name="medico_prefijo"
                                        class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                    <option value="">—</option>
                                    <option value="Dr."  @selected(old('medico_prefijo') === 'Dr.')>Dr.</option>
                                    <option value="Dra." @selected(old('medico_prefijo') === 'Dra.')>Dra.</option>
                                    <option value="Sr."  @selected(old('medico_prefijo') === 'Sr.')>Sr.</option>
                                    <option value="Sra." @selected(old('medico_prefijo') === 'Sra.')>Sra.</option>
                                </select>
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700">Nombre <span class="text-red-500">*</span></label>
                                <input type="text" name="medico_nombre"
                                       value="{{ old('medico_nombre') }}"
                                       placeholder="Nombre(s)"
                                       class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500" />
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700">Apellido</label>
                                <input type="text" name="medico_apellido"
                                       value="{{ old('medico_apellido') }}"
                                       placeholder="Apellidos"
                                       class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500" />
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700">Email</label>
                                <input type="email" name="medico_email"
                                       value="{{ old('medico_email') }}"
                                       class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500" />
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700">Teléfono</label>
                                <input type="text" name="medico_telefono"
                                       value="{{ old('medico_telefono') }}"
                                       class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500" />
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700">Especialidad</label>
                                <input type="text" name="medico_especialidad"
                                       value="{{ old('medico_especialidad') }}"
                                       class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500" />
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700">Cédula</label>
                                <input type="text" name="medico_cedula"
                                       value="{{ old('medico_cedula') }}"
                                       class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500" />
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="medico_modo" :value="medicoModo" />
                </div>
            </div>

            {{-- ══════════════════════════════════════ --}}
            {{-- PASO 2 — Institución                   --}}
            {{-- ══════════════════════════════════════ --}}
            <div x-show="paso === 2">
                <div class="rounded-xl border border-gray-200 bg-white p-6">
                    <h2 class="mb-1 text-sm font-semibold uppercase tracking-wide text-gray-700">Institución</h2>
                    <p class="mb-4 text-sm text-gray-400">Este campo es opcional.</p>

                    <div class="mb-4 flex rounded-lg border border-gray-200 overflow-hidden">
                        <button type="button"
                                @click="hospitalModo = 'existente'; hospitalId = null"
                                :class="hospitalModo === 'existente' ? 'text-white' : 'bg-white text-gray-600 hover:bg-gray-50'"
                                :style="hospitalModo === 'existente' ? 'background-color:#002745' : ''"
                                class="flex-1 px-4 py-2 text-sm font-medium transition-colors">
                            Institución existente
                        </button>
                        <button type="button"
                                @click="hospitalModo = 'nuevo'; hospitalId = null"
                                :class="hospitalModo === 'nuevo' ? 'text-white' : 'bg-white text-gray-600 hover:bg-gray-50'"
                                :style="hospitalModo === 'nuevo' ? 'background-color:#002745' : ''"
                                class="flex-1 px-4 py-2 text-sm border-l border-gray-200 font-medium transition-colors">
                            Registrar nueva
                        </button>
                    </div>

                    {{-- Existente --}}
                    <div x-show="hospitalModo === 'existente'" class="space-y-3">
                        <input type="text"
                               x-model="busquedaHospital"
                               placeholder="Buscar institución..."
                               class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500" />

                        <template x-if="hospitalId">
                            <div class="flex items-center justify-between rounded-lg border px-4 py-3"
                                 style="border-color:#002745; background-color:#f0f4f8;">
                                <div>
                                    <p class="font-medium text-gray-900" x-text="hospitalLabel"></p>
                                    <p class="text-sm text-gray-400" x-text="hospitalProcedenciaLabel"></p>
                                </div>
                                <button type="button" @click="hospitalId = null; hospitalLabel = ''"
                                        class="rounded-lg p-1 text-gray-400 hover:bg-gray-100 hover:text-gray-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </template>

                        <template x-if="!hospitalId">
                            <div class="flex flex-col gap-1">
                                <template x-for="h in hospitalesFiltrados()" :key="h.id">
                                    <button type="button"
                                            @click="seleccionarHospital(h)"
                                            class="flex items-center justify-between rounded-lg border border-gray-200 px-4 py-2.5 text-left hover:border-gray-400 hover:bg-gray-50 transition-colors">
                                        <span class="font-medium text-gray-900" x-text="h.nombre"></span>
                                        <span class="inline-flex items-center rounded-full bg-gray-100 px-2 py-1 text-xs font-medium text-gray-600"
                                              x-show="h.procedencia" x-text="h.procedencia"></span>
                                    </button>
                                </template>
                                <p x-show="hospitalesFiltrados().length === 0"
                                   class="text-sm text-gray-400 px-1">No se encontraron instituciones.</p>
                            </div>
                        </template>

                        <input type="hidden" name="hospital_id" :value="hospitalId" />
                    </div>

                    {{-- Nueva --}}
                    <div x-show="hospitalModo === 'nuevo'" class="space-y-4">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Nombre <span class="text-red-500">*</span></label>
                            <input type="text" name="hospital_nombre"
                                   value="{{ old('hospital_nombre') }}"
                                   placeholder="Nombre de la institución"
                                   class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500" />
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700">Procedencia</label>
                                <select name="hospital_procedencia"
                                        class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                    <option value="">Sin especificar</option>
                                    <option value="PRIVADO" @selected(old('hospital_procedencia') === 'PRIVADO')>Privado</option>
                                    <option value="IMSS"    @selected(old('hospital_procedencia') === 'IMSS')>IMSS</option>
                                    <option value="ISSSTE"  @selected(old('hospital_procedencia') === 'ISSSTE')>ISSSTE</option>
                                    <option value="SSA"     @selected(old('hospital_procedencia') === 'SSA')>SSA</option>
                                </select>
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700">Ciudad</label>
                                <input type="text" name="hospital_ciudad"
                                       value="{{ old('hospital_ciudad') }}"
                                       class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500" />
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="hospital_modo" :value="hospitalModo" />
                </div>
            </div>

            {{-- ══════════════════════════════════════ --}}
            {{-- PASO 3 — Estudios                      --}}
            {{-- ══════════════════════════════════════ --}}
            <div x-show="paso === 3">
                <div class="rounded-xl border border-gray-200 bg-white p-6">
                    <h2 class="mb-4 text-sm font-semibold uppercase tracking-wide text-gray-700">Agrega los estudios</h2>

                    <input type="text"
                           x-model="busquedaEstudio"
                           placeholder="Buscar estudio por nombre o área..."
                           class="mb-3 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500" />

                    {{-- Resultados búsqueda --}}
                    <template x-if="true">
                        <div class="mb-4 flex flex-col gap-1">
                            <template x-for="e in estudiosFiltrados()" :key="e.id">
                                <button type="button"
                                        @click="agregarEstudio(e)"
                                        class="flex items-center justify-between rounded-lg border border-gray-200 px-4 py-2.5 text-left hover:border-gray-400 hover:bg-gray-50 transition-colors">
                                    <div>
                                        <span class="font-medium text-gray-900" x-text="e.nombre"></span>
                                        <span class="ml-2 text-xs text-gray-400" x-text="e.area_terapeutica"></span>
                                    </div>
                                    <span class="text-sm font-medium" style="color:#002745;"
                                          x-text="'$' + Number(e.precio_unitario).toLocaleString('es-MX', {minimumFractionDigits:2})"></span>
                                </button>
                            </template>
                            <p x-show="estudiosFiltrados().length === 0"
                               class="text-sm text-gray-400 px-1">No se encontraron estudios.</p>
                        </div>
                    </template>

                    {{-- Estudios seleccionados --}}
                    <template x-if="estudios.length > 0">
                        <div class="mb-4 flex flex-col gap-2">
                            <template x-for="(item, i) in estudios" :key="item.id">
                                <div class="flex items-center gap-3 rounded-lg border border-gray-200 p-3">
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-900" x-text="item.nombre"></p>
                                        <p class="text-xs text-gray-400"
                                           x-text="'$' + Number(item.precio_unitario).toLocaleString('es-MX', {minimumFractionDigits:2}) + ' c/u'"></p>
                                    </div>
                                    <input type="number"
                                           min="1"
                                           x-model.number="item.cantidad"
                                           @change="item.cantidad = Math.max(1, item.cantidad)"
                                           class="w-20 rounded-lg border border-gray-200 px-2 py-1 text-center text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500" />
                                    <span class="w-28 text-right text-sm font-medium text-gray-900"
                                          x-text="'$' + (item.precio_unitario * item.cantidad).toLocaleString('es-MX', {minimumFractionDigits:2})"></span>
                                    <button type="button" @click="quitarEstudio(i)"
                                            class="rounded-lg p-1 text-gray-400 hover:bg-red-50 hover:text-red-500 transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                        </svg>
                                    </button>

                                    {{-- Campos ocultos para el POST --}}
                                    <input type="hidden" :name="`estudios[${i}][id]`" :value="item.id" />
                                    <input type="hidden" :name="`estudios[${i}][cantidad]`" :value="item.cantidad" />
                                    <input type="hidden" :name="`estudios[${i}][precio]`" :value="item.precio_unitario" />
                                </div>
                            </template>
                        </div>
                    </template>

                    @error('estudios')
                        <p class="mb-4 text-sm text-red-500">{{ $message }}</p>
                    @enderror

                    {{-- Totales --}}
                    <template x-if="estudios.length > 0">
                        <div class="mb-4 flex flex-col items-end gap-1 border-t border-gray-200 pt-4">
                            <div class="flex w-full max-w-xs justify-between text-sm text-gray-600">
                                <span>Subtotal</span>
                                <span x-text="'$' + subtotal().toLocaleString('es-MX', {minimumFractionDigits:2})"></span>
                            </div>
                            <template x-if="Number(descuento) > 0">
                                <div class="flex w-full max-w-xs justify-between text-sm">
                                    <span class="text-gray-600" x-text="`Descuento (${descuento}%)`"></span>
                                    <span class="text-red-500"
                                          x-text="'-$' + descuentoImporte().toLocaleString('es-MX', {minimumFractionDigits:2})"></span>
                                </div>
                            </template>
                            <div class="flex w-full max-w-xs justify-between border-t border-gray-200 pt-2 text-base font-semibold text-gray-900">
                                <span>Total</span>
                                <span x-text="'$' + total().toLocaleString('es-MX', {minimumFractionDigits:2})"></span>
                            </div>
                        </div>
                    </template>

                    {{-- Opciones adicionales --}}
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 border-t border-gray-200 pt-4">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Descuento (%)</label>
                            <input type="number"
                                   name="descuento"
                                   x-model="descuento"
                                   min="0" max="100" step="0.5"
                                   placeholder="0"
                                   value="{{ old('descuento', 0) }}"
                                   class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500" />
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Válida hasta</label>
                            <input type="date"
                                   name="valida_hasta"
                                   value="{{ old('valida_hasta') }}"
                                   class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500" />
                        </div>
                    </div>
                    <div class="mt-4">
                        <label class="mb-1 block text-sm font-medium text-gray-700">Notas</label>
                        <textarea name="notas" rows="2"
                                  placeholder="Observaciones generales..."
                                  class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">{{ old('notas') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Navegación --}}
            <div class="mt-6 flex items-center justify-between">
                <button type="button"
                        @click="paso === 1 ? window.location.href='{{ route("cotizaciones.index") }}' : paso--"
                        class="inline-flex items-center gap-2 rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                    </svg>
                    <span x-text="paso === 1 ? 'Cancelar' : 'Anterior'"></span>
                </button>

                <button x-show="paso < 3"
                        type="button"
                        @click="siguientePaso()"
                        class="rounded-lg px-4 py-2 text-sm font-medium text-white hover:opacity-90 transition-colors"
                        style="background-color:#002745;">
                    Siguiente
                </button>

                <button x-show="paso === 3"
                        type="submit"
                        class="inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium text-white hover:opacity-90 transition-colors"
                        style="background-color:#002745;">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                    </svg>
                    Crear cotización
                </button>
            </div>
        </form>
    </div>

    <script>
        @php
            $medicosJs = $medicos->map(fn($m) => [
                'id'              => $m->id,
                'nombre_completo' => $m->nombre_completo,
                'especialidad'    => $m->especialidad ?? '',
                'hospital'        => $m->hospital?->nombre ?? '',
            ]);

            $hospitalesJs = $hospitales->map(fn($h) => [
                'id'          => $h->id,
                'nombre'      => $h->nombre,
                'procedencia' => $h->procedencia ?? '',
            ]);

            $estudiosJs = $estudios->map(fn($e) => [
                'id'              => $e->id,
                'nombre'          => $e->nombre,
                'area_terapeutica'=> $e->area_terapeutica ?? '',
                'precio_unitario' => (float) $e->precio_unitario,
            ]);
        @endphp

        const _medicos    = @json($medicosJs);
        const _hospitales = @json($hospitalesJs);
        const _estudios   = @json($estudiosJs);

        function wizardCotizacion(pasoInicial = 1) {
            return {
                paso: pasoInicial,
                pasos: ['Médico', 'Institución', 'Estudios'],

                // Paso 1
                medicoModo: '{{ old("medico_modo", "nuevo") }}',
                medicoId: {{ old('medico_id') ? old('medico_id') : 'null' }},
                medicoLabel: '',
                medicoEspecialidadLabel: '',
                busquedaMedico: '',

                // Paso 2
                hospitalModo: '{{ old("hospital_modo", "existente") }}',
                hospitalId: {{ old('hospital_id') ? old('hospital_id') : 'null' }},
                hospitalLabel: '',
                hospitalProcedenciaLabel: '',
                busquedaHospital: '',

                // Paso 3
                estudios: [],
                busquedaEstudio: '',
                descuento: {{ old('descuento', 0) }},

                init() {
                    // Repoblar médico seleccionado si viene de old()
                    if (this.medicoId) {
                        const m = _medicos.find(m => m.id == this.medicoId);
                        if (m) {
                            this.medicoLabel = m.nombre_completo;
                            this.medicoEspecialidadLabel = m.especialidad;
                        }
                    }
                    // Repoblar hospital seleccionado si viene de old()
                    if (this.hospitalId) {
                        const h = _hospitales.find(h => h.id == this.hospitalId);
                        if (h) {
                            this.hospitalLabel = h.nombre;
                            this.hospitalProcedenciaLabel = h.procedencia;
                        }
                    }
                },

                medicosFiltrados() {
                    const q = this.busquedaMedico.toLowerCase();
                    return _medicos.filter(m =>
                        !q || m.nombre_completo.toLowerCase().includes(q) ||
                        m.especialidad.toLowerCase().includes(q)
                    ).slice(0, 10);
                },

                seleccionarMedico(m) {
                    this.medicoId = m.id;
                    this.medicoLabel = m.nombre_completo;
                    this.medicoEspecialidadLabel = m.especialidad;
                    this.busquedaMedico = '';
                },

                hospitalesFiltrados() {
                    const q = this.busquedaHospital.toLowerCase();
                    return _hospitales.filter(h =>
                        !q || h.nombre.toLowerCase().includes(q)
                    ).slice(0, 10);
                },

                seleccionarHospital(h) {
                    this.hospitalId = h.id;
                    this.hospitalLabel = h.nombre;
                    this.hospitalProcedenciaLabel = h.procedencia;
                    this.busquedaHospital = '';
                },

                estudiosFiltrados() {
                    const q = this.busquedaEstudio.toLowerCase();
                    const ids = this.estudios.map(e => e.id);
                    return _estudios.filter(e =>
                        !ids.includes(e.id) &&
                        (!q || e.nombre.toLowerCase().includes(q) ||
                        e.area_terapeutica.toLowerCase().includes(q))
                    ).slice(0, 20);
                },

                agregarEstudio(e) {
                    if (this.estudios.find(s => s.id === e.id)) return;
                    this.estudios.push({ ...e, cantidad: 1 });
                    this.busquedaEstudio = '';
                },

                quitarEstudio(i) {
                    this.estudios.splice(i, 1);
                },

                subtotal() {
                    return this.estudios.reduce((s, e) => s + e.precio_unitario * e.cantidad, 0);
                },

                descuentoImporte() {
                    return this.subtotal() * (Number(this.descuento) / 100);
                },

                total() {
                    return this.subtotal() - this.descuentoImporte();
                },

                siguientePaso() {
                    if (this.paso === 1 && this.medicoModo === 'existente' && !this.medicoId) {
                        alert('Selecciona un médico para continuar.');
                        return;
                    }
                    if (this.paso === 1 && this.medicoModo === 'nuevo') {
                        const nombre = document.querySelector('[name="medico_nombre"]').value;
                        if (!nombre.trim()) {
                            alert('El nombre del médico es requerido.');
                            return;
                        }
                    }
                    if (this.paso === 3 && this.estudios.length === 0) {
                        alert('Agrega al menos un estudio.');
                        return;
                    }
                    if (this.paso < 3) this.paso++;
                },
            }
        }
    </script>

</x-layouts::app>