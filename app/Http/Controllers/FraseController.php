<?php

namespace App\Http\Controllers;

use App\Models\Frase;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;

// Controlador que gestiona las operaciones CRUD (parciales) para las "frases" (animaciones)
// Está pensado para usarse por usuarios autenticados. Contiene las acciones principales:
// - index: lista las frases del usuario
// - create: muestra el formulario de creación
// - store: valida y guarda una nueva frase
// - show: muestra la animación de una frase
// - destroy: elimina una frase
#[\Illuminate\Auth\Middleware\Authenticate]
/**
 * @method User user()
 */
class FraseController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Map de tipos de animación -> texto descriptivo.
     * Se usa para poblar selects y mostrar nombres legibles en la UI.
     *
     * @var array<string, string>
     */

    // Array asociativo que mapea los tipos de animación con sus descripciones legibles
    private array $tiposAnimacion = [
        'matrix' => '🟢 Matrix Digital Rain - Efecto Matriz',
        'quantum' => '⚛️ Quantum Glitch - Distorsión Cuántica',
        'nebula' => '🌌 Cosmic Nebula - Explosión Cósmica',
        'hologram' => '🔷 Holographic Scan - Holograma Futurista',
        'particle' => '✨ Particle Explosion - Explosión de Partículas',
    ];

    // Mostrar listada de frases del usuario autenticado
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();

        // Recuperar frases del usuario en orden descendente y pasarlas a la vista
        return view('frases.index', [
            'frases' => $user->frases()->latest()->get(),
            'tiposAnimacion' => $this->tiposAnimacion,
        ]);
    }

    // Mostrar formulario de creación
    public function create()
    {
        return view('frases.create', [
            'tiposAnimacion' => $this->tiposAnimacion,
        ]);
    }

    // Validar y guardar una nueva frase
    public function store(Request $request)
    {
        // Validaciones básicas: texto obligatorio y tipo de animación permitido
        $validated = $request->validate([
            'texto' => 'required|max:255',
            'animacion' => 'required|in:matrix,quantum,nebula,hologram,particle',
        ]);

        /** @var User $user */
        $user = Auth::user();
        // Crear la frase asociada al usuario
        $frase = $user->frases()->create($validated);

        // Redirigir a la vista de la frase recién creada
        return redirect()
            ->route('frases.show', $frase)
            ->with('success', 'Frase creada exitosamente.');
    }

    // Mostrar una frase concreta
    public function show(Frase $frase)
    {
        $this->authorize('view', $frase);

        return view('frases.show', [
            'frase' => $frase,
            'puedeCrearNueva' => true,
        ]);
    }

    // Eliminar una frase
    public function destroy(Frase $frase)
    {
        $this->authorize('delete', $frase);
        $frase->delete();

        return redirect()
            ->route('frases.index')
            ->with('success', 'Frase eliminada exitosamente.');
    }
}
