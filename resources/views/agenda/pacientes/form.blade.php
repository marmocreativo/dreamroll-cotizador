<x-layouts::app :title="__( isset($paciente) ? 'Editar paciente' : 'Nuevo paciente')">

    <div class="mb-6 flex items-center gap-4">
        <a href="{{ route('agenda.pacientes.index') }}"
           class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">
                {{ isset($paciente) ? 'Editar paciente' : 'Nuevo paciente' }}
            </h1>
            <p class="text-sm text-gray-500">
                {{ isset($paciente) ? 'Actualiza los datos del paciente' : 'Registra un nuevo paciente' }}
            </p>
        </div>
    </div>

    <form method="POST"
          action="{{ isset($paciente) ? route('agenda.pacientes.update', $paciente) : route('agenda.pacientes.store') }}"
          class="flex flex-col gap-6 max-w-2xl"
          x-data="{ anonimo: {{ old('anonimo', isset($paciente) && $paciente->anonimo ? '1' : '') ? 'true' : 'false' }} }"
        @csrf
        @isset($paciente)
            @method('PUT')
        @endisset

        <div class="rounded-xl border border-gray-200 bg-white p-6">
            <h2 class="mb-4 text-sm font-semibold uppercase tracking-wide text-gray-700">Datos del paciente</h2>

            <div class="flex flex-col gap-4">

                {{-- Toggle anónimo --}}
                <div class="flex items-start gap-3 rounded-lg border border-gray-200 p-4">
                    <input type="checkbox"
                           name="anonimo"
                           id="anonimo"
                           value="1"
                           x-model="anonimo"
                           {{ old('anonimo', $paciente->anonimo ?? false) ? 'checked' : '' }}
                           class="mt-0.5 rounded border-gray-300 text-blue-600 focus:ring-blue-500" />
                    <div>
                        <label for="anonimo" class="text-sm font-medium text-gray-700 cursor-pointer">
                            Paciente anónimo
                        </label>
                        <p class="text-xs text-gray-400 mt-0.5">Se generará solo el folio, sin datos personales</p>
                    </div>
                </div>

                {{-- Campos personales (ocultos si anónimo) --}}
                <div x-show="!anonimo" class="flex flex-col gap-4">

                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Iniciales</label>
                            <input type="text"
                                   name="iniciales"
                                   value="{{ old('iniciales', $paciente->iniciales ?? '') }}"
                                   maxlength="10"
                                   placeholder="J.G.M."
                                   class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500" />
                        </div>
                        <div class="col-span-2">
                            <label class="mb-1 block text-sm font-medium text-gray-700">Nombre</label>
                            <input type="text"
                                   name="nombre"
                                   value="{{ old('nombre', $paciente->nombre ?? '') }}"
                                   class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500" />
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Apellido paterno</label>
                            <input type="text"
                                   name="apellido_paterno"
                                   value="{{ old('apellido_paterno', $paciente->apellido_paterno ?? '') }}"
                                   class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500" />
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Apellido materno</label>
                            <input type="text"
                                   name="apellido_materno"
                                   value="{{ old('apellido_materno', $paciente->apellido_materno ?? '') }}"
                                   class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500" />
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Fecha de nacimiento</label>
                            <input type="date"
                                   name="fecha_nacimiento"
                                   value="{{ old('fecha_nacimiento', isset($paciente) ? $paciente->fecha_nacimiento?->toDateString() : '') }}"
                                   class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500" />
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Sexo</label>
                            <select name="sexo"
                                    class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                <option value="">—</option>
                                <option value="M" @selected(old('sexo', $paciente->sexo ?? '') === 'M')>Masculino</option>
                                <option value="F" @selected(old('sexo', $paciente->sexo ?? '') === 'F')>Femenino</option>
                                <option value="O" @selected(old('sexo', $paciente->sexo ?? '') === 'O')>Otro</option>
                            </select>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- Contacto (oculto si anónimo) --}}
        <div x-show="!anonimo" class="rounded-xl border border-gray-200 bg-white p-6">
            <h2 class="mb-4 text-sm font-semibold uppercase tracking-wide text-gray-700">Contacto</h2>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">WhatsApp</label>
                    <input type="tel"
                           name="whatsapp"
                           value="{{ old('whatsapp', $paciente->whatsapp ?? '') }}"
                           placeholder="10 dígitos"
                           class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500" />
                    @error('whatsapp')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Correo</label>
                    <input type="email"
                           name="correo"
                           value="{{ old('correo', $paciente->correo ?? '') }}"
                           class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500" />
                    @error('correo')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Notas --}}
        <div class="rounded-xl border border-gray-200 bg-white p-6">
            <h2 class="mb-4 text-sm font-semibold uppercase tracking-wide text-gray-700">Notas</h2>
            <textarea name="notas"
                      rows="3"
                      placeholder="Observaciones adicionales..."
                      class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">{{ old('notas', $paciente->notas ?? '') }}</textarea>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('agenda.pacientes.index') }}"
               class="rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                Cancelar
            </a>
            <button type="submit"
                    class="rounded-lg px-4 py-2 text-sm font-medium text-white hover:opacity-90 transition-colors"
                    style="background-color:#002745;">
                {{ isset($paciente) ? 'Guardar cambios' : 'Registrar paciente' }}
            </button>
        </div>

    </form>

</x-layouts::app>