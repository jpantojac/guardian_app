<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * AuthApiTest — Pruebas de integración para los endpoints de autenticación.
 *
 * Entregable E3 — GuardianApp MVP v1.0
 * Autor: Julián Pantoja Clavijo — UNAD 2026
 *
 * Endpoints cubiertos:
 *   POST /api/register
 *   POST /api/login
 *   POST /api/logout
 *   GET  /api/user
 */
class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    // ─────────────────────────────────────────────
    //  POST /api/register
    // ─────────────────────────────────────────────

    /** @test */
    public function usuario_puede_registrarse_con_datos_validos(): void
    {
        $response = $this->postJson('/api/register', [
            'name'                  => 'Carlos Martínez',
            'email'                 => 'carlos@example.com',
            'password'              => 'SecurePass123!',
            'password_confirmation' => 'SecurePass123!',
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'access_token',
                     'token_type',
                     'user' => ['id', 'name', 'email', 'role'],
                 ])
                 ->assertJson(['token_type' => 'Bearer']);

        $this->assertDatabaseHas('users', [
            'email' => 'carlos@example.com',
            'role'  => 'user',
        ]);
    }

    /** @test */
    public function registro_falla_con_email_duplicado(): void
    {
        User::factory()->create(['email' => 'carlos@example.com']);

        $response = $this->postJson('/api/register', [
            'name'                  => 'Carlos Nuevo',
            'email'                 => 'carlos@example.com',
            'password'              => 'SecurePass123!',
            'password_confirmation' => 'SecurePass123!',
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['email']);
    }

    /** @test */
    public function registro_falla_si_passwords_no_coinciden(): void
    {
        $response = $this->postJson('/api/register', [
            'name'                  => 'Ana López',
            'email'                 => 'ana@example.com',
            'password'              => 'SecurePass123!',
            'password_confirmation' => 'OtroPassword!',
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['password']);
    }

    /** @test */
    public function registro_falla_con_campos_requeridos_vacios(): void
    {
        $response = $this->postJson('/api/register', []);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    /** @test */
    public function registro_falla_con_password_menor_a_8_caracteres(): void
    {
        $response = $this->postJson('/api/register', [
            'name'                  => 'Luis',
            'email'                 => 'luis@example.com',
            'password'              => '1234',
            'password_confirmation' => '1234',
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['password']);
    }

    // ─────────────────────────────────────────────
    //  POST /api/login
    // ─────────────────────────────────────────────

    /** @test */
    public function usuario_puede_iniciar_sesion_con_credenciales_correctas(): void
    {
        $user = User::factory()->create([
            'email'    => 'maria@example.com',
            'password' => bcrypt('MiPassword123!'),
        ]);

        $response = $this->postJson('/api/login', [
            'email'    => 'maria@example.com',
            'password' => 'MiPassword123!',
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure(['access_token', 'token_type', 'user'])
                 ->assertJson(['token_type' => 'Bearer']);
    }

    /** @test */
    public function login_falla_con_password_incorrecto(): void
    {
        User::factory()->create([
            'email'    => 'maria@example.com',
            'password' => bcrypt('MiPassword123!'),
        ]);

        $response = $this->postJson('/api/login', [
            'email'    => 'maria@example.com',
            'password' => 'PasswordIncorrecto!',
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['email']);
    }

    /** @test */
    public function login_falla_con_email_inexistente(): void
    {
        $response = $this->postJson('/api/login', [
            'email'    => 'noexiste@example.com',
            'password' => 'CualquierPass123!',
        ]);

        $response->assertStatus(422);
    }

    /** @test */
    public function login_falla_si_email_no_es_valido(): void
    {
        $response = $this->postJson('/api/login', [
            'email'    => 'esto-no-es-un-email',
            'password' => 'CualquierPass123!',
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['email']);
    }

    // ─────────────────────────────────────────────
    //  POST /api/logout  |  GET /api/user
    // ─────────────────────────────────────────────

    /** @test */
    public function usuario_puede_cerrar_sesion(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('LogoutPass123!'),
        ]);

        // Iniciar sesion para obtener token real en BD
        $loginResponse = $this->postJson('/api/login', [
            'email'    => $user->email,
            'password' => 'LogoutPass123!',
        ]);
        $token = $loginResponse->json('access_token');

        // Cerrar sesion con el token real
        $response = $this->withHeaders([
                             'Authorization' => 'Bearer ' . $token,
                         ])->postJson('/api/logout');

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Sesión cerrada correctamente']);
    }

    /** @test */
    public function logout_requiere_autenticacion(): void
    {
        $response = $this->postJson('/api/logout');

        $response->assertStatus(401);
    }

    /** @test */
    public function usuario_autenticado_puede_ver_su_perfil(): void
    {
        $user = User::factory()->create(['email' => 'perfil@example.com']);

        $response = $this->actingAs($user, 'sanctum')
                         ->getJson('/api/user');

        $response->assertStatus(200)
                 ->assertJsonPath('email', 'perfil@example.com');
    }

    /** @test */
    public function perfil_requiere_autenticacion(): void
    {
        $response = $this->getJson('/api/user');

        $response->assertStatus(401);
    }
}
