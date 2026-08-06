<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
        <style>
            /* Popover de usuario (dropdown de Flux) en color secondary */
            [data-flux-menu] {
                background-color: #1b2d4f !important;
                border-color: rgba(255,255,255,0.1) !important;
            }
            [data-flux-menu] [data-flux-menu-item] {
                color: rgba(255,255,255,0.85) !important;
            }
            [data-flux-menu] [data-flux-menu-item]:hover {
                background-color: rgba(255,255,255,0.1) !important;
                color: #ffffff !important;
            }
            [data-flux-menu] [data-flux-heading] {
                color: rgba(255,255,255,0.9) !important;
            }
            [data-flux-menu] [data-flux-text] {
                color: rgba(255,255,255,0.5) !important;
            }
            [data-flux-menu] [data-flux-separator] {
                border-color: rgba(255,255,255,0.1) !important;
            }
        </style>
    </head>
    <body class="min-h-screen" style="background-color:#dadada;">
        <flux:sidebar sticky collapsible="mobile" class="border-e-0" style="background-color:#1b2d4f;">
            <flux:sidebar.header>
                <img src="{{ asset('logo_blanco.png') }}" class="h-32 w-auto mx-auto" />
                <flux:sidebar.collapse class="lg:hidden !text-white/70 hover:!text-white" />
            </flux:sidebar.header>

            <flux:sidebar.nav>
                <flux:sidebar.group :heading="__('Principal')" class="grid **:data-[flux-heading]:!text-white/50">
                    <flux:sidebar.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')"
                        class="**:!text-white hover:**:!text-[#ffc000] !text-white hover:!text-[#ffc000] hover:!bg-white/10 data-current:!bg-white/15">
                        {{ __('Dashboard') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="document-plus" :href="route('cotizaciones.create')" :current="request()->routeIs('cotizaciones.create')"
                        class="**:!text-white hover:**:!text-[#ffc000] !text-white hover:!text-[#ffc000] hover:!bg-white/10 data-current:!bg-white/15">
                        {{ __('Nueva cotización') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="document-duplicate" :href="route('cotizaciones.index')" :current="request()->routeIs('cotizaciones.*')"
                        class="**:!text-white hover:**:!text-[#ffc000] !text-white hover:!text-[#ffc000] hover:!bg-white/10 data-current:!bg-white/15">
                        {{ __('Cotizaciones') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>

                <flux:sidebar.group :heading="__('Catálogos')" class="grid **:data-[flux-heading]:!text-white/50">
                    <flux:sidebar.item icon="cube" :href="route('productos.index')" :current="request()->routeIs('productos.*')"
                        class="**:!text-white hover:**:!text-[#ffc000] !text-white hover:!text-[#ffc000] hover:!bg-white/10 data-current:!bg-white/15">
                        {{ __('Productos') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="photo" :href="route('eventos.index')" :current="request()->routeIs('eventos.*')"
                        class="**:!text-white hover:**:!text-[#ffc000] !text-white hover:!text-[#ffc000] hover:!bg-white/10 data-current:!bg-white/15">
                        {{ __('Eventos') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>

                <flux:sidebar.group :heading="__('Sistema')" class="grid **:data-[flux-heading]:!text-white/50">
                    <flux:sidebar.item icon="users" :href="route('usuarios.index')" :current="request()->routeIs('usuarios.*')"
                        class="**:!text-white hover:**:!text-[#ffc000] !text-white hover:!text-[#ffc000] hover:!bg-white/10 data-current:!bg-white/15">
                        {{ __('Usuarios') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>
            </flux:sidebar.nav>

            <flux:spacer />

            <x-desktop-user-menu class="hidden lg:block" :name="auth()->user()->name" />
        </flux:sidebar>

        <!-- Mobile User Menu -->
        <flux:header class="lg:hidden" style="background-color:#1b2d4f; border-color:rgba(255,255,255,0.1);">
            <flux:sidebar.toggle class="lg:hidden !text-white/70 hover:!text-white" icon="bars-2" inset="left" />

            <flux:spacer />

            <flux:dropdown position="top" align="end">
                <flux:profile
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevron-down"
                    class="!text-white"
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