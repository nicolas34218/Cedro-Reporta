<?php

namespace App\Constants;

/**
 * Constantes do sistema de denúncias.
 * 
 * Centraliza categorias, status e outras constantes para evitar duplicação.
 */
class ReportConstants
{
    /**
     * Categorias disponíveis para denúncias.
     */
    public const CATEGORIES = [
        'Iluminação',
        'Buracos',
        'Lixo',
        'Segurança',
        'Outros',
    ];

    /**
     * Retorna todas as categorias.
     *
     * @return array
     */
    public static function getCategories(): array
    {
        return self::CATEGORIES;
    }

    /**
     * Retorna as categorias como string para validação.
     *
     * @return string
     */
    public static function getCategoriesValidation(): string
    {
        return implode(',', self::CATEGORIES);
    }
}
