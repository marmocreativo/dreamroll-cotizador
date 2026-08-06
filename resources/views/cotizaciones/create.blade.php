<x-layouts::app :title="__('Nueva cotización')">

    <div class="mb-6 flex items-center gap-4">
        <a href="{{ route('cotizaciones.index') }}"
           class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-semibold text-secondary">Nueva cotización</h1>
            <p class="text-sm text-gray-500">Completa los 3 pasos para generar la cotización</p>
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

        <form method="POST" action="{{ route('cotizaciones.store') }}" @submit="return validarPaso(3)">
            @csrf

            {{-- ── PASO 1: Datos del cliente ─────────────────────── --}}
            <div x-show="paso === 1" x-cloak class="rounded-xl border border-gray-200 bg-white p-6 space-y-4">
                <p class="text-sm font-medium text-gray-700">Datos del cliente</p>

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
                            Nombre <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="cliente_nombre" x-model="cliente.nombre"
                               value="{{ old('cliente_nombre') }}"
                               class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary" />
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Apellidos</label>
                        <input type="text" name="cliente_apellidos" x-model="cliente.apellidos"
                               value="{{ old('cliente_apellidos') }}"
                               class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary" />
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Empresa</label>
                        <input type="text" name="cliente_empresa" x-model="cliente.empresa"
                               value="{{ old('cliente_empresa') }}"
                               class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary" />
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Teléfono</label>
                        <input type="text" name="cliente_telefono" x-model="cliente.telefono"
                               value="{{ old('cliente_telefono') }}"
                               placeholder="Incluye lada, ej. 5215512345678"
                               class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary" />
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" name="cliente_email" x-model="cliente.email"
                               value="{{ old('cliente_email') }}"
                               class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary" />
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Dirección</label>
                        <input type="text" name="cliente_direccion" x-model="cliente.direccion"
                               value="{{ old('cliente_direccion') }}"
                               class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary" />
                    </div>
                </div>
            </div>

            {{-- ── PASO 2: Productos ─────────────────────────────── --}}
            <div x-show="paso === 2" x-cloak class="rounded-xl border border-gray-200 bg-white p-6 space-y-4">
                <p class="text-sm font-medium text-gray-700">Productos</p>

                {{-- Buscador con autocompletado --}}
                <div class="relative">
                    <label class="mb-1 block text-sm font-medium text-gray-700">Buscar o agregar producto</label>
                    <input type="text" x-model="busqueda" @input.debounce.300ms="buscarProductos"
                           @focus="mostrarResultados = true"
                           @keydown.escape="mostrarResultados = false"
                           placeholder="Escribe el nombre del producto..."
                           autocomplete="off"
                           class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary" />

                    <div x-show="mostrarResultados && (resultados.length > 0 || busqueda.length > 1)"
                         x-cloak @click.outside="mostrarResultados = false"
                         class="absolute z-10 mt-1 w-full rounded-lg border border-gray-200 bg-white shadow-lg max-h-64 overflow-y-auto">
                        <template x-for="producto in resultados" :key="producto.id">
                            <button type="button" @click="agregarProducto(producto)"
                                    class="flex w-full items-center gap-3 px-3 py-2 text-left hover:bg-gray-50 transition-colors">
                                <img :src="producto.imagen_url" x-show="producto.imagen_url" class="size-8 rounded object-cover border border-gray-200">
                                <div class="flex-1">
                                    <p class="text-sm text-gray-900" x-text="producto.nombre"></p>
                                    <p class="text-xs text-gray-400">$<span x-text="Number(producto.precio_unitario).toFixed(2)"></span></p>
                                </div>
                            </button>
                        </template>
                        <button type="button" x-show="busqueda.length > 1" @click="agregarProductoNuevo()"
                                class="flex w-full items-center gap-2 border-t border-gray-100 px-3 py-2 text-left text-sm text-secondary hover:bg-gray-50 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                            Agregar "<span x-text="busqueda"></span>" como nuevo producto
                        </button>
                    </div>
                </div>

                {{-- Tabla de productos agregados --}}
                <div class="overflow-hidden rounded-lg border border-gray-200">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-left">
                            <tr>
                                <th class="px-3 py-2 text-xs font-medium uppercase tracking-wide text-gray-500">Producto</th>
                                <th class="px-3 py-2 text-xs font-medium uppercase tracking-wide text-gray-500 w-24">Cantidad</th>
                                <th class="px-3 py-2 text-xs font-medium uppercase tracking-wide text-gray-500 w-32">Precio unit.</th>
                                <th class="px-3 py-2 text-xs font-medium uppercase tracking-wide text-gray-500 w-32">Subtotal</th>
                                <th class="px-3 py-2 w-10"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <template x-for="(item, index) in items" :key="item.uid">
                                <tr>
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
                                <td colspan="5" class="px-3 py-8 text-center text-gray-400 text-sm">
                                    Aún no has agregado productos.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- Descuento y totales --}}
                <div class="flex justify-end">
                    <div class="w-full max-w-xs space-y-2 rounded-lg border border-gray-200 p-4">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500">Subtotal</span>
                            <span class="font-medium text-gray-900">$<span x-text="subtotal.toFixed(2)"></span></span>
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
                               value="{{ old('tiempo_entrega') }}"
                               placeholder="Ej. 10 días hábiles"
                               class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary" />
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Válida hasta</label>
                        <input type="date" name="valida_hasta" x-model="entrega.validaHasta"
                               value="{{ old('valida_hasta') }}"
                               class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary" />
                    </div>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Condiciones</label>
                    <textarea name="condiciones" x-model="entrega.condiciones" rows="4"
                              placeholder="Términos de pago, garantías, etc."
                              class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary">{{ old('condiciones') }}</textarea>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Notas internas</label>
                    <textarea name="notas" x-model="entrega.notas" rows="2"
                              placeholder="Notas que no se muestran al cliente"
                              class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary">{{ old('notas') }}</textarea>
                </div>

                {{-- Resumen final --}}
                <div class="rounded-lg bg-gray-50 p-4">
                    <p class="mb-2 text-xs font-medium uppercase tracking-wide text-gray-500">Resumen</p>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-600">Cliente</span>
                        <span class="font-medium text-gray-900" x-text="cliente.nombre + ' ' + cliente.apellidos"></span>
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
                    Crear cotización
                </button>
            </div>
        </form>
    </div>

    <script>
        function cotizacionWizard() {
            return {
                paso: 1,
                pasos: ['Cliente', 'Productos', 'Entrega y condiciones'],
                errores: [],

                cliente: {
                    prefijo: '{{ old('cliente_prefijo') }}',
                    nombre: '{{ old('cliente_nombre') }}',
                    apellidos: '{{ old('cliente_apellidos') }}',
                    empresa: '{{ old('cliente_empresa') }}',
                    telefono: '{{ old('cliente_telefono') }}',
                    email: '{{ old('cliente_email') }}',
                    direccion: '{{ old('cliente_direccion') }}',
                },

                entrega: {
                    tiempo: '{{ old('tiempo_entrega') }}',
                    validaHasta: '{{ old('valida_hasta') }}',
                    condiciones: '{{ old('condiciones') }}',
                    notas: '{{ old('notas') }}',
                },

                busqueda: '',
                resultados: [],
                mostrarResultados: false,
                items: [],
                siguienteUid: 1,

                descuento: 0,
                subtotal: 0,
                iva: 0,
                total: 0,

                async buscarProductos() {
                    if (this.busqueda.length < 2) {
                        this.resultados = [];
                        return;
                    }
                    try {
                        const res = await fetch(`{{ route('productos.buscar') }}?q=${encodeURIComponent(this.busqueda)}`);
                        this.resultados = await res.json();
                    } catch (e) {
                        this.resultados = [];
                    }
                },

                agregarProducto(producto) {
                    this.items.push({
                        uid: this.siguienteUid++,
                        id: producto.id,
                        nombre: producto.nombre,
                        cantidad: 1,
                        precio: parseFloat(producto.precio_unitario),
                    });
                    this.busqueda = '';
                    this.resultados = [];
                    this.mostrarResultados = false;
                    this.calcularTotales();
                },

                agregarProductoNuevo() {
                    this.items.push({
                        uid: this.siguienteUid++,
                        id: null,
                        nombre: this.busqueda,
                        cantidad: 1,
                        precio: 0,
                    });
                    this.busqueda = '';
                    this.resultados = [];
                    this.mostrarResultados = false;
                    this.calcularTotales();
                },

                quitarProducto(index) {
                    this.items.splice(index, 1);
                    this.calcularTotales();
                },

                calcularTotales() {
                    this.subtotal = this.items.reduce((sum, item) => sum + ((item.cantidad || 0) * (item.precio || 0)), 0);
                    const conDescuento = this.subtotal - (this.subtotal * ((this.descuento || 0) / 100));
                    this.iva = conDescuento * 0.16;
                    this.total = conDescuento + this.iva;
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
                        if (!this.cliente.nombre.trim()) {
                            this.errores.push('El nombre del cliente es requerido.');
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