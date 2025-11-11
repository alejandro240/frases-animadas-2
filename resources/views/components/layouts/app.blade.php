<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
    @if(isset($title))
    <title>{{ $title }} - {{ config('app.name') }}</title>
    @else
    <title>{{ config('app.name') }}</title>
    @endif
    <!--
        Layout principal de la aplicación (header/nav y contenedor principal).
        - Aquí se define el nav personalizado (no usamos el nav de Laravel/Flux).
        - También contiene el dropdown de usuario con nombre, email y logout.
        Comentarios:
        - Usamos comentarios Blade {{-- --}} para explicar partes HTML/Blade.
        - Los estilos CSS usan /* ... */ como es habitual.
-->
    <style>
        .user-dropdown {
            position: relative;
            display: inline-block;
        }

        .user-dropdown-menu {
            display: none;
            position: absolute;
            right: 0;
            top: 100%;
            margin-top: 0.5rem;
            background: #27272a;
            border: 1px solid #3f3f46;
            border-radius: 0.5rem;
            min-width: 250px;
            box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.3);
            z-index: 50;
        }

        .user-dropdown:hover .user-dropdown-menu {
            display: block;
        }

        .user-dropdown-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            color: #e4e4e7;
            text-decoration: none;
            transition: background-color 0.2s;
            border: none;
            background: none;
            width: 100%;
            text-align: left;
            cursor: pointer;
            font-size: 14px;
        }

        .user-dropdown-item:hover {
            background-color: #3f3f46;
        }

        .user-dropdown-separator {
            height: 1px;
            background-color: #3f3f46;
            margin: 0.25rem 0;
        }

        .user-profile-button {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.5rem 1rem;
            background: #3f3f46;
            border: 1px solid #52525b;
            border-radius: 0.5rem;
            color: white;
            cursor: pointer;
            transition: all 0.2s;
        }

        .user-profile-button:hover {
            background: #52525b;
        }

        .user-initials {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 2rem;
            height: 2rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 0.375rem;
            font-weight: bold;
            font-size: 0.875rem;
        }

        /* Custom Nav Styles */
        .custom-nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem 2rem;
            background: #18181b;
            border-bottom: 1px solid #3f3f46;
        }

        .nav-left {
            display: flex;
            align-items: center;
            gap: 2rem;
        }

        .nav-logo {
            font-size: 1.5rem;
            font-weight: bold;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-decoration: none;
        }

        .nav-links {
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        .nav-link {
            padding: 0.5rem 1rem;
            color: #a1a1aa;
            text-decoration: none;
            border-radius: 0.375rem;
            transition: all 0.2s;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .nav-link:hover {
            color: #e4e4e7;
            background: #27272a;
        }

        .nav-link.active {
            color: #e4e4e7;
            background: #27272a;
        }
    </style>
</head>

<body class="min-h-screen bg-zinc-900">
    {{-- Custom Navigation: reemplaza el nav de Flux y contiene enlaces principales y cuenta --}}
    <nav class="custom-nav">
        <div class="nav-left">
            <a href="{{ route('frases.index') }}" class="nav-logo">
                Frases Animadas
            </a>

            {{-- Enlaces principales a Mis Animaciones y Crear nueva --}}
            <div class="nav-links">
                <a href="{{ route('frases.index') }}" class="nav-link {{ request()->routeIs('frases.index') ? 'active' : '' }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                        <polyline points="9 22 9 12 15 12 15 22"></polyline>
                    </svg>
                    Mis Animaciones
                </a>

                <a href="{{ route('frases.create') }}" class="nav-link {{ request()->routeIs('frases.create') ? 'active' : '' }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    Nueva Animación
                </a>
            </div>
        </div>

        {{-- Bloque de cuenta: iniciales, nombre y menú desplegable con info y logout --}}
        <div class="user-dropdown">
            <div class="user-profile-button">
                <div class="user-initials">{{ auth()->user()->initials() }}</div>
                <span>{{ Str::limit(auth()->user()->name, 20) }}</span>
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="6 9 12 15 18 9"></polyline>
                </svg>
            </div>

            <div class="user-dropdown-menu">
                {{-- Información de usuario (solo lectura) --}}
                <div class="user-dropdown-item" style="pointer-events: none; opacity: 0.7;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                    <span>{{ auth()->user()->name }}</span>
                </div>

                {{-- Email del usuario (solo lectura) --}}
                <div class="user-dropdown-item" style="pointer-events: none; opacity: 0.7;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="2" y="4" width="20" height="16" rx="2"></rect>
                        <path d="m2 7 10 7 10-7"></path>
                    </svg>
                    <span>{{ auth()->user()->email }}</span>
                </div>

                <div class="user-dropdown-separator"></div>

                {{-- Formulario para cerrar sesión (POST) --}}
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="user-dropdown-item">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                            <polyline points="16 17 21 12 16 7"></polyline>
                            <line x1="21" y1="12" x2="9" y2="12"></line>
                        </svg>
                        <span>Cerrar Sesión</span>
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <main class="container mx-auto px-4">
        {{ $slot }}
    </main>
</body>

</html>