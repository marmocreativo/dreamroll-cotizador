<x-layouts::app :title="__('Clientes')">

    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-secondary">Clientes</h1>
            <p class="text-sm text-gray-500">Empresas y contactos a quienes facturas</p>
        </div>
        <a href="{{ route('clientes.create') }}"
           class="rounded-lg bg-secondary px-4 py-2 text-sm font-medium text-white transition-colors hover:opacity-90">
            + Nuevo cliente
        </a>
    </div>

    @if (session('success'))
        <div class="mb-4 rounded-lg border border-green-200 bg-green-50 p-4 text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    {{-- Búsqueda --}}
    <form method="GET" class="mb-4">
        <div class="relative max-w-sm">
            <input type="text" name="busqueda" value="{{ request('busqueda') }}"
                   placeholder="Buscar por empresa, contacto o RFC..."
                   class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary" />
        </div>
    </form>

    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-left">
                <tr>
                    <th class="px-4 py-3 text-xs font-medium uppercase tracking-wide text-gray-500">Empresa</th>
                    <th class="px-4 py-3 text-xs font-medium uppercase tracking-wide text-gray-500">RFC</th>
                    <th class="px-4 py-3 text-xs font-medium uppercase tracking-wide text-gray-500">Contacto</th>
                    <th class="px-4 py-3 text-xs font-medium uppercase tracking-wide text-gray-500">Teléfono</th>
                    <th class="px-4 py-3 text-xs font-medium uppercase tracking-wide text-gray-500">Email</th>
                    <th class="px-4 py-3 text-xs font-medium uppercase tracking-wide text-gray-500 w-24">Estado</th>
                    <th class="px-4 py-3 w-24"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($clientes as $cliente)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $cliente->empresa }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $cliente->rfc ?: '—' }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ $cliente->contacto_nombre_completo }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $cliente->contacto_telefono ?: '—' }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $cliente->contacto_email ?: '—' }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium
                                {{ $cliente->activo ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                {{ $cliente->activo ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('clientes.edit', $cliente) }}"
                                   class="text-gray-400 hover:text-secondary transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125" />
                                    </svg>
                                </a>
                                <form action="{{ route('clientes.destroy', $cliente) }}" method="POST"
                                      onsubmit="return confirm('¿Eliminar este cliente?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-gray-400 hover:text-red-600 transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-10 text-center text-sm text-gray-400">
                            No hay clientes registrados todavía.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $clientes->links() }}
    </div>

</x-layouts::app>