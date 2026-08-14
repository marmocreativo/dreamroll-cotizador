<x-layouts::app :title="isset($cliente) ? __('Editar cliente') : __('Nuevo cliente')">

    <div class="mb-6 flex items-center gap-4">
        <a href="{{ route('clientes.index') }}"
           class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-semibold text-secondary">
                {{ isset($cliente) ? 'Editar cliente' : 'Nuevo cliente' }}
            </h1>
            <p class="text-sm text-gray-500">Datos fiscales y de contacto</p>
        </div>
    </div>

    <div class="max-w-2xl">
        <form method="POST"
              action="{{ isset($cliente) ? route('clientes.update', $cliente) : route('clientes.store') }}"
              class="space-y-6">
            @csrf
            @if (isset($cliente))
                @method('PUT')
            @endif

            {{-- Datos fiscales --}}
            <div class="rounded-xl border border-gray-200 bg-white p-6 space-y-4">
                <p class="text-sm font-medium text-gray-700">Datos fiscales</p>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label class="mb-1 block text-sm font-medium text-gray-700">
                            Empresa <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="empresa"
                               value="{{ old('empresa', $cliente->empresa ?? '') }}"
                               class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary" />
                        @error('empresa')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">RFC</label>
                        <input type="text" name="rfc"
                               value="{{ old('rfc', $cliente->rfc ?? '') }}"
                               class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary" />
                        @error('rfc')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Régimen fiscal</label>
                        <input type="text" name="regimen_fiscal"
                               value="{{ old('regimen_fiscal', $cliente->regimen_fiscal ?? '') }}"
                               class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary" />
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Uso de CFDI</label>
                        <input type="text" name="uso_cfdi"
                               value="{{ old('uso_cfdi', $cliente->uso_cfdi ?? '') }}"
                               class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary" />
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Dirección fiscal</label>
                        <input type="text" name="direccion_fiscal"
                               value="{{ old('direccion_fiscal', $cliente->direccion_fiscal ?? '') }}"
                               class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary" />
                    </div>
                </div>
            </div>

            {{-- Contacto --}}
            <div class="rounded-xl border border-gray-200 bg-white p-6 space-y-4">
                <p class="text-sm font-medium text-gray-700">Contacto</p>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-4">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Prefijo</label>
                        <select name="contacto_prefijo"
                                class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary">
                            @php $prefijoActual = old('contacto_prefijo', $cliente->contacto_prefijo ?? ''); @endphp
                            <option value="" @selected($prefijoActual === '')>—</option>
                            @foreach (['Sr.', 'Sra.', 'Dr.', 'Dra.', 'Ing.', 'Lic.'] as $prefijo)
                                <option value="{{ $prefijo }}" @selected($prefijoActual === $prefijo)>{{ $prefijo }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="mb-1 block text-sm font-medium text-gray-700">
                            Nombre <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="contacto_nombre"
                               value="{{ old('contacto_nombre', $cliente->contacto_nombre ?? '') }}"
                               class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary" />
                        @error('contacto_nombre')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Apellidos</label>
                        <input type="text" name="contacto_apellidos"
                               value="{{ old('contacto_apellidos', $cliente->contacto_apellidos ?? '') }}"
                               class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary" />
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Teléfono</label>
                        <input type="text" name="contacto_telefono"
                               value="{{ old('contacto_telefono', $cliente->contacto_telefono ?? '') }}"
                               placeholder="Incluye lada, ej. 5215512345678"
                               class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary" />
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" name="contacto_email"
                               value="{{ old('contacto_email', $cliente->contacto_email ?? '') }}"
                               class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary" />
                        @error('contacto_email')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label class="inline-flex items-center gap-2">
                        <input type="hidden" name="activo" value="0">
                        <input type="checkbox" name="activo" value="1"
                               @checked(old('activo', $cliente->activo ?? true))
                               class="rounded border-gray-300 text-secondary focus:ring-primary" />
                        <span class="text-sm text-gray-700">Cliente activo</span>
                    </label>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('clientes.index') }}"
                   class="rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancelar
                </a>
                <button type="submit"
                        class="rounded-lg bg-secondary px-4 py-2 text-sm font-medium text-white transition-colors hover:opacity-90">
                    {{ isset($cliente) ? 'Guardar cambios' : 'Crear cliente' }}
                </button>
            </div>
        </form>
    </div>

</x-layouts::app>