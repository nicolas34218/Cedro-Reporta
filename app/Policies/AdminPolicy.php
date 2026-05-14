<?php

namespace App\Policies;

use App\Models\Admin;

/**
 * Policy para controle de acesso ao painel administrativo.
 */
class AdminPolicy
{
    /**
     * Determina se o usuário pode acessar o painel administrativo.
     *
     * @param Admin $user
     * @return bool
     */
    public function viewDashboard(Admin $user): bool
    {
        return true;
    }
}
