<x-layouts::app.sidebar :title="$title ?? null">
    <flux:main>

        @if(session('success'))
            <div x-data x-init="$nextTick(() => window.Flux.toast('{{ session('success') }}', { variant: 'success' }))"></div>
        @endif

        @if(session('error'))
            <div x-data x-init="$nextTick(() => window.Flux.toast('{{ session('error') }}', { variant: 'danger' }))"></div>
        @endif

        {{ $slot }}

    </flux:main>
</x-layouts::app.sidebar>