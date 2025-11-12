<?php

namespace Tests\Feature;

use App\Models\Frase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FraseTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test que verifica que un usuario autenticado puede acceder al dashboard.
     */
    public function test_authenticated_user_can_access_dashboard(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertViewIs('frases.index');
    }

    /**
     * Test que verifica que usuarios no autenticados son redirigidos al login.
     */
    public function test_unauthenticated_user_cannot_access_dashboard(): void
    {
        $response = $this->get(route('dashboard'));

        $response->assertRedirect(route('login'));
    }

    /**
     * Test que verifica que un usuario puede ver el formulario de creación.
     */
    public function test_user_can_view_create_form(): void
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('frases.create'));

        $response->assertStatus(200);
        $response->assertViewIs('frases.create');
        $response->assertSee('Matrix Digital Rain');
        $response->assertSee('Quantum Glitch');
    }

    /**
     * Test que verifica que un usuario puede crear una nueva frase.
     */
    public function test_user_can_create_a_frase(): void
    {
        $user = User::factory()->create();

        $fraseData = [
            'texto' => 'Esta es una frase de prueba',
            'animacion' => 'matrix',
        ];

        $response = $this->actingAs($user)->post(route('frases.store'), $fraseData);

        $response->assertRedirect();
        $this->assertDatabaseHas('frases', [
            'texto' => 'Esta es una frase de prueba',
            'animacion' => 'matrix',
            'user_id' => $user->id,
        ]);
    }

    /**
     * Test que verifica todas las animaciones válidas.
     */
    public function test_user_can_create_frase_with_all_animation_types(): void
    {
        $user = User::factory()->create();
        $animaciones = ['matrix', 'quantum', 'nebula', 'hologram', 'particle'];

        foreach ($animaciones as $animacion) {
            $fraseData = [
                'texto' => "Frase con animación {$animacion}",
                'animacion' => $animacion,
            ];

            $response = $this->actingAs($user)->post(route('frases.store'), $fraseData);

            $response->assertRedirect();
            $this->assertDatabaseHas('frases', [
                'texto' => "Frase con animación {$animacion}",
                'animacion' => $animacion,
                'user_id' => $user->id,
            ]);
        }
    }

    /**
     * Test que verifica que el campo texto es requerido.
     */
    public function test_texto_field_is_required(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('frases.store'), [
            'texto' => '',
            'animacion' => 'matrix',
        ]);

        $response->assertSessionHasErrors('texto');
    }

    /**
     * Test que verifica que el campo animacion es requerido.
     */
    public function test_animacion_field_is_required(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('frases.store'), [
            'texto' => 'Texto de prueba',
            'animacion' => '',
        ]);

        $response->assertSessionHasErrors('animacion');
    }

    /**
     * Test que verifica que el campo texto no puede exceder 255 caracteres.
     */
    public function test_texto_field_cannot_exceed_255_characters(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('frases.store'), [
            'texto' => str_repeat('a', 256),
            'animacion' => 'matrix',
        ]);

        $response->assertSessionHasErrors('texto');
    }

    /**
     * Test que verifica que solo se aceptan animaciones válidas.
     */
    public function test_only_valid_animations_are_accepted(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('frases.store'), [
            'texto' => 'Texto de prueba',
            'animacion' => 'invalid_animation',
        ]);

        $response->assertSessionHasErrors('animacion');
    }

    /**
     * Test que verifica que un usuario puede ver una frase que le pertenece.
     */
    public function test_user_can_view_their_own_frase(): void
    {
        $user = User::factory()->create();
        $frase = Frase::factory()->create([
            'user_id' => $user->id,
            'texto' => 'Mi frase de prueba',
            'animacion' => 'matrix',
        ]);

        $response = $this->actingAs($user)->get(route('frases.show', $frase));

        $response->assertStatus(200);
        $response->assertSee('Mi frase de prueba');
    }

    /**
     * Test que verifica que un usuario no puede ver frases de otros usuarios.
     */
    public function test_user_cannot_view_other_users_frases(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $frase = Frase::factory()->create([
            'user_id' => $otherUser->id,
        ]);

        $response = $this->actingAs($user)->get(route('frases.show', $frase));

        $response->assertStatus(403);
    }

    /**
     * Test que verifica que un usuario puede eliminar su propia frase.
     */
    public function test_user_can_delete_their_own_frase(): void
    {
        $user = User::factory()->create();
        $frase = Frase::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->delete(route('frases.destroy', $frase));

        $response->assertRedirect();
        $this->assertDatabaseMissing('frases', [
            'id' => $frase->id,
        ]);
    }

    /**
     * Test que verifica que un usuario no puede eliminar frases de otros usuarios.
     */
    public function test_user_cannot_delete_other_users_frases(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $frase = Frase::factory()->create([
            'user_id' => $otherUser->id,
        ]);

        $response = $this->actingAs($user)->delete(route('frases.destroy', $frase));

        $response->assertStatus(403);
        $this->assertDatabaseHas('frases', [
            'id' => $frase->id,
        ]);
    }

    /**
     * Test que verifica que el dashboard muestra solo las frases del usuario autenticado.
     */
    public function test_dashboard_shows_only_user_frases(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $userFrase = Frase::factory()->create([
            'user_id' => $user->id,
            'texto' => 'Frase del usuario',
        ]);

        $otherUserFrase = Frase::factory()->create([
            'user_id' => $otherUser->id,
            'texto' => 'Frase de otro usuario',
        ]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Frase del usuario');
        $response->assertDontSee('Frase de otro usuario');
    }

    /**
     * Test que verifica que las frases se muestran ordenadas por las más recientes.
     */
    public function test_frases_are_listed_in_descending_order(): void
    {
        $user = User::factory()->create();

        $frase1 = Frase::factory()->create([
            'user_id' => $user->id,
            'texto' => 'Primera frase',
            'created_at' => now()->subDays(2),
        ]);

        $frase2 = Frase::factory()->create([
            'user_id' => $user->id,
            'texto' => 'Segunda frase',
            'created_at' => now()->subDay(),
        ]);

        $frase3 = Frase::factory()->create([
            'user_id' => $user->id,
            'texto' => 'Tercera frase',
            'created_at' => now(),
        ]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertStatus(200);
        
        // Verificar que las frases aparecen en el orden correcto
        $frases = $response->viewData('frases');
        $this->assertEquals($frase3->id, $frases[0]->id);
        $this->assertEquals($frase2->id, $frases[1]->id);
        $this->assertEquals($frase1->id, $frases[2]->id);
    }

    /**
     * Test que verifica la relación entre Frase y User.
     */
    public function test_frase_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $frase = Frase::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->assertInstanceOf(User::class, $frase->user);
        $this->assertEquals($user->id, $frase->user->id);
    }

    /**
     * Test que verifica que un usuario puede tener múltiples frases.
     */
    public function test_user_can_have_multiple_frases(): void
    {
        $user = User::factory()->create();
        
        Frase::factory()->count(5)->create([
            'user_id' => $user->id,
        ]);

        $this->assertCount(5, $user->frases);
    }
}
