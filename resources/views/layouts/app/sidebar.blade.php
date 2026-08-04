<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <flux:sidebar sticky collapsible="mobile" class="border-e border-gray-200 bg-white dark:border-gray-700 dark:bg-white">
            <flux:sidebar.header>
                <img src="/favicon.svg" class="h-10 w-auto mx-auto" />
                <flux:sidebar.collapse class="lg:hidden" />
            </flux:sidebar.header>

            <flux:sidebar.nav>
                <flux:sidebar.group :heading="__('Principal')" class="grid">
                    <flux:sidebar.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="document-plus" :href="route('cotizaciones.create')" :current="request()->routeIs('cotizaciones.create')">
                        {{ __('Nueva cotización') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="document-duplicate" :href="route('cotizaciones.index')" :current="request()->routeIs('cotizaciones.*')">
                        {{ __('Cotizaciones') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>

                <flux:sidebar.group :heading="__('Catálogos')" class="grid">
                    <flux:sidebar.item icon="user-group" :href="route('medicos.index')" :current="request()->routeIs('medicos.*')">
                        {{ __('Médicos') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="building-office-2" :href="route('instituciones.index')" :current="request()->routeIs('instituciones.*')">
                        {{ __('Instituciones') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="beaker" :href="route('estudios.index')" :current="request()->routeIs('estudios.*')">
                        {{ __('Estudios') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>

                <flux:sidebar.group :heading="__('Agenda')" class="grid">
                    <flux:sidebar.item icon="calendar" :href="route('agenda.calendario')" :current="request()->routeIs('agenda.calendario')">
                        {{ __('Calendario') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="users" :href="route('agenda.pacientes.index')" :current="request()->routeIs('agenda.pacientes.*')">
                        {{ __('Pacientes') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="building-storefront" :href="route('agenda.centros.index')" :current="request()->routeIs('agenda.centros.*')">
                        {{ __('Centros') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>
                
                <flux:sidebar.group :heading="__('Sistema')" class="grid">
                    <flux:sidebar.item icon="users" :href="route('usuarios.index')" :current="request()->routeIs('usuarios.*')">
                        {{ __('Usuarios') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>
                <flux:sidebar.group :heading="__('Herramientas')" class="grid">
                    <flux:sidebar.item icon="user-group" :href="route('fusion.index', 'medicos')" :current="request()->routeIs('fusion.*') && request()->route('tipo') === 'medicos'">
                        {{ __('Fusionar médicos') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="building-office-2" :href="route('fusion.index', 'instituciones')" :current="request()->routeIs('fusion.*') && request()->route('tipo') === 'instituciones'">
                        {{ __('Fusionar instituciones') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="users" :href="route('fusion.index', 'pacientes')" :current="request()->routeIs('fusion.*') && request()->route('tipo') === 'pacientes'">
                        {{ __('Fusionar pacientes') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>
            </flux:sidebar.nav>

            <flux:spacer />

            <x-desktop-user-menu class="hidden lg:block" :name="auth()->user()->name" />
        </flux:sidebar>

        <!-- Mobile User Menu -->
        <flux:header class="lg:hidden">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <flux:spacer />

            <flux:dropdown position="top" align="end">
                <flux:profile
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevron-down"
                />

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <flux:avatar
                                    :name="auth()->user()->name"
                                    :initials="auth()->user()->initials()"
                                />
                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <flux:heading class="truncate">{{ auth()->user()->name }}</flux:heading>
                                    <flux:text class="truncate">{{ auth()->user()->email }}</flux:text>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('profile.edit')" icon="cog">
                            {{ __('Configuración') }}
                        </flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item
                            as="button"
                            type="submit"
                            icon="arrow-right-start-on-rectangle"
                            class="w-full cursor-pointer"
                        >
                            {{ __('Cerrar sesión') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        {{ $slot }}

        <flux:toast />

        @fluxScripts
    </body>
</html>