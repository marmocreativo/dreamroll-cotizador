<x-layouts::app :title="__('Usuarios')">

    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-secondary">Usuarios</h1>
            <p class="text-sm text-gray-500">Cuentas con acceso al sistema</p>
        </div>
        <a href="{{ route('usuarios.create') }}"
        class="rounded-lg bg-secondary px-4 py-2 text-sm font-medium text-white transition-colors hover:opacity-90">
            + Nuevo usuario
        </a>
    </div>

    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-left">
                <tr>
                    <th class="px-4 py-3 text-xs font-medium uppercase tracking-wide text-gray-500">Usuario</th>
                    <th class="px-4 py-3 text-xs font-medium uppercase tracking-wide text-gray-500">Correo</th>
                    <th class="px-4 py-3 text-xs font-medium uppercase tracking-wide text-gray-500">Registro</th>
                    <th class="px-4 py-3 text-xs font-medium uppercase tracking-wide text-gray-500"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($usuarios as $usuario)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <div class="flex size-8 items-center justify-center rounded-full bg-secondary text-xs font-semibold text-white">
                                    {{ $usuario->initials() }}
                                </div>
                                <span class="font-medium text-gray-900">{{ $usuario->name }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-gray-600">{{ $usuario->email }}</td>
                        <td class="px-4 py-3 text-xs text-gray-400">
                            {{ $usuario->created_at->format('d/m/Y') }}
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('usuarios.edit', $usuario) }}"
                                class="rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-medium text-gray-600 hover:bg-gray-50 transition-colors">
                                    Editar
                                </a>
                                <form method="POST" action="{{ route('usuarios.destroy', $usuario) }}"
                                    onsubmit="return confirm('¿Eliminar este usuario?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="rounded-lg px-3 py-1.5 text-xs font-medium text-red-600 hover:bg-red-50 transition-colors">
                                        Eliminar
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-12 text-center text-gray-400">
                            No hay usuarios registrados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $usuarios->links() }}
    </div>

</x-layouts::app>