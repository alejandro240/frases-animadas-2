<x-layouts.auth>
    <div class="flex flex-col gap-6">
        <!-- Header -->
        <div class="mb-2 text-center">
            <h1 class="text-3xl font-bold text-white mb-2" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
                Crear Cuenta
            </h1>
            <p class="text-gray-400 text-sm">Ingresa tus datos para registrarte</p>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <!-- Formulario -->
        <div class="bg-zinc-800/50 border border-zinc-700 rounded-xl p-6">
            <form method="POST" action="{{ route('register.store') }}" class="space-y-5">
                @csrf

                <!-- Name -->
                <div>
                    <label for="name" class="block text-white font-semibold mb-2">
                        Nombre Completo
                    </label>
                    <input type="text" name="name" id="name" required autofocus autocomplete="name"
                           value="{{ old('name') }}"
                           class="w-full bg-zinc-900 border border-zinc-600 rounded-lg px-4 py-3 text-white placeholder-gray-500 focus:border-purple-500 focus:ring-2 focus:ring-purple-500/50 focus:outline-none transition-all"
                           placeholder="Juan Pérez">
                    @error('name')
                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email Address -->
                <div>
                    <label for="email" class="block text-white font-semibold mb-2">
                        Correo Electrónico
                    </label>
                    <input type="email" name="email" id="email" required autocomplete="email"
                           value="{{ old('email') }}"
                           class="w-full bg-zinc-900 border border-zinc-600 rounded-lg px-4 py-3 text-white placeholder-gray-500 focus:border-purple-500 focus:ring-2 focus:ring-purple-500/50 focus:outline-none transition-all"
                           placeholder="correo@ejemplo.com">
                    @error('email')
                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-white font-semibold mb-2">
                        Contraseña
                    </label>
                    <input type="password" name="password" id="password" required autocomplete="new-password"
                           class="w-full bg-zinc-900 border border-zinc-600 rounded-lg px-4 py-3 text-white placeholder-gray-500 focus:border-purple-500 focus:ring-2 focus:ring-purple-500/50 focus:outline-none transition-all"
                           placeholder="••••••••">
                    @error('password')
                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation" class="block text-white font-semibold mb-2">
                        Confirmar Contraseña
                    </label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required autocomplete="new-password"
                           class="w-full bg-zinc-900 border border-zinc-600 rounded-lg px-4 py-3 text-white placeholder-gray-500 focus:border-purple-500 focus:ring-2 focus:ring-purple-500/50 focus:outline-none transition-all"
                           placeholder="••••••••">
                    @error('password_confirmation')
                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="pt-2">
                    <button type="submit" data-test="register-user-button"
                            class="w-full bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-blue-700 text-white font-bold py-3 px-6 rounded-lg transition-all transform hover:scale-105 shadow-lg">
                        ✨ Crear Cuenta
                    </button>
                </div>
            </form>
        </div>

        <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-400">
            <span>¿Ya tienes una cuenta?</span>
            <a href="{{ route('login') }}" class="text-purple-400 hover:text-purple-300 font-semibold transition-colors" wire:navigate>
                Iniciar Sesión
            </a>
        </div>
    </div>
</x-layouts.auth>
