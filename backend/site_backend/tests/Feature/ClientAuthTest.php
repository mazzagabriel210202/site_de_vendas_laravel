<?php

namespace Tests\Feature;

use App\Models\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ClientAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_client_can_register()
    {
        $payload = [
            'nome' => 'Gabriel Mazza',
            'email' => 'gabriel@teste.com',
            'senha' => 'password123',
            'cpf' => '12345678901',
        ];

        $response = $this->postJson('/api/register', $payload);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'success',
                     'message',
                     'data' => [
                         'client' => ['id', 'nome', 'email', 'cpf'],
                         'token'
                     ]
                 ]);

        $this->assertDatabaseHas('clients', [
            'email' => 'gabriel@teste.com',
            'cpf' => '12345678901',
        ]);
    }

    public function test_a_client_can_login_using_factory()
    {
        /** @var Client $client */
        $client = Client::factory()->create([
            'email' => 'login@teste.com',
            'senha' => Hash::make('password123'),
        ]);

        $payload = [
            'email' => 'login@teste.com',
            'senha' => 'password123',
        ];

        $response = $this->postJson('/api/login', $payload);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'message',
                     'data' => [
                         'client' => ['id', 'nome', 'email'],
                         'token'
                     ]
                 ]);
    }

    public function test_authenticated_client_can_logout()
    {
        /** @var Client $client */
        $client = Client::factory()->create();

        $response = $this->actingAs($client, 'sanctum')
                         ->postJson('/api/logout');

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Logout realizado com sucesso!',
                 ]);
    }

    public function test_it_fails_login_with_invalid_password()
    {
        Client::factory()->create([
            'email' => 'user@teste.com',
            'senha' => Hash::make('correctpassword'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'user@teste.com',
            'senha' => 'wrongpassword',
        ]);

        $response->assertStatus(401)
                 ->assertJson([
                     'success' => false,
                     'message' => 'Credenciais inválidas.',
                 ]);
    }

    public function test_it_fails_login_with_non_existent_email()
    {
        $response = $this->postJson('/api/login', [
            'email' => 'naoexiste@teste.com',
            'senha' => 'password123',
        ]);

        $response->assertStatus(401)
                 ->assertJson([
                     'success' => false,
                     'message' => 'Credenciais inválidas.',
                 ]);
    }

    public function test_it_fails_register_with_duplicated_cpf()
    {
        Client::factory()->create([
            'cpf' => '11122233344',
        ]);

        $payload = [
            'nome' => 'Outro Usuario',
            'email' => 'outro@teste.com',
            'senha' => 'password123',
            'cpf' => '11122233344', // CPF Duplicado
        ];

        $response = $this->postJson('/api/register', $payload);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['cpf']);
    }

    public function test_it_fails_register_with_empty_password()
    {
        $payload = [
            'nome' => 'Usuario Sem Senha',
            'email' => 'semtoken@teste.com',
            'senha' => '', // Senha vazia
            'cpf' => '99988877766',
        ];

        $response = $this->postJson('/api/register', $payload);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['senha']);
    }
}