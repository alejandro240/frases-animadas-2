<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-zinc-950 antialiased">
        <!-- Fondo con gradiente -->
        <div class="fixed inset-0 bg-gradient-to-br from-zinc-950 via-zinc-900 to-purple-950/20 -z-10"></div>
        
        <!-- Efectos visuales de fondo -->
        <div class="fixed inset-0 -z-10">
            <div class="absolute top-0 left-1/4 w-96 h-96 bg-purple-600/10 rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-blue-600/10 rounded-full blur-3xl"></div>
        </div>

        <div class="flex min-h-screen flex-col items-center justify-center gap-6 p-6 md:p-10">
            <div class="flex w-full max-w-md flex-col gap-4">
                <div class="flex flex-col gap-4">
                    {{ $slot }}
                </div>
            </div>
        </div>
        @fluxScripts
    </body>
</html>
