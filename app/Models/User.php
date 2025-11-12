<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    // Este modelo representa al usuario autenticado de la aplicación.
    // Contiene relaciones y helpers relacionados con el usuario.

    public function frases(): HasMany
    {
        // Relación 1:N: Un usuario puede tener muchas "frases" (animaciones)
        return $this->hasMany(Frase::class);
    }

    public function initials(): string
    {
        // Devuelve las iniciales del nombre del usuario.
        // Se usa en el header para mostrar un identificador corto del usuario.
        $names = explode(' ', $this->name);

        if (count($names) >= 2) {
            // Tomar primera letra del primer y segundo nombre
            return strtoupper(substr($names[0], 0, 1).substr($names[1], 0, 1));
        }

        // Si solo hay un nombre, tomar las dos primeras letras
        return strtoupper(substr($this->name, 0, 2));
    }

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
}
