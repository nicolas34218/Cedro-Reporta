<?php

namespace App\Policies;

use App\Models\Report;
use App\Models\Citizen;

/**
 * Policy para controle de acesso a denúncias.
 */
class ReportPolicy
{
    /**
     * Verifica se o usuário é o proprietário da denúncia.
     *
     * @param Citizen $user
     * @param Report $report
     * @return bool
     */
    private function isOwner(Citizen $user, Report $report): bool
    {
        return $user->id === $report->user_id;
    }

    /**
     * Determina se o usuário pode visualizar uma denúncia.
     *
     * Um cidadão só pode visualizar denúncias que sejam suas.
     *
     * @param Citizen $user
     * @param Report $report
     * @return bool
     */
    public function view(User $user, Report $report): bool
    {
        return $this->isOwner($user, $report);
    }

    /**
     * Determina se o usuário pode atualizar uma denúncia.
     *
     * Um usuário só pode atualizar denúncias que sejam suas.
     *
     * @param User $user
     * @param Report $report
     * @return bool
     */
    public function update(User $user, Report $report): bool
    {
        return $this->isOwner($user, $report);
    }

    /**
     * Determina se o usuário pode deletar uma denúncia.
     *
     * Um usuário só pode deletar denúncias que sejam suas.
     *
     * @param User $user
     * @param Report $report
     * @return bool
     */
    public function delete(User $user, Report $report): bool
    {
        return $this->isOwner($user, $report);
    }

    /**
     * Determina se o usuário pode acompanhar uma denúncia.
     *
     * Um usuário só pode acompanhar denúncias que sejam suas.
     *
     * @param User $user
     * @param Report $report
     * @return bool
     */
    public function track(User $user, Report $report): bool
    {
        return $this->isOwner($user, $report);
    }
}
