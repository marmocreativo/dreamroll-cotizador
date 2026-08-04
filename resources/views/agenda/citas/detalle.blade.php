@php
    $colores = [
        'programada' => 'bg-blue-100 text-blue-800',
        'confirmada' => 'bg-green-100 text-green-800',
        'realizada'  => 'bg-gray-200 text-gray-600',
        'cancelada'  => 'bg-red-100 text-red-700',
    ];

    $sexoLabel = '';
    if ($cita->paciente->sexo === 'M') $sexoLabel = 'Masculino';
    elseif ($cita->paciente->sexo === 'F') $sexoLabel = 'Femenino';
    elseif ($cita->paciente->sexo) $sexoLabel = 'Otro';

    $datosPaciente = '';
    if ($cita->paciente->edad) $datosPaciente .= "Edad: {$cita->paciente->edad} años";
    if ($cita->paciente->edad && $sexoLabel) $datosPaciente .= ' | ';
    if ($sexoLabel) $datosPaciente .= "Sexo: {$sexoLabel}";

    $textoWa =
        "Estimado/a {$cita->paciente->nombre_display},\n\n" .
        "Por medio del presente mensaje, GeneticLab le confirma su cita:\n\n" .
        ($datosPaciente ? "Paciente: {$datosPaciente}\n" : "") .
        "Fecha: " . $cita->fecha->translatedFormat('l d \d\e F \d\e Y') . "\n" .
        "Hora: {$cita->hora}\n" .
        "Centro: {$cita->centro->nombre}" .
        ($cita->centro->direccion ? "\n" . $cita->centro->direccion : "") .
        ($cita->notas ? "\nNotas: {$cita->notas}" : "") .
        "\n\nAtentamente,\nGeneticLab";

    $urlWaBase = "https://wa.me/52" . ($cita->paciente->whatsapp ?? '') . "?text=" . rawurlencode($textoWa);
@endphp

