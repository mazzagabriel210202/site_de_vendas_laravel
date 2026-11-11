<?php

namespace Tests\Feature;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_an_admin_can_register()
    {
        $payload = [
            'nome'  => 'Administrador Master',
            'email' => 'admin@teste.com',
            'senha' => 'password123',
            'cpf'   => '12345678901',
        ];

        $response = $this->postJson('/api/admin/register', $payload);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'success',
                     'message',
                     'data' => [
                         'admin' => ['id', 'nome', 'email', 'cpf'],
                         'token'
                     ]
                 ]);

        $this->assertDatabaseHas('admins', [
            'email' => 'admin@teste.com',
            'cpf'   => '12345678901',
        ]);
    }

    public function test_an_admin_can_login_using_factory()
    {
        /** @var Admin $admin */
        $admin = Admin::factory()->create([
            'email' => 'adminlogin@teste.com',
            'senha' => Hash::make('password123'),
        ]);

        $payload = [
            'email' => 'adminlogin@teste.com',
            'senha' => 'password123',
        ];

        $response = $this->postJson('/api/admin/login', $payload);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'message',
                     'data' => [
                         'admin' => ['id', 'nome', 'email'],
                         'token'
                     ]
                 ]);
    }

    public function test_authenticated_admin_can_logout()
    {
        /** @var Admin $admin */
        $admin = Admin::factory()->create();

        $response = $this->actingAs($admin, 'sanctum')
                         ->postJson('/api/admin/logout');

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Logout realizado com sucesso!',
                 ]);
    }

    public function test_it_fails_admin_login_with_invalid_password()
    {
        Admin::factory()->create([
            'email' => 'adminuser@teste.com',
            'senha' => Hash::make('correctpassword'),
        ]);

        $response = $this->postJson('/api/admin/login', [
            'email' => 'adminuser@teste.com',
            'senha' => 'wrongpassword',
        ]);

        $response->assertStatus(401)
                 ->assertJson([
                     'success' => false,
                     'message' => 'Credenciais inválidas.',
                 ]);
    }

    public function test_it_fails_admin_login_with_non_existent_email()
    {
        $response = $this->postJson('/api/admin/login', [
            'email' => 'naoexiste@teste.com',
            'senha' => 'password123',
        ]);

        $response->assertStatus(401)
                 ->assertJson([
                     'success' => false,
                     'message' => 'Credenciais inválidas.',
                 ]);
    }

    public function test_it_fails_admin_register_with_duplicated_cpf()
    {
        Admin::factory()->create([
            'cpf' => '99988877766',
        ]);

        $payload = [
            'nome'  => 'Outro Admin',
            'email' => 'outroadmin@teste.com',
            'senha' => 'password123',
            'cpf'   => '99988877766', // CPF duplicado
        ];

        $response = $this->postJson('/api/admin/register', $payload);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['cpf']);
    }

    public function test_it_fails_admin_register_with_empty_password()
    {
        $payload = [
            'nome'  => 'Admin Sem Senha',
            'email' => 'semsenha@teste.com',
            'senha' => '', // Senha vazia
            'cpf'   => '11122233344',
        ];

        $response = $this->postJson('/api/admin/register', $payload);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['senha']);
    }
}