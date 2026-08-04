<x-layouts::app :title="__($hospital ?? false ? 'Editar institución' : 'Nueva institución')">

    {{-- Encabezado --}}
    <div class="mb-6 flex items-center gap-4">
        <a href="{{ route('instituciones.index') }}"
           class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">
                {{ isset($hospital) ? 'Editar institución' : 'Nueva institución' }}
            </h1>
            <p class="text-sm text-gray-500">
                {{ isset($hospital) ? 'Actualiza los datos de la institución' : 'Registra una nueva institución' }}
            </p>
        </div>
    </div>

    <form method="POST"
          action="{{ isset($hospital) ? route('instituciones.update', $hospital) : route('instituciones.store') }}"
          class="flex flex-col gap-6 max-w-2xl">
        @csrf
        @if(isset($hospital))
            @method('PUT')
        @endif

        {{-- Datos generales --}}
        <div class="rounded-xl border border-gray-200 bg-white p-6">
            <h2 class="mb-4 text-sm font-semibold uppercase tracking-wide text-gray-700">Datos generales</h2>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div class="sm:col-span-2">
                    <label class="mb-1 block text-sm font-medium text-gray-700">
                        Nombre <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           name="nombre"
                           value="{{ old('nombre', $hospital->nombre ?? '') }}"
                           placeholder="Nombre completo de la institución"
                           class="w-full rounded-lg border px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 {{ $errors->has('nombre') ? 'border-red-400' : 'border-gray-200' }}" />
                    @error('nombre')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Nombre corto</label>
                    <input type="text"
                           name="nombre_corto"
                           value="{{ old('nombre_corto', $hospital->nombre_corto ?? '') }}"
                           placeholder="Ej. INCAN"
                           class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500" />
                </div>
            </div>

            <div class="mt-4">
                <label class="mb-1 block text-sm font-medium text-gray-700">Procedencia</label>
                <select name="procedencia"
                        class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    <option value="">Sin especificar</option>
                    <option value="PRIVADO" @selected(old('procedencia', $hospital->procedencia ?? '') === 'PRIVADO')>Privado</option>
                    <option value="IMSS"    @selected(old('procedencia', $hospital->procedencia ?? '') === 'IMSS')>IMSS</option>
                    <option value="ISSSTE"  @selected(old('procedencia', $hospital->procedencia ?? '') === 'ISSSTE')>ISSSTE</option>
                    <option value="SSA"     @selected(old('procedencia', $hospital->procedencia ?? '') === 'SSA')>SSA</option>
                </select>
            </div>
        </div>

        {{-- Ubicación --}}
        <div class="rounded-xl border border-gray-200 bg-white p-6">
            <h2 class="mb-4 text-sm font-semibold uppercase tracking-wide text-gray-700">Ubicación</h2>

            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Dirección</label>
                <input type="text"
                       name="direccion"
                       value="{{ old('direccion', $hospital->direccion ?? '') }}"
                       placeholder="Calle, número, colonia"
                       class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500" />
            </div>

            <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Ciudad</label>
                    <input type="text"
                           name="ciudad"
                           value="{{ old('ciudad', $hospital->ciudad ?? '') }}"
                           placeholder="Ciudad"
                           class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500" />
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Estado</label>
                    <input type="text"
                           name="estado"
                           value="{{ old('estado', $hospital->estado ?? '') }}"
                           placeholder="Estado"
                           class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500" />
                </div>
            </div>
        </div>

        {{-- Contacto --}}
        <div class="rounded-xl border border-gray-200 bg-white p-6">
            <h2 class="mb-4 text-sm font-semibold uppercase tracking-wide text-gray-700">Contacto</h2>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Correo electrónico</label>
                    <input type="email"
                           name="email"
                           value="{{ old('email', $hospital->email ?? '') }}"
                           placeholder="contacto@institución.com"
                           class="w-full rounded-lg border px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 {{ $errors->has('email') ? 'border-red-400' : 'border-gray-200' }}" />
                    @error('email')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Teléfono</label>
                    <input type="text"
                           name="telefono"
                           value="{{ old('telefono', $hospital->telefono ?? '') }}"
                           placeholder="55 0000 0000"
                           class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500" />
                </div>
            </div>

            <div class="mt-4">
                <label class="mb-1 block text-sm font-medium text-gray-700">Notas</label>
                <textarea name="notas"
                          rows="3"
                          placeholder="Observaciones adicionales..."
                          class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">{{ old('notas', $hospital->notas ?? '') }}</textarea>
            </div>
        </div>

        {{-- Acciones --}}
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('instituciones.index') }}"
               class="rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                Cancelar
            </a>
            <button type="submit"
                    class="rounded-lg px-4 py-2 text-sm font-medium text-white hover:opacity-90 transition-colors"
                    style="background-color:#002745;">
                {{ isset($hospital) ? 'Guardar cambios' : 'Registrar institución' }}
            </button>
        </div>

    </form>
</x-layouts::app>