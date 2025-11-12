<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $texto
 * @property string $animacion
 * @property int $user_id
 * @property-read User $user
 */
class Frase extends Model
{
    use HasFactory;

    // Modelo que representa una "frase" o animación creada por un usuario.
    // Contiene el texto a animar, el tipo de animación y referencia al usuario propietario.

    protected $fillable = [
        'texto',
        'animacion',
        'user_id',
    ];

    /**
     * Get the user that owns the frase.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<User, Frase>
     */
    public function user(): BelongsTo
    {
        // Relación inversa: la frase pertenece a un único usuario
        return $this->belongsTo(User::class);
    }
}
