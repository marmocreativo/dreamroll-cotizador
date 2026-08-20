<x-layouts::app :title="__('Editar cotización') . ' ' . $cotizacion->folio">

    <!-- CDN de Quill -->
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet">

    <div class="mb-6 flex items-center gap-4">
        <a href="{{ route('cotizaciones.show', $cotizacion) }}"
           class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-semibold text-secondary">Editar {{ $cotizacion->folio }}</h1>
            <p class="text-sm text-gray-500">Actualiza los datos de la cotización</p>
        </div>
    </div>

    <div x-data="cotizacionWizard()" class="max-w-4xl">

        {{-- Stepper --}}
        <div class="mb-8 flex items-center">
            <template x-for="(label, index) in pasos" :key="index">
                <div class="flex items-center" :class="index < pasos.length - 1 ? 'flex-1' : ''">
                    <button type="button"
                            @click="irAPaso(index + 1)"
                            class="flex size-9 shrink-0 items-center justify-center rounded-full text-sm font-semibold transition-colors"
                            :class="paso >= index + 1 ? 'bg-secondary text-white' : 'bg-gray-100 text-gray-400'">
                        <span x-show="paso > index + 1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                            </svg>
                        </span>
                        <span x-show="paso <= index + 1" x-text="index + 1"></span>
                    </button>
                    <div class="ml-3 mr-4">
                        <p class="text-xs font-medium text-gray-400">Paso <span x-text="index + 1"></span></p>
                        <p class="text-sm font-medium text-gray-700" x-text="label"></p>
                    </div>
                    <template x-if="index < pasos.length - 1">
                        <div class="h-px flex-1 bg-gray-200"></div>
                    </template>
                </div>
            </template>
        </div>

        {{-- Alertas de validación --}}
        <div x-show="errores.length > 0" x-cloak class="mb-4 rounded-lg border border-red-200 bg-red-50 p-4">
            <p class="text-sm font-medium text-red-700">Corrige lo siguiente antes de continuar:</p>
            <ul class="mt-1 list-disc pl-5 text-sm text-red-600">
                <template x-for="error in errores" :key="error">
                    <li x-text="error"></li>
                </template>
            </ul>
        </div>

        <form method="POST" action="{{ route('cotizaciones.update', $cotizacion) }}" enctype="multipart/form-data" @submit="enviarFormulario($event)">
            @csrf
            @method('PUT')

            {{-- ── PASO 1: Cliente ───────────────────────────────── --}}
            <div x-show="paso === 1" x-cloak class="rounded-xl border border-gray-200 bg-white p-6 space-y-4">
                <p class="text-sm font-medium text-gray-700">Cliente</p>

                <input type="hidden" name="cliente_id" :value="cliente.id ?? ''">

                <div x-show="!cliente.id && !clienteNuevoModo" x-cloak>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Buscar cliente</label>
                    <div class="flex gap-2">
                        <input type="text" x-model="busquedaCliente"
                               @keydown.enter.prevent="buscarClientes()"
                               placeholder="Busca por empresa, nombre o RFC y presiona Enter o Buscar..."
                               autocomplete="off"
                               class="flex-1 rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary" />
                        <button type="button" @click="buscarClientes()"
                                class="shrink-0 rounded-lg bg-secondary px-4 py-2 text-sm font-medium text-white transition-colors hover:opacity-90">
                            Buscar
                        </button>
                    </div>
                </div>

                <button type="button" x-show="!cliente.id && !clienteNuevoModo" @click="activarClienteNuevo()"
                        class="text-sm font-medium text-secondary hover:underline">
                    + Agregar nuevo cliente
                </button>

                <div x-show="cliente.id" x-cloak class="flex items-center justify-between rounded-lg bg-gray-50 px-3 py-2">
                    <div>
                        <p class="text-sm font-medium text-gray-900" x-text="cliente.empresa"></p>
                        <p class="text-xs text-gray-400" x-text="'RFC: ' + (cliente.rfc || '—')"></p>
                    </div>
                    <button type="button" @click="quitarClienteSeleccionado()" class="text-xs font-medium text-gray-500 hover:text-red-600">
                        Cambiar
                    </button>
                </div>

                <template x-if="clienteNuevoModo">
                    <div class="space-y-4 rounded-lg border border-dashed border-gray-200 p-4">
                        <div class="flex items-center justify-between">
                            <p class="text-xs font-medium uppercase tracking-wide text-gray-400">Datos fiscales del nuevo cliente</p>
                            <button type="button" @click="quitarClienteSeleccionado()" class="text-xs text-gray-400 hover:text-red-600">Cancelar</button>
                        </div>

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700">
                                    Empresa <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="cliente_nuevo[empresa]" x-model="cliente.empresa"
                                       class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary" />
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700">RFC</label>
                                <input type="text" name="cliente_nuevo[rfc]" x-model="cliente.rfc"
                                       class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700">Régimen fiscal</label>
                                <input type="text" name="cliente_nuevo[regimen_fiscal]" x-model="cliente.regimenFiscal"
                                       class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary" />
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700">Uso de CFDI</label>
                                <input type="text" name="cliente_nuevo[uso_cfdi]" x-model="cliente.usoCfdi"
                                       class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary" />
                            </div>
                        </div>
                    </div>
                </template>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-4">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Prefijo</label>
                        <select name="cliente_prefijo" x-model="cliente.prefijo"
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary">
                            <option value="">—</option>
                            <option>Sr.</option>
                            <option>Sra.</option>
                            <option>Dr.</option>
                            <option>Dra.</option>
                            <option>Ing.</option>
                            <option>Lic.</option>
                        </select>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="mb-1 block text-sm font-medium text-gray-700">
                            Nombre de contacto <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="cliente_nombre" x-model="cliente.nombre"
                               class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary" />
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Apellidos</label>
                        <input type="text" name="cliente_apellidos" x-model="cliente.apellidos"
                               class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary" />
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Puesto</label>
                        <input type="text" name="cliente_puesto" x-model="cliente.puesto"
                               class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary" />
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div x-show="!clienteNuevoModo">
                        <label class="mb-1 block text-sm font-medium text-gray-700">Empresa</label>
                        <input type="text" name="cliente_empresa" x-model="cliente.empresa"
                               class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary" />
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Teléfono</label>
                        <input type="text" name="cliente_telefono" x-model="cliente.telefono"
                               placeholder="Incluye lada, ej. 5215512345678"
                               class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary" />
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" name="cliente_email" x-model="cliente.email"
                               class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary" />
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Dirección</label>
                        <input type="text" name="cliente_direccion" x-model="cliente.direccion"
                               class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary" />
                    </div>
                </div>
            </div>

            {{-- ── PASO 2: Productos ─────────────────────────────── --}}
            <div x-show="paso === 2" x-cloak class="rounded-xl border border-gray-200 bg-white p-6 space-y-4">
                <p class="text-sm font-medium text-gray-700">Productos/Servicios</p>

                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Buscar o agregar producto o servicio</label>
                    <div class="flex gap-2">
                        <input type="text" x-model="busqueda"
                               @keydown.enter.prevent="buscarProductos()"
                               placeholder="Escribe el nombre y presiona Enter o Buscar..."
                               autocomplete="off"
                               class="flex-1 rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary" />
                        <button type="button" @click="buscarProductos()"
                                class="shrink-0 rounded-lg bg-secondary px-4 py-2 text-sm font-medium text-white transition-colors hover:opacity-90">
                            Buscar
                        </button>
                    </div>
                </div>

                <div class="overflow-hidden rounded-lg border border-gray-200">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-left">
                            <tr>
                                <th class="px-3 py-2 text-xs font-medium uppercase tracking-wide text-gray-500 w-14">Img</th>
                                <th class="px-3 py-2 text-xs font-medium uppercase tracking-wide text-gray-500">Producto/Servicio</th>
                                <th class="px-3 py-2 text-xs font-medium uppercase tracking-wide text-gray-500 w-24">Cantidad</th>
                                <th class="px-3 py-2 text-xs font-medium uppercase tracking-wide text-gray-500 w-24">Costo</th>
                                <th class="px-3 py-2 text-xs font-medium uppercase tracking-wide text-gray-500 w-20">Aum. %</th>
                                <th class="px-3 py-2 text-xs font-medium uppercase tracking-wide text-gray-500 w-28">Precio unit.</th>
                                <th class="px-3 py-2 text-xs font-medium uppercase tracking-wide text-gray-500 w-32">Subtotal</th>
                                <th class="px-3 py-2 w-10"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <template x-for="(item, index) in items" :key="item.uid">
                                <tr>
                                    <td class="px-3 py-2">
                                        <div @click="abrirDialogFoto(index)"
                                             class="group relative block size-10 cursor-pointer overflow-hidden rounded border border-gray-200 bg-gray-50">
                                            <img :src="item.imagenPreview || item.imagen_url" x-show="item.imagenPreview || item.imagen_url"
                                                 class="size-full object-cover" />
                                            <span x-show="!item.imagenPreview && !item.imagen_url"
                                                  class="flex size-full items-center justify-center text-gray-300">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909M3 15h18M2.25 4.5h19.5M4.5 4.5v15h15v-15" />
                                                </svg>
                                            </span>
                                        </div>
                                        <input type="file" accept="image/*" class="hidden"
                                               :id="'foto-input-' + item.uid"
                                               :name="'productos['+index+'][imagen]'"
                                               @change="onFileSeleccionado($event, index)" />
                                    </td>
                                    <td class="px-3 py-2">
                                        <input type="text" x-model="item.nombre"
                                               :name="'productos['+index+'][nombre]'"
                                               class="w-full rounded border border-gray-200 px-2 py-1 text-sm text-gray-900" />
                                        <input type="hidden" :name="'productos['+index+'][id]'" :value="item.id ?? ''">
                                    </td>
                                    <td class="px-3 py-2">
                                        <input type="number" min="1" x-model.number="item.cantidad" @input="calcularTotales"
                                               :name="'productos['+index+'][cantidad]'"
                                               class="w-full rounded border border-gray-200 px-2 py-1 text-sm text-gray-900" />
                                    </td>
                                    <td class="px-3 py-2">
                                        <input type="number" min="0" step="0.01" x-model.number="item.costo"
                                               @input="calcularPrecioItem(index)"
                                               :name="'productos['+index+'][costo]'"
                                               class="w-full rounded border border-gray-200 px-2 py-1 text-sm text-gray-900" />
                                    </td>
                                    <td class="px-3 py-2">
                                        <input type="number" min="0" step="0.01" x-model.number="item.aumento_porcentaje"
                                               @input="calcularPrecioItem(index)"
                                               :name="'productos['+index+'][aumento_porcentaje]'"
                                               class="w-full rounded border border-gray-200 px-2 py-1 text-sm text-gray-900" />
                                    </td>
                                    <td class="px-3 py-2">
                                        <input type="number" min="0" step="0.01" x-model.number="item.precio" @input="calcularTotales"
                                               :name="'productos['+index+'][precio]'"
                                               class="w-full rounded border border-gray-200 px-2 py-1 text-sm text-gray-900" />
                                    </td>
                                    <td class="px-3 py-2 font-medium text-gray-900">
                                        $<span x-text="((item.cantidad || 0) * (item.precio || 0)).toFixed(2)"></span>
                                    </td>
                                    <td class="px-3 py-2 text-right">
                                        <button type="button" @click="quitarProducto(index)" class="text-gray-400 hover:text-red-600 transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                            <tr x-show="items.length === 0">
                                <td colspan="8" class="px-3 py-8 text-center text-gray-400 text-sm">
                                    Aún no has agregado productos.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="flex justify-end">
                    <div class="w-full max-w-xs space-y-2 rounded-lg border border-gray-200 p-4">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500">Subtotal</span>
                            <span class="font-medium text-gray-900">$<span x-text="subtotal.toFixed(2)"></span></span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <label class="text-gray-500">Fee de agencia (%)</label>
                            <input type="number" name="fee_porcentaje" min="0" max="100" step="0.01"
                                   x-model.number="feePorcentaje" @input="calcularTotales"
                                   class="w-20 rounded border border-gray-200 px-2 py-1 text-right text-sm text-gray-900" />
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <label class="text-gray-500">Descuento (%)</label>
                            <input type="number" name="descuento" min="0" max="100" step="0.01"
                                   x-model.number="descuento" @input="calcularTotales"
                                   class="w-20 rounded border border-gray-200 px-2 py-1 text-right text-sm text-gray-900" />
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500">IVA (16%)</span>
                            <span class="font-medium text-gray-900">$<span x-text="iva.toFixed(2)"></span></span>
                        </div>
                        <div class="flex items-center justify-between border-t border-gray-200 pt-2 text-base">
                            <span class="font-semibold text-gray-900">Total</span>
                            <span class="font-semibold text-secondary">$<span x-text="total.toFixed(2)"></span></span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── PASO 3: Entrega y condiciones ─────────────────── --}}
            <div x-show="paso === 3" x-cloak class="rounded-xl border border-gray-200 bg-white p-6 space-y-4">
                <p class="text-sm font-medium text-gray-700">Tiempo de entrega y condiciones</p>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Tiempo de entrega</label>
                        <input type="text" name="tiempo_entrega" x-model="entrega.tiempo"
                               placeholder="Ej. 10 días hábiles"
                               class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary" />
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Válida hasta</label>
                        <input type="date" name="valida_hasta" x-model="entrega.validaHasta"
                               class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary" />
                    </div>
                </div>

                <div>
                    <label for="condiciones" class="mb-1 block text-sm font-medium text-gray-700">Condiciones</label>
                    <div id="condiciones-editor" style="height: 150px; background: #fff;">
                        {!! old('condiciones', $cotizacion->condiciones ?? '') !!}
                    </div>
                    <!-- Este input hidden es el que realmente se manda en el submit -->
                    <input type="hidden" name="condiciones" id="condiciones-input">
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Notas internas</label>
                    <textarea name="notas" x-model="entrega.notas" rows="2"
                              placeholder="Notas que no se muestran al cliente"
                              class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"></textarea>
                </div>

                <div class="rounded-lg bg-gray-50 p-4">
                    <p class="mb-2 text-xs font-medium uppercase tracking-wide text-gray-500">Resumen</p>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-600">Cliente</span>
                        <span class="font-medium text-gray-900" x-text="(cliente.empresa || (cliente.nombre + ' ' + cliente.apellidos))"></span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-600">Productos</span>
                        <span class="font-medium text-gray-900" x-text="items.length"></span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-600">Total</span>
                        <span class="font-semibold text-secondary">$<span x-text="total.toFixed(2)"></span></span>
                    </div>
                </div>
            </div>

            {{-- Navegación --}}
            <div class="mt-6 flex items-center justify-between">
                <button type="button" @click="pasoAnterior" x-show="paso > 1"
                        class="rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                    Atrás
                </button>
                <span x-show="paso === 1"></span>

                <button type="button" @click="siguientePaso" x-show="paso < 3"
                        class="rounded-lg bg-secondary px-4 py-2 text-sm font-medium text-white transition-colors hover:opacity-90">
                    Siguiente
                </button>
                <button type="submit" x-show="paso === 3"
                        class="rounded-lg bg-secondary px-4 py-2 text-sm font-medium text-white transition-colors hover:opacity-90">
                    Guardar cambios
                </button>
            </div>
        </form>

        {{-- ── DIÁLOGO: Resultados de búsqueda de cliente ─────────── --}}
        <div x-show="mostrarDialogBusquedaCliente" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4"
             @keydown.escape.window="cerrarDialogBusquedaCliente()">
            <div @click.outside="cerrarDialogBusquedaCliente()"
                 class="w-full max-w-md rounded-xl bg-white shadow-xl">
                <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4">
                    <p class="text-sm font-semibold text-gray-900">
                        Resultados para "<span x-text="terminoBuscadoCliente"></span>"
                    </p>
                    <button type="button" @click="cerrarDialogBusquedaCliente()" class="text-gray-400 hover:text-gray-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="max-h-80 overflow-y-auto p-2">
                    <template x-if="resultadosBusquedaCliente.length === 0">
                        <div class="px-3 py-6 text-center">
                            <p class="text-sm text-gray-500">No se encontraron clientes con ese nombre.</p>
                        </div>
                    </template>

                    <template x-for="c in resultadosBusquedaCliente" :key="c.id">
                        <button type="button" @click="seleccionarClienteBusqueda(c)"
                                class="flex w-full flex-col items-start rounded-lg px-3 py-2 text-left hover:bg-gray-50 transition-colors">
                            <span class="text-sm text-gray-900" x-text="c.empresa"></span>
                            <span class="text-xs text-gray-400" x-text="c.contacto_nombre + ' ' + (c.contacto_apellidos || '') + (c.rfc ? ' · ' + c.rfc : '')"></span>
                        </button>
                    </template>
                </div>

                <div class="border-t border-gray-100 p-4">
                    <button type="button" @click="crearClienteNuevoDesdeBusqueda()"
                            class="flex w-full items-center justify-center gap-2 rounded-lg border border-dashed border-gray-300 px-4 py-2 text-sm font-medium text-secondary hover:bg-gray-50 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        <span x-show="resultadosBusquedaCliente.length === 0">
                            Agregar "<span x-text="terminoBuscadoCliente"></span>" como nuevo cliente
                        </span>
                        <span x-show="resultadosBusquedaCliente.length > 0">
                            Crear cliente nuevo "<span x-text="terminoBuscadoCliente"></span>"
                        </span>
                    </button>
                </div>
            </div>
        </div>

        {{-- ── DIÁLOGO: Resultados de búsqueda de producto ────────── --}}
        <div x-show="mostrarDialogBusqueda" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4"
             @keydown.escape.window="cerrarDialogBusqueda()">
            <div @click.outside="cerrarDialogBusqueda()"
                 class="w-full max-w-md rounded-xl bg-white shadow-xl">
                <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4">
                    <p class="text-sm font-semibold text-gray-900">
                        Resultados para "<span x-text="terminoBuscado"></span>"
                    </p>
                    <button type="button" @click="cerrarDialogBusqueda()" class="text-gray-400 hover:text-gray-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="max-h-80 overflow-y-auto p-2">
                    <template x-if="resultadosBusqueda.length === 0">
                        <div class="px-3 py-6 text-center">
                            <p class="text-sm text-gray-500">No se encontraron productos con ese nombre.</p>
                        </div>
                    </template>

                    <template x-for="producto in resultadosBusqueda" :key="producto.id">
                        <button type="button" @click="seleccionarProductoBusqueda(producto)"
                                class="flex w-full items-center gap-3 rounded-lg px-3 py-2 text-left hover:bg-gray-50 transition-colors">
                            <img :src="producto.imagen_url" x-show="producto.imagen_url" class="size-9 rounded object-cover border border-gray-200">
                            <div class="flex-1">
                                <p class="text-sm text-gray-900" x-text="producto.nombre"></p>
                                <p class="text-xs text-gray-400">$<span x-text="Number(producto.precio_unitario).toFixed(2)"></span></p>
                            </div>
                        </button>
                    </template>
                </div>

                <div class="border-t border-gray-100 p-4">
                    <button type="button" @click="crearProductoNuevoDesdeBusqueda()"
                            class="flex w-full items-center justify-center gap-2 rounded-lg border border-dashed border-gray-300 px-4 py-2 text-sm font-medium text-secondary hover:bg-gray-50 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        <span x-show="resultadosBusqueda.length === 0">
                            Agregar "<span x-text="terminoBuscado"></span>" como nuevo producto
                        </span>
                        <span x-show="resultadosBusqueda.length > 0">
                            Crear variación nueva "<span x-text="terminoBuscado"></span>"
                        </span>
                    </button>
                </div>
            </div>
        </div>

        {{-- ── DIÁLOGO: Foto del producto ──────────────────────────── --}}
        <div x-show="mostrarDialogFoto" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4"
             @keydown.escape.window="cerrarDialogFoto()"
             @paste.window="pegarDesdeEvento($event)">
            <div @click.outside="cerrarDialogFoto()"
                 class="w-full max-w-sm rounded-xl bg-white shadow-xl">
                <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4">
                    <p class="text-sm font-semibold text-gray-900">Imagen del producto</p>
                    <button type="button" @click="cerrarDialogFoto()" class="text-gray-400 hover:text-gray-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="space-y-4 p-5">
                    <div x-ref="pasteZone" contenteditable="true" tabindex="-1"
                         style="position:fixed; top:-9999px; left:-9999px; width:1px; height:1px; overflow:hidden;"
                         aria-hidden="true"></div>

                    <div @dragover.prevent="draggingFoto = true"
                         @dragleave.prevent="draggingFoto = false"
                         @drop.prevent="onDropFoto($event)"
                         :class="draggingFoto ? 'border-secondary bg-gray-50' : 'border-gray-200'"
                         class="flex flex-col items-center justify-center gap-2 rounded-lg border-2 border-dashed px-4 py-8 text-center transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-8 text-gray-300" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                        </svg>
                        <p class="text-sm text-gray-500">Arrastra y suelta una imagen aquí</p>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <button type="button" @click="abrirExplorador()"
                                class="flex items-center justify-center gap-2 rounded-lg border border-gray-200 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 0 1 4.5 9.75h15A2.25 2.25 0 0 1 21.75 12v.75m-19.5 0v6a2.25 2.25 0 0 0 2.25 2.25h15a2.25 2.25 0 0 0 2.25-2.25v-6m-19.5 0h19.5" />
                            </svg>
                            Explorador
                        </button>
                        <button type="button" @click="pegarDesdePortapapeles()"
                                class="flex items-center justify-center gap-2 rounded-lg border border-gray-200 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.666 3.888A2.25 2.25 0 0 0 13.5 2.25h-3a2.25 2.25 0 0 0-2.166 1.638m7.332 0c.055.194.084.4.084.612v0a.75.75 0 0 1-.75.75h-6a.75.75 0 0 1-.75-.75v0c0-.212.03-.418.084-.612m7.332 0c.646.049 1.288.11 1.927.184 1.1.128 1.907 1.077 1.907 2.185V19.5a2.25 2.25 0 0 1-2.25 2.25H6.75A2.25 2.25 0 0 1 4.5 19.5V6.257c0-1.108.806-2.057 1.907-2.185a48.208 48.208 0 0 1 1.927-.184" />
                            </svg>
                            Pegar imagen
                        </button>
                    </div>
                    <p class="text-center text-xs text-gray-400">
                        Puedes copiar la imagen desde Word y presionar <kbd class="rounded border border-gray-200 px-1">Ctrl</kbd>+<kbd class="rounded border border-gray-200 px-1">V</kbd> con este diálogo abierto.
                    </p>
                    <p x-show="errorFoto" x-text="errorFoto" class="text-center text-xs text-red-500"></p>
                </div>
            </div>
        </div>
    </div>

    @php
        $clienteData = [
            'id' => $cotizacion->cliente_id,
            'prefijo' => $cotizacion->cliente_prefijo,
            'nombre' => $cotizacion->cliente_nombre,
            'apellidos' => $cotizacion->cliente_apellidos,
            'puesto' => $cotizacion->cliente_puesto,
            'empresa' => $cotizacion->cliente_empresa,
            'telefono' => $cotizacion->cliente_telefono,
            'email' => $cotizacion->cliente_email,
            'direccion' => $cotizacion->cliente_direccion,
            'rfc' => $cotizacion->cliente->rfc ?? null,
            'regimenFiscal' => $cotizacion->cliente->regimen_fiscal ?? null,
            'usoCfdi' => $cotizacion->cliente->uso_cfdi ?? null,
        ];

        $entregaData = [
            'tiempo' => $cotizacion->tiempo_entrega,
            'validaHasta' => optional($cotizacion->valida_hasta)->toDateString(),
            'notas' => $cotizacion->notas,
        ];

        $itemsData = $cotizacion->productos->map(function ($item) {
            $nombreProducto = $item->producto->nombre ?? 'producto eliminado';

            return [
                'uid' => $item->id,
                'id' => $item->producto_id,
                'nombre' => $nombreProducto,
                'cantidad' => $item->cantidad,
                'costo' => $item->costo !== null ? (float) $item->costo : '',
                'aumento_porcentaje' => $item->aumento_porcentaje !== null ? (float) $item->aumento_porcentaje : '',
                'precio' => (float) $item->precio_unitario,
                'imagen_url' => $item->producto->imagen_url ?? null,
                'imagenPreview' => null,
            ];
        })->values();

        $siguienteUidValor = $cotizacion->productos->max('id') + 1;
    @endphp

    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
    <script>
        function cotizacionWizard() {
            return {
                paso: 1,
                pasos: ['Cliente', 'Productos', 'Entrega y condiciones'],
                errores: [],
                quill: null,

                cliente: @json($clienteData),
                clienteNuevoModo: false,

                busquedaCliente: '',
                terminoBuscadoCliente: '',
                resultadosBusquedaCliente: [],
                mostrarDialogBusquedaCliente: false,

                entrega: @json($entregaData),

                busqueda: '',
                terminoBuscado: '',
                resultadosBusqueda: [],
                mostrarDialogBusqueda: false,

                mostrarDialogFoto: false,
                fotoEditIndex: null,
                draggingFoto: false,
                errorFoto: '',

                items: @json($itemsData),
                siguienteUid: {{ $siguienteUidValor }},

                feePorcentaje: 10,
                descuento: {{ $cotizacion->descuento }},
                subtotal: 0,
                iva: 0,
                total: 0,

                init() {
                    // Limpia texto pegado desde Word en cualquier input/textarea del wizard
                    this.$el.addEventListener('paste', (e) => {
                        const el = e.target;
                        if (!(el instanceof HTMLInputElement) && !(el instanceof HTMLTextAreaElement)) return;
                        if (el.type === 'file') return;

                        setTimeout(() => {
                            const limpio = this.limpiarTextoWord(el.value);
                            if (limpio !== el.value) {
                                el.value = limpio;
                                el.dispatchEvent(new Event('input', { bubbles: true }));
                            }
                        }, 0);
                    });

                    // Inicializa Quill una sola vez, aquí en el init de Alpine
                    this.quill = new Quill('#condiciones-editor', {
                        theme: 'snow',
                        modules: {
                            toolbar: [
                                ['bold', 'italic', 'underline'],
                                [{ list: 'ordered' }, { list: 'bullet' }],
                                ['clean']
                            ]
                        }
                    });

                    this.calcularTotales();
                },

                // Sincroniza Quill al hidden input y valida antes de enviar
                enviarFormulario(event) {
                    document.querySelector('#condiciones-input').value = this.quill.root.innerHTML;

                    if (!this.validarPaso(3)) {
                        event.preventDefault();
                    }
                },

                limpiarTextoWord(texto) {
                    if (!texto) return texto;
                    return texto
                        .replace(/[\u2018\u2019]/g, "'")
                        .replace(/[\u201C\u201D]/g, '"')
                        .replace(/[\u2013\u2014]/g, '-')
                        .replace(/\u2026/g, '...')
                        .replace(/[\u00A0\u200B\uFEFF]/g, ' ')
                        .replace(/\r\n/g, '\n')
                        .trim();
                },

                async buscarClientes() {
                    const termino = this.busquedaCliente.trim();
                    if (termino.length < 2) return;

                    this.terminoBuscadoCliente = termino;

                    try {
                        const res = await fetch(`{{ route('clientes.buscar') }}?q=${encodeURIComponent(termino)}`);
                        this.resultadosBusquedaCliente = await res.json();
                    } catch (e) {
                        this.resultadosBusquedaCliente = [];
                    }

                    this.mostrarDialogBusquedaCliente = true;
                },

                cerrarDialogBusquedaCliente() {
                    this.mostrarDialogBusquedaCliente = false;
                },

                seleccionarClienteBusqueda(c) {
                    this.seleccionarCliente(c);
                    this.cerrarDialogBusquedaCliente();
                },

                crearClienteNuevoDesdeBusqueda() {
                    this.activarClienteNuevo(this.terminoBuscadoCliente);
                    this.cerrarDialogBusquedaCliente();
                },

                seleccionarCliente(c) {
                    this.cliente = {
                        id: c.id,
                        prefijo: c.contacto_prefijo || '',
                        nombre: c.contacto_nombre || '',
                        apellidos: c.contacto_apellidos || '',
                        puesto: c.contacto_puesto || '',
                        empresa: c.empresa || '',
                        telefono: c.contacto_telefono || '',
                        email: c.contacto_email || '',
                        direccion: c.direccion_fiscal || '',
                        rfc: c.rfc || '',
                        regimenFiscal: '',
                        usoCfdi: '',
                    };
                    this.clienteNuevoModo = false;
                    this.busquedaCliente = '';
                },

                activarClienteNuevo(nombreSugerido = '') {
                    this.clienteNuevoModo = true;
                    this.cliente.id = null;
                    this.cliente.empresa = this.cliente.empresa || nombreSugerido || this.busquedaCliente;
                    this.busquedaCliente = '';
                },

                quitarClienteSeleccionado() {
                    this.cliente = {
                        id: null, prefijo: '', nombre: '', apellidos: '', puesto: '', empresa: '',
                        telefono: '', email: '', direccion: '', rfc: '', regimenFiscal: '', usoCfdi: '',
                    };
                    this.clienteNuevoModo = false;
                },

                async buscarProductos() {
                    const termino = this.busqueda.trim();
                    if (termino.length < 2) return;

                    this.terminoBuscado = termino;

                    try {
                        const res = await fetch(`{{ route('productos.buscar') }}?q=${encodeURIComponent(termino)}`);
                        this.resultadosBusqueda = await res.json();
                    } catch (e) {
                        this.resultadosBusqueda = [];
                    }

                    this.mostrarDialogBusqueda = true;
                },

                cerrarDialogBusqueda() {
                    this.mostrarDialogBusqueda = false;
                },

                seleccionarProductoBusqueda(producto) {
                    this.agregarProducto(producto);
                    this.cerrarDialogBusqueda();
                    this.busqueda = '';
                },

                crearProductoNuevoDesdeBusqueda() {
                    this.agregarProductoNuevo(this.terminoBuscado);
                    this.cerrarDialogBusqueda();
                    this.busqueda = '';
                },

                agregarProducto(producto) {
                    this.items.push({
                        uid: this.siguienteUid++,
                        id: producto.id,
                        nombre: producto.nombre,
                        cantidad: 1,
                        costo: producto.costo ?? '',
                        aumento_porcentaje: producto.aumento_porcentaje ?? '',
                        precio: parseFloat(producto.precio_unitario),
                        imagen_url: producto.imagen_url || null,
                        imagenPreview: null,
                    });
                    this.calcularTotales();
                },

                agregarProductoNuevo(nombre) {
                    this.items.push({
                        uid: this.siguienteUid++,
                        id: null,
                        nombre: nombre,
                        cantidad: 1,
                        costo: '',
                        aumento_porcentaje: '',
                        precio: 0,
                        imagen_url: null,
                        imagenPreview: null,
                    });
                    this.calcularTotales();
                },

                calcularPrecioItem(index) {
                    const item = this.items[index];
                    const costo = parseFloat(item.costo) || 0;
                    const aumento = parseFloat(item.aumento_porcentaje) || 0;
                    item.precio = parseFloat((costo * (1 + aumento / 100)).toFixed(2));
                    this.calcularTotales();
                },

                quitarProducto(index) {
                    this.items.splice(index, 1);
                    this.calcularTotales();
                },

                calcularTotales() {
                    this.subtotal = this.items.reduce((sum, item) => sum + ((item.cantidad || 0) * (item.precio || 0)), 0);
                    const fee = this.subtotal * ((this.feePorcentaje || 0) / 100);
                    const subtotalConFee = this.subtotal + fee;
                    const conDescuento = subtotalConFee - (subtotalConFee * ((this.descuento || 0) / 100));
                    this.iva = conDescuento * 0.16;
                    this.total = conDescuento + this.iva;
                },

                abrirDialogFoto(index) {
                    this.fotoEditIndex = index;
                    this.errorFoto = '';
                    this.draggingFoto = false;
                    this.mostrarDialogFoto = true;
                    this.$nextTick(() => this.$refs.pasteZone?.focus());
                },

                cerrarDialogFoto() {
                    this.mostrarDialogFoto = false;
                    this.fotoEditIndex = null;
                    this.draggingFoto = false;
                    if (this.$refs.pasteZone) this.$refs.pasteZone.innerHTML = '';
                },

                abrirExplorador() {
                    const item = this.items[this.fotoEditIndex];
                    if (!item) return;
                    document.getElementById('foto-input-' + item.uid).click();
                },

                onFileSeleccionado(event, index) {
                    const file = event.target.files[0];
                    if (!file) return;
                    this.items[index].imagenPreview = URL.createObjectURL(file);
                    this.cerrarDialogFoto();
                },

                onDropFoto(event) {
                    this.draggingFoto = false;
                    const file = event.dataTransfer.files[0];
                    if (!file) return;
                    if (!file.type.startsWith('image/')) {
                        this.errorFoto = 'El archivo debe ser una imagen.';
                        return;
                    }
                    this.asignarArchivoAItem(file);
                },

                async pegarDesdePortapapeles() {
                    this.errorFoto = '';

                    try {
                        const items = await navigator.clipboard.read();
                        for (const clipboardItem of items) {
                            const tipoImagen = clipboardItem.types.find(t => t.startsWith('image/'));
                            if (tipoImagen) {
                                const blob = await clipboardItem.getType(tipoImagen);
                                const file = new File([blob], 'pegado.png', { type: tipoImagen });
                                this.asignarArchivoAItem(file);
                                return;
                            }
                        }
                    } catch (e) {
                        // Sigue al siguiente método
                    }

                    if (this.$refs.pasteZone) {
                        this.$refs.pasteZone.innerHTML = '';
                        this.$refs.pasteZone.focus();
                    }
                    this.errorFoto = 'Listo. Ahora presiona Ctrl+V para pegar la imagen.';
                },

                pegarDesdeEvento(event) {
                    if (!this.mostrarDialogFoto) return;

                    const clipboardItems = event.clipboardData?.items;
                    if (clipboardItems) {
                        for (const clipboardItem of clipboardItems) {
                            if (clipboardItem.type.startsWith('image/')) {
                                const file = clipboardItem.getAsFile();
                                if (file) {
                                    this.asignarArchivoAItem(file);
                                    return;
                                }
                            }
                        }
                    }

                    this.errorFoto = '';
                    setTimeout(() => this.procesarPasteZone(), 60);
                },

                async procesarPasteZone() {
                    const zona = this.$refs.pasteZone;
                    if (!zona) return;

                    const img = zona.querySelector('img');
                    if (!img || !img.src) {
                        this.errorFoto = 'No se detectó ninguna imagen en el portapapeles.';
                        zona.innerHTML = '';
                        return;
                    }

                    try {
                        const file = await this.rasterizarImagen(img.src);
                        zona.innerHTML = '';
                        this.asignarArchivoAItem(file);
                    } catch (e) {
                        this.errorFoto = 'No se pudo procesar la imagen pegada. Intenta de nuevo.';
                        zona.innerHTML = '';
                    }
                },

                rasterizarImagen(src) {
                    return new Promise((resolve, reject) => {
                        const imagen = new Image();
                        imagen.onload = () => {
                            const canvas = document.createElement('canvas');
                            canvas.width = imagen.naturalWidth || imagen.width;
                            canvas.height = imagen.naturalHeight || imagen.height;
                            const ctx = canvas.getContext('2d');
                            ctx.drawImage(imagen, 0, 0);
                            canvas.toBlob((blob) => {
                                if (!blob) {
                                    reject(new Error('No se pudo generar la imagen.'));
                                    return;
                                }
                                resolve(new File([blob], 'pegado.png', { type: 'image/png' }));
                            }, 'image/png');
                        };
                        imagen.onerror = () => reject(new Error('No se pudo cargar la imagen pegada.'));
                        imagen.src = src;
                    });
                },

                asignarArchivoAItem(file) {
                    const item = this.items[this.fotoEditIndex];
                    if (!item) return;

                    const input = document.getElementById('foto-input-' + item.uid);
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(file);
                    input.files = dataTransfer.files;

                    item.imagenPreview = URL.createObjectURL(file);
                    this.cerrarDialogFoto();
                },

                irAPaso(n) {
                    if (n < this.paso || this.validarPaso(this.paso)) {
                        this.paso = n;
                    }
                },

                siguientePaso() {
                    if (this.validarPaso(this.paso)) {
                        this.paso++;
                    }
                },

                pasoAnterior() {
                    this.paso--;
                },

                validarPaso(n) {
                    this.errores = [];

                    if (n === 1) {
                        if (this.clienteNuevoModo && !this.cliente.empresa.trim()) {
                            this.errores.push('La empresa del nuevo cliente es requerida.');
                        }
                        if (this.clienteNuevoModo && this.cliente.empresa.length > 150) {
                            this.errores.push('El nombre de la empresa es demasiado largo (' + this.cliente.empresa.length + '/150). Revisa si pegaste texto de más desde Word.');
                        }
                        if (!this.cliente.nombre.trim()) {
                            this.errores.push('El nombre de contacto es requerido.');
                        }
                        if (this.cliente.nombre.length > 100) {
                            this.errores.push('El nombre de contacto es demasiado largo (' + this.cliente.nombre.length + '/100). Revisa si pegaste texto de más desde Word.');
                        }
                    }

                    if (n === 2) {
                        if (this.items.length === 0) {
                            this.errores.push('Agrega al menos un producto.');
                        }
                        this.items.forEach((item, i) => {
                            if (!item.nombre || !item.nombre.trim()) {
                                this.errores.push(`El producto #${i + 1} necesita un nombre.`);
                            }
                            if (item.nombre && item.nombre.length > 150) {
                                this.errores.push(`El nombre del producto #${i + 1} es demasiado largo (${item.nombre.length}/150). Revisa si pegaste texto de más desde Word.`);
                            }
                            if (!item.cantidad || item.cantidad < 1) {
                                this.errores.push(`El producto #${i + 1} necesita una cantidad válida.`);
                            }
                        });
                    }

                    return this.errores.length === 0;
                },
            };
        }
    </script>

</x-layouts::app>