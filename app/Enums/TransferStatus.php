<?php

namespace App\Enums;

/**
 * Enum para status de transferências de denúncias entre secretarias.
 */
class TransferStatus
{
    public const PENDING = 'Pendente';
    public const ACCEPTED = 'Aceita';
    public const REJECTED = 'Rejeitada';

    /**
     * Retorna todos os status disponíveis.
     */
    public static function getAll(): array
    {
        return [
            self::PENDING,
            self::ACCEPTED,
            self::REJECTED,
        ];
    }
}
