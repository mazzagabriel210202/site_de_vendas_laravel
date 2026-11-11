<?php

namespace App\Services;

use App\DTOs\AdminRegisterDTO;
use App\DTOs\AdminLoginDTO;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Exception;

class AdminAuthService
{
    /**
     * Realiza o registro de um novo administrador com validação if-else.
     */
   public function register(AdminRegisterDTO $dto): array
    {
        try {
            DB::beginTransaction();

            $admin = $dto->toModel();
            $saved = $admin->save();

            if ($saved) {
                $token = $admin->createToken('auth_token')->plainTextToken;

                DB::commit();

                return [
                    'success' => true,
                    'message' => 'Administrador cadastrado com sucesso!',
                    'admin'   => $admin,
                    'token'   => $token,
                ];
            } else {
                DB::rollBack();

                return [
                    'success' => false,
                    'message' => 'Não foi possível salvar o administrador no banco de dados.',
                ];
            }
        } catch (\Exception $e) {
            DB::rollBack();

            // Retorna a mensagem real da exceção (ex: violação de unique, campo nulo, etc)
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Realiza a autenticação de um administrador com validação if-else.
     */
    public function login(AdminLoginDTO $dto): array
    {
        $admin = Admin::where('email', $dto->email)->first();

        // Validação if-else para verificar se o admin existe e a senha confere
        if ($admin && Hash::check($dto->senha, $admin->senha)) {
            $token = $admin->createToken('auth_token')->plainTextToken;

            return [
                'success' => true,
                'message' => 'Login realizado com sucesso!',
                'admin'   => $admin,
                'token'   => $token,
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Credenciais inválidas.',
            ];
        }
    }

    /**
     * Encerra a sessão revogando os tokens ativos do administrador.
     */
    public function logout(Admin $admin): bool
    {
        if ($admin && method_exists($admin, 'tokens')) {
            $admin->tokens()->delete();
            return true;
        }
        
        return false;
    }
}