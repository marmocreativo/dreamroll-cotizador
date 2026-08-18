<x-layouts::app :title="__('Nuevo usuario')">

    <div class="mb-6 flex items-center gap-4">
        <a href="{{ route('usuarios.index') }}"
           class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-semibold text-secondary">Nuevo usuario</h1>
            <p class="text-sm text-gray-500">Registra una nueva cuenta de acceso</p>
        </div>
    </div>

    <form method="POST" action="{{ route('usuarios.store') }}" class="max-w-lg flex flex-col gap-6">
        @csrf

        <div class="rounded-xl border border-gray-200 bg-white p-6 space-y-4"
             x-data="{ preview: null }">

            {{-- Firma --}}
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Imagen de firma</label>
                <div class="flex items-center gap-4">
                    <div class="flex h-16 w-32 items-center justify-center overflow-hidden rounded-lg border border-gray-200 bg-gray-50">
                        <template x-if="preview">
                            <img :src="preview" class="h-full w-full object-contain">
                        </template>
                        <template x-if="!preview">
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-6 text-gray-300" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                            </svg>
                        </template>
                    </div>
                    <div class="flex-1">
                        <input type="file" name="imagen_firma" accept="image/*"
                               @change="preview = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : preview"
                               class="block w-full text-sm text-gray-600 file:mr-3 file:rounded-lg file:border-0 file:bg-gray-100 file:px-3 file:py-2 file:text-xs file:font-medium file:text-gray-700 hover:file:bg-gray-200" />
                        <p class="mt-1 text-xs text-gray-400">Se usará para firmar las cotizaciones en PDF.</p>
                        @error('imagen_firma')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-4">
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Prefijo</label>
                    <select name="prefijo"
                            class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary">
                        @php $prefijoActual = old('prefijo', ''); @endphp
                        <option value="" @selected($prefijoActual === '')>—</option>
                        @foreach (['Sr.', 'Sra.', 'Dr.', 'Dra.', 'Ing.', 'Lic.'] as $prefijo)
                            <option value="{{ $prefijo }}" @selected($prefijoActual === $prefijo)>{{ $prefijo }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="sm:col-span-3">
                    <label class="mb-1 block text-sm font-medium text-gray-700">
                        Nombre <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name') }}"
                           class="w-full rounded-lg border px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary {{ $errors->has('name') ? 'border-red-400' : 'border-gray-200' }}" />
                    @error('name')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Apellidos</label>
                    <input type="text" name="apellidos" value="{{ old('apellidos') }}"
                           class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary" />
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Puesto</label>
                    <input type="text" name="puesto" value="{{ old('puesto') }}"
                           class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary" />
                </div>
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">
                    Correo electrónico <span class="text-red-500">*</span>
                </label>
                <input type="email" name="email" value="{{ old('email') }}"
                       class="w-full rounded-lg border px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary {{ $errors->has('email') ? 'border-red-400' : 'border-gray-200' }}" />
                @error('email')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">
                    Contraseña <span class="text-red-500">*</span>
                </label>
                <input type="password" name="password"
                       class="w-full rounded-lg border px-3 py-2 text-sm text-gray-900 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary {{ $errors->has('password') ? 'border-red-400' : 'border-gray-200' }}" />
                @error('password')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">
                    Confirmar contraseña <span class="text-red-500">*</span>
                </label>
                <input type="password" name="password_confirmation"
                       class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary" />
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('usuarios.index') }}"
               class="rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                Cancelar
            </a>
            <button type="submit"
                    class="rounded-lg bg-secondary px-4 py-2 text-sm font-medium text-white transition-colors hover:opacity-90">
                Crear usuario
            </button>
        </div>
    </form>

</x-layouts::app>