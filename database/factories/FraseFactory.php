<?php

namespace Database\Factories;

use App\Models\Frase;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Frase>
 */
class FraseFactory extends Factory
{
    protected $model = Frase::class;

    /**
     * Estado por defecto del modelo.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $animaciones = ['matrix', 'quantum', 'nebula', 'hologram', 'particle'];

        return [
            'texto' => $this->faker->sentence(6),
            'animacion' => $this->faker->randomElement($animaciones),
            'user_id' => User::factory(),
        ];
    }

    /** Frase con animación matrix */
    public function matrix(): static
    {
        return $this->state(fn (array $attributes) => [
            'animacion' => 'matrix',
        ]);
    }

    /** Frase con animación quantum */
    public function quantum(): static
    {
        return $this->state(fn (array $attributes) => [
            'animacion' => 'quantum',
        ]);
    }

    /** Frase con animación nebula */
    public function nebula(): static
    {
        return $this->state(fn (array $attributes) => [
            'animacion' => 'nebula',
        ]);
    }

    /** Frase con animación hologram */
    public function hologram(): static
    {
        return $this->state(fn (array $attributes) => [
            'animacion' => 'hologram',
        ]);
    }

    /** Frase con animación particle */
    public function particle(): static
    {
        return $this->state(fn (array $attributes) => [
            'animacion' => 'particle',
        ]);
    }
}
