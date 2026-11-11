<?php

namespace Tests\Feature;

use App\Models\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_client_can_list_all_clients()
    {
        /** @var Client $client */
        $client = Client::factory()->create();

        // Cria mais alguns clientes para testar a listagem
        Client::factory()->count(3)->create();

        $response = $this->actingAs($client, 'sanctum')
                         ->getJson('/api/clients');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'data' => [
                         '*' => ['id', 'nome', 'email', 'cpf', 'created_at', 'updated_at']
                     ]
                 ]);
    }

    public function test_authenticated_client_can_create_a_new_client()
    {
        /** @var Client $authClient */
        $authClient = Client::factory()->create();

        $payload = [
            'nome'  => 'Carlos Silva',
            'email' => 'carlos@teste.com',
            'senha' => 'password123',
            'cpf'   => '98765432100',
        ];

        $response = $this->actingAs($authClient, 'sanctum')
                         ->postJson('/api/clients', $payload);

        $response->assertStatus(201)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Cliente cadastrado com sucesso!',
                 ]);

        $this->assertDatabaseHas('clients', [
            'email' => 'carlos@teste.com',
            'cpf'   => '98765432100',
        ]);
    }

    public function test_authenticated_client_can_show_a_specific_client()
    {
        /** @var Client $authClient */
        $authClient = Client::factory()->create();
        
        /** @var Client $targetClient */
        $targetClient = Client::factory()->create([
            'nome' => 'Ana Souza'
        ]);

        $response = $this->actingAs($authClient, 'sanctum')
                         ->getJson("/api/clients/{$targetClient->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'id' => $targetClient->id,
                         'nome' => 'Ana Souza',
                         'email' => $targetClient->email,
                     ]
                 ]);
    }

    public function test_authenticated_client_can_update_a_client()
    {
        /** @var Client $authClient */
        $authClient = Client::factory()->create([
            'nome' => 'Nome Antigo'
        ]);

        $payload = [
            'nome' => 'Nome Atualizado',
            'email' => $authClient->email,
            'cpf' => $authClient->cpf,
            'senha' => 'password123', // Ajuste caso seu FormRequest exija a senha no update
        ];

        // Atualiza o próprio cliente logado para garantir permissão válida
        $response = $this->actingAs($authClient, 'sanctum')
                         ->putJson("/api/clients/{$authClient->id}", $payload);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Cliente atualizado com sucesso!',
                 ]);

        $this->assertDatabaseHas('clients', [
            'id' => $authClient->id,
            'nome' => 'Nome Atualizado',
        ]);
    }

    public function test_authenticated_client_can_delete_a_client()
    {
        /** @var Client $authClient */
        $authClient = Client::factory()->create();
        
        /** @var Client $targetClient */
        $targetClient = Client::factory()->create();

        $response = $this->actingAs($authClient, 'sanctum')
                         ->deleteJson("/api/clients/{$targetClient->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Cliente removido com sucesso!',
                 ]);

        $this->assertDatabaseMissing('clients', [
            'id' => $targetClient->id,
        ]);
    }
}