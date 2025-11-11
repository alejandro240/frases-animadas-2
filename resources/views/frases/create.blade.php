<!--
    Vista: Crear nueva animación
    - Muestra un formulario con dos campos: texto y tipo de animación.
    - Se envía por POST a la ruta 'frases.store'.
-->
<x-layouts.app>
    <div class="py-8">
        <div class="max-w-2xl mx-auto">
            <!-- Header -->
            <div class="mb-8 text-center">
                <h1 class="text-4xl font-bold text-white mb-2" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
                    Crear Nueva Animación
                </h1>
                <p class="text-gray-400">Elige un texto y el tipo de animación que prefieras</p>
            </div>

            <!-- Formulario -->
            <div class="bg-zinc-800/50 border border-zinc-700 rounded-xl p-8">
                <form action="{{ route('frases.store') }}" method="POST" class="space-y-6">
                    @csrf
                    
                    <!-- Campo: Texto a animar -->
                    <div>
                        <label for="texto" class="block text-white font-semibold mb-2">
                            Texto a Animar
                        </label>
                        <input type="text" name="texto" id="texto" required 
                               value="{{ old('texto') }}"
                               class="w-full bg-zinc-900 border border-zinc-600 rounded-lg px-4 py-3 text-white placeholder-gray-500 focus:border-purple-500 focus:ring-2 focus:ring-purple-500/50 focus:outline-none transition-all"
                               placeholder="Escribe una letra, palabra o frase...">
                        @error('texto')
                            <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Campo: Tipo de animación -->
                    <div>
                        <label for="animacion" class="block text-white font-semibold mb-3">
                            Tipo de Animación
                        </label>
                        
                        <!-- Grid de opciones con radio buttons estilizados -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($tiposAnimacion as $valor => $nombre)
                            <label class="relative flex items-center gap-3 p-4 bg-zinc-900 border border-zinc-600 rounded-lg cursor-pointer hover:border-purple-500 hover:bg-zinc-800 transition-all group">
                                <input type="radio" name="animacion" value="{{ $valor }}" required
                                       {{ old('animacion') == $valor ? 'checked' : '' }}
                                       class="w-4 h-4 text-purple-600 bg-zinc-700 border-zinc-500 focus:ring-purple-500">
                                <div class="flex-1">
                                    <span class="text-white font-medium group-hover:text-purple-400 transition-colors">{{ $nombre }}</span>
                                </div>
                            </label>
                            @endforeach
                        </div>
                        
                        @error('animacion')
                            <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Botones -->
                    <div class="flex gap-4 pt-4">
                        <button type="submit" 
                                class="flex-1 bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-blue-700 text-white font-bold py-3 px-6 rounded-lg transition-all transform hover:scale-105 shadow-lg">
                            ✨ Crear Animación
                        </button>
                        <a href="{{ route('frases.index') }}" 
                           class="px-6 py-3 bg-zinc-700 hover:bg-zinc-600 text-white font-semibold rounded-lg transition-all">
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>