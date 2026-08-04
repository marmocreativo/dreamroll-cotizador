<x-layouts::app :title="__('Revisar fusión')">
    <div class="max-w-4xl mx-auto py-8 space-y-6">
        <flux:heading size="xl">Revisar fusión</flux:heading>
        <flux:text class="text-zinc-500">
            Selecciona el registro que se conservará. Los demás se fusionarán en él y se eliminarán permanentemente.
        </flux:text>

        <form action="{{ route('fusion.confirmar', $tipo) }}" method="POST" class="space-y-4">
            @csrf
            @foreach($registros as $registro)
                <input type="hidden" name="ids[]" value="{{ $registro->id }}">
            @endforeach

            <div class="grid gap-3">
                @foreach($registros as $registro)
                    <label class="flex items-start gap-3 p-4 border border-zinc-200 dark:border-zinc-700 rounded-xl cursor-pointer">
                        <input type="radio" name="conservar" value="{{ $registro->id }}"
                               {{ $loop->first ? 'checked' : '' }} class="mt-1">
                        <div class="flex-1">
                            <div class="font-medium">
                                @if($tipo === 'medicos')
                                    {{ $registro->nombre_completo }}
                                @elseif($tipo === 'instituciones')
                                    {{ $registro->nombre }}
                                @else
                                    {{ $registro->nombre_display }}
                                @endif
                                <span class="text-zinc-400 text-sm">#{{ $registro->id }}</span>
                            </div>

                            <div class="text-sm text-zinc-500 mt-1 flex gap-4">
                                @foreach($impacto[$registro->id] as $label => $conteo)
                                    <span>{{ ucfirst($label) }}: <strong>{{ $conteo }}</strong></span>
                                @endforeach
                            </div>
                        </div>
                    </label>
                @endforeach
            </div>

            <flux:callout variant="warning" icon="exclamation-triangle">
                Al confirmar, las cotizaciones y/o citas de los registros no seleccionados se reasignarán al registro
                elegido, y los registros duplicados se eliminarán permanentemente. Esta acción no se puede deshacer.
            </flux:callout>

            <div class="flex gap-3">
                <flux:button href="{{ route('fusion.index', $tipo) }}" variant="ghost">Cancelar</flux:button>
                <flux:button type="submit" variant="danger">Confirmar fusión</flux:button>
            </div>
        </form>
    </div>
</x-layouts:app>