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
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = Frase::class;

    /**
     * Define the model's default state.
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

    /**
     * Indicate that the frase uses matrix animation.
     */
    public function matrix(): static
    {
        return $this->state(fn (array $attributes) => [
            'animacion' => 'matrix',
        ]);
    }

    /**
     * Indicate that the frase uses quantum animation.
     */
    public function quantum(): static
    {
        return $this->state(fn (array $attributes) => [
            'animacion' => 'quantum',
        ]);
    }

    /**
     * Indicate that the frase uses nebula animation.
     */
    public function nebula(): static
    {
        return $this->state(fn (array $attributes) => [
            'animacion' => 'nebula',
        ]);
    }

    /**
     * Indicate that the frase uses hologram animation.
     */
    public function hologram(): static
    {
        return $this->state(fn (array $attributes) => [
            'animacion' => 'hologram',
        ]);
    }

    /**
     * Indicate that the frase uses particle animation.
     */
    public function particle(): static
    {
        return $this->state(fn (array $attributes) => [
            'animacion' => 'particle',
        ]);
    }
}
