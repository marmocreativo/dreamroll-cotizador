<x-layouts::app :title="__('Fusionar duplicados')">
    <div class="max-w-5xl mx-auto py-8 space-y-6">
        <flux:heading size="xl">Fusionar duplicados</flux:heading>

        <div class="flex gap-2">
            @foreach(['medicos' => 'Médicos', 'instituciones' => 'Instituciones', 'pacientes' => 'Pacientes'] as $key => $label)
                <flux:button
                    href="{{ route('fusion.index', $key) }}"
                    variant="{{ $tipo === $key ? 'primary' : 'ghost' }}"
                    size="sm"
                >
                    {{ $label }}
                </flux:button>
            @endforeach
        </div>

        @if(session('success'))
            <flux:callout variant="success" icon="check-circle">{{ session('success') }}</flux:callout>
        @endif
        @if(session('error'))
            <flux:callout variant="danger" icon="exclamation-triangle">{{ session('error') }}</flux:callout>
        @endif

        @forelse($grupos as $grupo)
            <form action="{{ route('fusion.revisar', $tipo) }}" method="POST"
                  class="border border-zinc-200 dark:border-zinc-700 rounded-xl p-4 space-y-3">
                @csrf
                <div class="flex items-center justify-between">
                    <flux:badge :color="$grupo['motivo'] === 'exacto' ? 'green' : 'yellow'">
                        {{ $grupo['motivo'] === 'exacto' ? 'Coincidencia exacta' : 'Posible duplicado' }}
                    </flux:badge>
                    <flux:button type="submit" size="sm" variant="primary">Revisar fusión</flux:button>
                </div>

                <div class="grid gap-2">
                    @foreach($grupo['items'] as $item)
                        <label class="flex items-center gap-3 p-2 rounded-lg hover:bg-zinc-50 dark:hover:bg-zinc-800 cursor-pointer">
                            <input type="checkbox" name="ids[]" value="{{ $item->id }}" checked class="rounded">
                            <span class="text-sm">
                                @if($tipo === 'medicos')
                                    <strong>{{ $item->nombre_completo }}</strong>
                                    <span class="text-zinc-500">— {{ $item->email ?? 'sin email' }}</span>
                                @elseif($tipo === 'instituciones')
                                    <strong>{{ $item->nombre }}</strong>
                                    <span class="text-zinc-500">— {{ $item->ciudad ?? 'sin ciudad' }}</span>
                                @else
                                    <strong>{{ $item->nombre_display }}</strong>
                                    <span class="text-zinc-500">— {{ $item->folio }}</span>
                                @endif
                                <span class="text-zinc-400">#{{ $item->id }}</span>
                            </span>
                        </label>
                    @endforeach
                </div>
            </form>
        @empty
            <flux:text class="text-zinc-500">No se encontraron posibles duplicados.</flux:text>
        @endforelse
    </div>
</x-layouts::app>