<div class="p-6 space-y-4">

    {{-- Header --}}
    <div class="flex items-start justify-between gap-2">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Detalle de cita</p>
            <h2 class="text-lg font-semibold text-gray-900 mt-0.5">
                {{ $cita->paciente->nombre_display }}
            </h2>
        </div>
        <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium {{ $colores[$cita->estado] ?? 'bg-gray-100 text-gray-600' }}">
            {{ ucfirst($cita->estado) }}
        </span>
    </div>

    {{-- Datos --}}
    <div class="grid grid-cols-2 gap-3 text-sm">
        <div>
            <p class="text-xs text-gray-400">Fecha</p>
            <p class="font-medium text-gray-900">{{ $cita->fecha->translatedFormat('l d \d\e F \d\e Y') }}</p>
        </div>
        <div>
            <p class="text-xs text-gray-400">Hora</p>
            <p class="font-medium text-gray-900">{{ $cita->hora }}</p>
        </div>
        <div class="col-span-2">
            <p class="text-xs text-gray-400">Centro</p>
            <p class="font-medium text-gray-900">{{ $cita->centro->nombre }}</p>
            @if($cita->centro->direccion)
                <p class="text-xs text-gray-400 mt-0.5">{{ $cita->centro->direccion }}</p>
            @endif
        </div>
        @if($cita->notas)
            <div class="col-span-2">
                <p class="text-xs text-gray-400">Notas</p>
                <p class="text-gray-700">{{ $cita->notas }}</p>
            </div>
        @endif
    </div>

    {{-- Cambiar estado --}}
    <div>
        <p class="text-xs text-gray-400 mb-1">Cambiar estado</p>
        <form method="POST" action="/agenda/citas/{{ $cita->id }}/estado"
              class="flex gap-2 flex-wrap">
            @csrf
            @method('PATCH')
            <select name="estado"
                    class="flex-1 rounded-lg border border-gray-200 px-3 py-1.5 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                <option value="programada" @selected($cita->estado === 'programada')>Programada</option>
                <option value="confirmada" @selected($cita->estado === 'confirmada')>Confirmada</option>
                <option value="realizada"  @selected($cita->estado === 'realizada')>Realizada</option>
                <option value="cancelada"  @selected($cita->estado === 'cancelada')>Cancelada</option>
            </select>
            <button type="submit"
                    class="rounded-lg px-3 py-1.5 text-sm font-medium text-white hover:opacity-90 transition-colors"
                    style="background-color:#002745;">
                Guardar
            </button>
        </form>
    </div>

    {{-- Acciones --}}
    <div class="space-y-2 pt-2 border-t border-gray-100"
         x-data="{
             modoWa: false,
             modoEmail: false,
             telefono: '{{ $cita->paciente->whatsapp ?? '' }}',
             email: '{{ $cita->paciente->correo ?? '' }}',
             textoWa: document.getElementById('textoWa-{{ $cita->id }}').dataset.texto,
             enviando: false,
             enviado: false,

             urlWa() {
                 return 'https://wa.me/52' + this.telefono + '?text=' + encodeURIComponent(this.textoWa);
             },

             async enviarEmail() {
                 this.enviando = true;
                 const res = await fetch('/agenda/citas/{{ $cita->id }}/enviar', {
                     method: 'POST',
                     headers: {
                         'Content-Type': 'application/json',
                         'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                     },
                     body: JSON.stringify({ email: this.email }),
                 });
                 this.enviando = false;
                 if (res.ok) {
                     this.enviado = true;
                     setTimeout(() => { this.modoEmail = false; this.enviado = false; }, 2000);
                 }
             }
         }">

        {{-- Elemento oculto que lleva el texto de WA como data attribute --}}
        <span id="textoWa-{{ $cita->id }}"
              data-texto="{{ $textoWa }}"
              style="display:none"></span>

        {{-- Botones principales --}}
        <div class="flex items-center justify-between">
            <a href="{{ route('agenda.citas.edit', $cita) }}"
            class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 px-3 py-1.5 text-sm text-gray-600 hover:bg-gray-50 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125" />
                </svg>
                Editar
            </a>
            <div class="flex gap-2">
                <button type="button"
                        @click="modoWa = !modoWa; modoEmail = false"
                        class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 px-3 py-1.5 text-sm text-gray-600 hover:bg-gray-50 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-4 text-green-500" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                        <path d="M12 0C5.373 0 0 5.373 0 12c0 2.125.558 4.12 1.535 5.847L.057 23.882l6.2-1.625A11.945 11.945 0 0 0 12 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818a9.818 9.818 0 0 1-5.006-1.374l-.36-.214-3.68.965.982-3.594-.235-.369A9.818 9.818 0 1 1 12 21.818z"/>
                    </svg>
                    WhatsApp
                </button>

                <button type="button"
                        @click="modoEmail = !modoEmail; modoWa = false"
                        class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 px-3 py-1.5 text-sm text-gray-600 hover:bg-gray-50 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                    </svg>
                    Correo
                </button>
            </div>

            <form method="POST" action="/agenda/citas/{{ $cita->id }}"
                  onsubmit="return confirm('¿Eliminar esta cita?')">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="rounded-lg px-3 py-1.5 text-sm font-medium text-red-600 hover:bg-red-50 transition-colors">
                    Eliminar
                </button>
            </form>
        </div>

        {{-- Panel WhatsApp --}}
        <div x-show="modoWa" x-transition class="rounded-lg border border-gray-200 p-3 space-y-2">
            <p class="text-xs font-medium text-gray-600">Número de WhatsApp</p>
            <div class="flex gap-2">
                <span class="flex items-center rounded-l-lg border border-r-0 border-gray-200 bg-gray-50 px-3 text-sm text-gray-500">+52</span>
                <input type="tel"
                    id="waNumero-{{ $cita->id }}"
                    value="{{ $cita->paciente->whatsapp ?? '' }}"
                    placeholder="10 dígitos"
                    class="flex-1 rounded-r-lg border border-gray-200 px-3 py-1.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500" />
            </div>
            <a id="waLink-{{ $cita->id }}"
            href="{{ $urlWaBase }}"
            target="_blank"
            onclick="
                const num = document.getElementById('waNumero-{{ $cita->id }}').value;
                this.href = 'https://wa.me/52' + num + '?text=' + encodeURIComponent({{ json_encode($textoWa) }});
            "
            class="flex w-full items-center justify-center gap-2 rounded-lg px-3 py-1.5 text-sm font-medium text-white transition-colors"
            style="background-color:#25d366;">
                Abrir en WhatsApp
            </a>
        </div>

        {{-- Panel correo --}}
        <div x-show="modoEmail" x-transition class="rounded-lg border border-gray-200 p-3 space-y-2">
            <p class="text-xs font-medium text-gray-600">Correo electrónico</p>
            <input type="email"
                   x-model="email"
                   placeholder="correo@ejemplo.com"
                   class="w-full rounded-lg border border-gray-200 px-3 py-1.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500" />
            <button type="button"
                    @click="enviarEmail()"
                    :disabled="enviando || enviado"
                    class="flex w-full items-center justify-center gap-2 rounded-lg px-3 py-1.5 text-sm font-medium text-white hover:opacity-90 transition-colors disabled:opacity-60"
                    style="background-color:#002745;">
                <span x-text="enviado ? '✓ Enviado' : (enviando ? 'Enviando...' : 'Enviar correo')"></span>
            </button>
        </div>
    </div>
</div>