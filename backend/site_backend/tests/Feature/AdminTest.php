<?php

namespace Tests\Feature;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_an_authenticated_admin_can_list_all_admins()
    {
        /** @var Admin $admin */
        $admin = Admin::factory()->create();

        Admin::factory()->count(3)->create();

        $response = $this->actingAs($admin, 'sanctum')
                         ->getJson('/api/admins');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'data' => [
                         '*' => ['id', 'nome', 'email', 'cpf', 'created_at', 'updated_at']
                     ]
                 ])
                 ->assertJsonCount(4, 'data'); // 1 autenticado + 3 criados
    }

    public function test_an_authenticated_admin_can_create_a_new_admin()
    {
        /** @var Admin $admin */
        $admin = Admin::factory()->create();

        $payload = [
            'nome'  => 'Novo Administrador',
            'email' => 'novo@admin.com',
            'senha' => 'password123',
            'cpf'   => '12345678901',
        ];

        $response = $this->actingAs($admin, 'sanctum')
                         ->postJson('/api/admins', $payload);

        $response->assertStatus(201)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Administrador criado com sucesso!',
                 ])
                 ->assertJsonStructure([
                     'success',
                     'message',
                     'data' => ['id', 'nome', 'email', 'cpf']
                 ]);

        $this->assertDatabaseHas('admins', [
            'email' => 'novo@admin.com',
            'cpf'   => '12345678901',
        ]);
    }

    public function test_an_authenticated_admin_can_show_a_specific_admin()
    {
        /** @var Admin $admin */
        $admin = Admin::factory()->create();

        $targetAdmin = Admin::factory()->create([
            'nome'  => 'Admin Alvo',
            'email' => 'alvo@admin.com',
        ]);

        $response = $this->actingAs($admin, 'sanctum')
                         ->getJson("/api/admins/{$targetAdmin->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'id'    => $targetAdmin->id,
                         'nome'  => 'Admin Alvo',
                         'email' => 'alvo@admin.com',
                     ]
                 ]);
    }

    public function test_an_authenticated_admin_can_update_an_admin()
    {
        /** @var Admin $admin */
        $admin = Admin::factory()->create();

        $targetAdmin = Admin::factory()->create([
            'nome'  => 'Nome Antigo',
            'email' => 'antigo@admin.com',
        ]);

        $payload = [
            'nome'  => 'Nome Atualizado',
            'email' => 'atualizado@admin.com',
        ];

        $response = $this->actingAs($admin, 'sanctum')
                         ->putJson("/api/admins/{$targetAdmin->id}", $payload);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Administrador atualizado com sucesso!',
                     'data' => [
                         'nome'  => 'Nome Atualizado',
                         'email' => 'atualizado@admin.com',
                     ]
                 ]);

        $this->assertDatabaseHas('admins', [
            'id'    => $targetAdmin->id,
            'email' => 'atualizado@admin.com',
        ]);
    }

    public function test_an_authenticated_admin_can_delete_an_admin()
    {
        /** @var Admin $admin */
        $admin = Admin::factory()->create();

        $targetAdmin = Admin::factory()->create();

        $response = $this->actingAs($admin, 'sanctum')
                         ->deleteJson("/api/admins/{$targetAdmin->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Administrador excluído com sucesso!',
                 ]);

        $this->assertDatabaseMissing('admins', [
            'id' => $targetAdmin->id,
        ]);
    }

    public function test_it_fails_to_create_admin_with_duplicated_email()
    {
        /** @var Admin $admin */
        $admin = Admin::factory()->create();

        Admin::factory()->create([
            'email' => 'existente@admin.com',
        ]);

        $payload = [
            'nome'  => 'Outro Admin',
            'email' => 'existente@admin.com', // E-mail já cadastrado
            'senha' => 'password123',
            'cpf'   => '98765432100',
        ];

        $response = $this->actingAs($admin, 'sanctum')
                         ->postJson('/api/admins', $payload);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['email']);
    }

    public function test_unauthenticated_user_cannot_access_admin_crud()
    {
        $response = $this->getJson('/api/admins');

        $response->assertStatus(401);
    }
}