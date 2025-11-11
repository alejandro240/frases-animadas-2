<x-layouts.auth>
    <div class="flex flex-col gap-6">
        <!-- Header -->
        <div class="mb-2 text-center">
            <h1 class="text-3xl font-bold text-white mb-2" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
                Iniciar Sesión
            </h1>
            <p class="text-gray-400 text-sm">Ingresa tus credenciales para acceder a tu cuenta</p>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <!-- Formulario -->
        <div class="bg-zinc-800/50 border border-zinc-700 rounded-xl p-6">
            <form method="POST" action="{{ route('login.store') }}" class="space-y-5">
                @csrf

                <!-- Email Address -->
                <div>
                    <label for="email" class="block text-white font-semibold mb-2">
                        Correo Electrónico
                    </label>
                    <input type="email" name="email" id="email" required autofocus autocomplete="email"
                           value="{{ old('email') }}"
                           class="w-full bg-zinc-900 border border-zinc-600 rounded-lg px-4 py-3 text-white placeholder-gray-500 focus:border-purple-500 focus:ring-2 focus:ring-purple-500/50 focus:outline-none transition-all"
                           placeholder="correo@ejemplo.com">
                    @error('email')
                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label for="password" class="block text-white font-semibold">
                            Contraseña
                        </label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-sm text-purple-400 hover:text-purple-300 transition-colors" wire:navigate>
                                ¿Olvidaste tu contraseña?
                            </a>
                        @endif
                    </div>
                    <input type="password" name="password" id="password" required autocomplete="current-password"
                           class="w-full bg-zinc-900 border border-zinc-600 rounded-lg px-4 py-3 text-white placeholder-gray-500 focus:border-purple-500 focus:ring-2 focus:ring-purple-500/50 focus:outline-none transition-all"
                           placeholder="••••••••">
                    @error('password')
                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="flex items-center">
                    <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}
                           class="w-4 h-4 text-purple-600 bg-zinc-700 border-zinc-500 rounded focus:ring-purple-500 focus:ring-2">
                    <label for="remember" class="ml-2 text-sm text-gray-300">
                        Recordarme
                    </label>
                </div>

                <div class="pt-2">
                    <button type="submit" data-test="login-button"
                            class="w-full bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-blue-700 text-white font-bold py-3 px-6 rounded-lg transition-all transform hover:scale-105 shadow-lg">
                        🔐 Iniciar Sesión
                    </button>
                </div>
            </form>
        </div>

        @if (Route::has('register'))
            <div class="space-x-1 text-sm text-center rtl:space-x-reverse text-zinc-400">
                <span>¿No tienes una cuenta?</span>
                <a href="{{ route('register') }}" class="text-purple-400 hover:text-purple-300 font-semibold transition-colors" wire:navigate>
                    Regístrate
                </a>
            </div>
        @endif
    </div>
</x-layouts.auth>
