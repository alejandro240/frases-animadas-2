<?php

namespace Tests\Feature;

use App\Models\Frase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FraseTest extends TestCase
{
    use RefreshDatabase;

    /** Usuario autenticado puede acceder al dashboard */
    public function test_usuario_accede_dashboard(): void
    {
        $usuario = User::factory()->create();

        $respuesta = $this->actingAs($usuario)->get(route('dashboard'));

        $respuesta->assertStatus(200);
        $respuesta->assertViewIs('frases.index');
    }

    /** Usuario sin autenticar es redirigido al login */
    public function test_invitado_redirige_login(): void
    {
        $respuesta = $this->get(route('dashboard'));

        $respuesta->assertRedirect(route('login'));
    }

    /** Usuario puede ver formulario de creación */
    public function test_usuario_ve_formulario_crear(): void
    {
        /** @var \App\Models\User $usuario */
        $usuario = User::factory()->create();

        $respuesta = $this->actingAs($usuario)->get(route('frases.create'));

        $respuesta->assertStatus(200);
        $respuesta->assertViewIs('frases.create');
        $respuesta->assertSee('Matrix Digital Rain');
        $respuesta->assertSee('Quantum Glitch');
    }

    /** Usuario puede crear una frase */
    public function test_usuario_crea_frase(): void
    {
        $usuario = User::factory()->create();

        $datos = [
            'texto' => 'Esta es una frase de prueba',
            'animacion' => 'matrix',
        ];

        $respuesta = $this->actingAs($usuario)->post(route('frases.store'), $datos);

        $respuesta->assertRedirect();
        $this->assertDatabaseHas('frases', [
            'texto' => 'Esta es una frase de prueba',
            'animacion' => 'matrix',
            'user_id' => $usuario->id,
        ]);
    }

    /** Usuario puede crear frases con todas las animaciones */
    public function test_usuario_crea_todas_animaciones(): void
    {
        $usuario = User::factory()->create();
        $animaciones = ['matrix', 'quantum', 'nebula', 'hologram', 'particle'];

        foreach ($animaciones as $animacion) {
            $datos = [
                'texto' => "Frase con animación {$animacion}",
                'animacion' => $animacion,
            ];

            $respuesta = $this->actingAs($usuario)->post(route('frases.store'), $datos);

            $respuesta->assertRedirect();
            $this->assertDatabaseHas('frases', [
                'texto' => "Frase con animación {$animacion}",
                'animacion' => $animacion,
                'user_id' => $usuario->id,
            ]);
        }
    }

    /** Campo texto es obligatorio */
    public function test_texto_obligatorio(): void
    {
        $usuario = User::factory()->create();

        $respuesta = $this->actingAs($usuario)->post(route('frases.store'), [
            'texto' => '',
            'animacion' => 'matrix',
        ]);

        $respuesta->assertSessionHasErrors('texto');
    }

    /** Campo animacion es obligatorio */
    public function test_animacion_obligatoria(): void
    {
        $usuario = User::factory()->create();

        $respuesta = $this->actingAs($usuario)->post(route('frases.store'), [
            'texto' => 'Texto de prueba',
            'animacion' => '',
        ]);

        $respuesta->assertSessionHasErrors('animacion');
    }

    /** Texto no puede exceder 255 caracteres */
    public function test_texto_maximo_255(): void
    {
        $usuario = User::factory()->create();

        $respuesta = $this->actingAs($usuario)->post(route('frases.store'), [
            'texto' => str_repeat('a', 256),
            'animacion' => 'matrix',
        ]);

        $respuesta->assertSessionHasErrors('texto');
    }

    /** Solo se aceptan animaciones válidas */
    public function test_solo_animaciones_validas(): void
    {
        $usuario = User::factory()->create();

        $respuesta = $this->actingAs($usuario)->post(route('frases.store'), [
            'texto' => 'Texto de prueba',
            'animacion' => 'animacion_invalida',
        ]);

        $respuesta->assertSessionHasErrors('animacion');
    }

    /** Usuario puede ver su propia frase */
    public function test_usuario_ve_su_frase(): void
    {
        $usuario = User::factory()->create();
        $frase = Frase::factory()->create([
            'user_id' => $usuario->id,
            'texto' => 'Mi frase de prueba',
            'animacion' => 'matrix',
        ]);

        $respuesta = $this->actingAs($usuario)->get(route('frases.show', $frase));

        $respuesta->assertStatus(200);
        $respuesta->assertSee('Mi frase de prueba');
    }

    /** Usuario no puede ver frases de otros */
    public function test_usuario_no_ve_frases_ajenas(): void
    {
        $usuario = User::factory()->create();
        $otroUsuario = User::factory()->create();
        $frase = Frase::factory()->create([
            'user_id' => $otroUsuario->id,
        ]);

        $respuesta = $this->actingAs($usuario)->get(route('frases.show', $frase));

        $respuesta->assertStatus(403);
    }

    /** Usuario puede eliminar su propia frase */
    public function test_usuario_elimina_su_frase(): void
    {
        $usuario = User::factory()->create();
        $frase = Frase::factory()->create([
            'user_id' => $usuario->id,
        ]);

        $respuesta = $this->actingAs($usuario)->delete(route('frases.destroy', $frase));

        $respuesta->assertRedirect();
        $this->assertDatabaseMissing('frases', [
            'id' => $frase->id,
        ]);
    }

    /** Usuario no puede eliminar frases ajenas */
    public function test_usuario_no_elimina_frases_ajenas(): void
    {
        $usuario = User::factory()->create();
        $otroUsuario = User::factory()->create();
        $frase = Frase::factory()->create([
            'user_id' => $otroUsuario->id,
        ]);

        $respuesta = $this->actingAs($usuario)->delete(route('frases.destroy', $frase));

        $respuesta->assertStatus(403);
        $this->assertDatabaseHas('frases', [
            'id' => $frase->id,
        ]);
    }

    /** Dashboard muestra solo frases del usuario */
    public function test_dashboard_solo_frases_propias(): void
    {
        $usuario = User::factory()->create();
        $otroUsuario = User::factory()->create();

        $frasePropia = Frase::factory()->create([
            'user_id' => $usuario->id,
            'texto' => 'Frase del usuario',
        ]);

        $fraseAjena = Frase::factory()->create([
            'user_id' => $otroUsuario->id,
            'texto' => 'Frase de otro usuario',
        ]);

        $respuesta = $this->actingAs($usuario)->get(route('dashboard'));

        $respuesta->assertStatus(200);
        $respuesta->assertSee('Frase del usuario');
        $respuesta->assertDontSee('Frase de otro usuario');
    }

    /** Frases se ordenan por las más recientes */
    public function test_frases_ordenadas_recientes(): void
    {
        $usuario = User::factory()->create();

        $frase1 = Frase::factory()->create([
            'user_id' => $usuario->id,
            'texto' => 'Primera frase',
            'created_at' => now()->subDays(2),
        ]);

        $frase2 = Frase::factory()->create([
            'user_id' => $usuario->id,
            'texto' => 'Segunda frase',
            'created_at' => now()->subDay(),
        ]);

        $frase3 = Frase::factory()->create([
            'user_id' => $usuario->id,
            'texto' => 'Tercera frase',
            'created_at' => now(),
        ]);

        $respuesta = $this->actingAs($usuario)->get(route('dashboard'));

        $response->assertStatus(200);

        // Verificar que las frases aparecen en el orden correcto
        $frases = $response->viewData('frases');
        $this->assertEquals($frase3->id, $frases[0]->id);
        $this->assertEquals($frase2->id, $frases[1]->id);
        $this->assertEquals($frase1->id, $frases[2]->id);
    }

    /** Frase pertenece a un usuario */
    public function test_frase_pertenece_usuario(): void
    {
        $usuario = User::factory()->create();
        $frase = Frase::factory()->create([
            'user_id' => $usuario->id,
        ]);

        $this->assertInstanceOf(User::class, $frase->user);
        $this->assertEquals($usuario->id, $frase->user->id);
    }

    /** Usuario puede tener múltiples frases */
    public function test_usuario_multiples_frases(): void
    {
        $user = User::factory()->create();

        Frase::factory()->count(5)->create([
            'user_id' => $usuario->id,
        ]);

        $this->assertCount(5, $usuario->frases);
    }
}
