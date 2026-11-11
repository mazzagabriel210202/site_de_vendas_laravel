<?php

namespace App\Services;

use App\DTOs\AdminCreateDTO;
use App\DTOs\AdminUpdateDTO;
use App\Models\Admin;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Exception;

class AdminService
{
    /**
     * Retorna todos os administradores cadastrados.
     */
    public function getAll(): Collection
    {
        return Admin::all();
    }

    /**
     * Cria um novo administrador utilizando o AdminCreateDTO.
     */
    public function create(AdminCreateDTO $dto): array
    {
        try {
            DB::beginTransaction();

            $admin = $dto->toModel();
            $saved = $admin->save();

            if ($saved) {
                DB::commit();

                return [
                    'success' => true,
                    'message' => 'Administrador criado com sucesso!',
                    'admin'   => $admin,
                ];
            } else {
                DB::rollBack();

                return [
                    'success' => false,
                    'message' => 'Não foi possível salvar o administrador no banco de dados.',
                ];
            }
        } catch (Exception $e) {
            DB::rollBack();

            return [
                'success' => false,
                'message' => 'Erro interno ao criar administrador: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Busca um administrador específico pelo ID.
     */
    public function findById(int $id): ?Admin
    {
        return Admin::find($id);
    }

    /**
     * Atualiza um administrador existente utilizando o AdminUpdateDTO.
     */
    public function update(Admin $admin, AdminUpdateDTO $dto): array
    {
        try {
            DB::beginTransaction();

            $data = $dto->toArray();

            // Validação if-else caso nenhum campo válido tenha sido enviado para alteração
            if (empty($data)) {
                return [
                    'success' => false,
                    'message' => 'Nenhum dado válido foi fornecido para atualização.',
                ];
            }

            $updated = $admin->update($data);

            if ($updated) {
                DB::commit();

                return [
                    'success' => true,
                    'message' => 'Administrador atualizado com sucesso!',
                    'admin'   => $admin->fresh(),
                ];
            } else {
                DB::rollBack();

                return [
                    'success' => false,
                    'message' => 'Não foi possível atualizar os dados do administrador.',
                ];
            }
        } catch (Exception $e) {
            DB::rollBack();

            return [
                'success' => false,
                'message' => 'Erro interno ao atualizar administrador: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Remove um administrador do sistema.
     */
    public function delete(Admin $admin): array
    {
        try {
            DB::beginTransaction();

            $deleted = $admin->delete();

            if ($deleted) {
                DB::commit();

                return [
                    'success' => true,
                    'message' => 'Administrador excluído com sucesso!',
                ];
            } else {
                DB::rollBack();

                return [
                    'success' => false,
                    'message' => 'Não foi possível excluir o administrador.',
                ];
            }
        } catch (Exception $e) {
            DB::rollBack();

            return [
                'success' => false,
                'message' => 'Erro interno ao excluir administrador: ' . $e->getMessage(),
            ];
        }
    }
}