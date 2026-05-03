<?php

namespace App\Enums;

/**
 * Enum para status de denúncias.
 * 
 * Centraliza a configuração de status, evitando duplicação de código
 * e facilitando manutenção futura.
 */
class ReportStatus
{
    public const PENDING = 'Pendente';
    public const ANALYZING = 'Em Análise';
    public const RESOLVED = 'Resolvida';
    public const CLOSED = 'Fechada';

    /**
     * Retorna todos os status disponíveis.
     */
    public static function getAll(): array
    {
        return [
            self::PENDING,
            self::ANALYZING,
            self::RESOLVED,
            self::CLOSED,
        ];
    }

    /**
     * Retorna a configuração visual de cada status (cor, ícone, descrição).
     */
    public static function getConfig(): array
    {
        return [
            self::PENDING => [
                'color' => '#FFC107',
                'icon' => '⏳',
                'description' => 'Sua denúncia foi registrada e aguarda análise.'
            ],
            self::ANALYZING => [
                'color' => '#17A2B8',
                'icon' => '🔍',
                'description' => 'Sua denúncia está sendo analisada pela equipe.'
            ],
            self::RESOLVED => [
                'color' => '#28A745',
                'icon' => '✓',
                'description' => 'Sua denúncia foi resolvida com sucesso!'
            ],
            self::CLOSED => [
                'color' => '#DC3545',
                'icon' => '✕',
                'description' => 'Sua denúncia foi fechada.'
            ],
        ];
    }

    /**
     * Retorna a configuração de um status específico.
     */
    public static function getStatusConfig(string $status): array
    {
        return self::getConfig()[$status] ?? [];
    }

    /**
     * Valida se um status é válido.
     */
    public static function isValid(string $status): bool
    {
        return in_array($status, self::getAll());
    }
}
