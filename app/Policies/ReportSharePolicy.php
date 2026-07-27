<?php

namespace App\Policies;

use App\Models\ReportShare;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReportSharePolicy
{
    use HandlesAuthorization;

    /**
     * Determina se o usuário logado pode aceitar ou rejeitar o compartilhamento.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user (No seu caso, a model Admin)
     * @param  \App\Models\ReportShare  $reportShare
     * @return bool
     */
    public function respond($user, ReportShare $reportShare): bool
    {
        // Verifica se a secretaria do usuário atual é a mesma que a secretaria de destino do compartilhamento.
        // Assume-se que a model do usuário autenticado (ex: Admin) possua o atributo 'secretary_id'.
        return (int) $user->secretary_id === (int) $reportShare->destination_secretary_id;
    }
}