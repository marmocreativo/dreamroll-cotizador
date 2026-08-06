<x-layouts::app :title="__('Editar usuario')">

    <div class="mb-6 flex items-center gap-4">
        <a href="{{ route('usuarios.index') }}"
           class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-semibold text-secondary">Editar usuario</h1>
            <p class="text-sm text-gray-500">{{ $usuario->email }}</p>
        </div>
    </div>

    <form method="POST" action="{{ route('usuarios.update', $usuario) }}" class="max-w-lg flex flex-col gap-6">
        @csrf
        @method('PUT')

        <div class="rounded-xl border border-gray-200 bg-white p-6 space-y-4">

            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">
                    Nombre <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" value="{{ old('name', $usuario->name) }}"
                       class="w-full rounded-lg border px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary {{ $errors->has('name') ? 'border-red-400' : 'border-gray-200' }}" />
                @error('name')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">
                    Correo electrónico <span class="text-red-500">*</span>
                </label>
                <input type="email" name="email" value="{{ old('email', $usuario->email) }}"
                       class="w-full rounded-lg border px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary {{ $errors->has('email') ? 'border-red-400' : 'border-gray-200' }}" />
                @error('email')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="border-t border-gray-100 pt-4">
                <p class="mb-3 text-xs text-gray-400">Deja en blanco para mantener la contraseña actual.</p>
                <div class="space-y-4">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Nueva contraseña</label>
                        <input type="password" name="password"
                               class="w-full rounded-lg border px-3 py-2 text-sm text-gray-900 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary {{ $errors->has('password') ? 'border-red-400' : 'border-gray-200' }}" />
                        @error('password')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Confirmar contraseña</label>
                        <input type="password" name="password_confirmation"
                               class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary" />
                    </div>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('usuarios.index') }}"
               class="rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                Cancelar
            </a>
            <button type="submit"
                    class="rounded-lg bg-secondary px-4 py-2 text-sm font-medium text-white transition-colors hover:opacity-90">
                Guardar cambios
            </button>
        </div>
    </form>

</x-layouts::app>