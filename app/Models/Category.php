<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Model para categorias de denúncias.
 * 
 * @property int $id
 * @property string $name Nome da categoria (ex: Iluminação Pública)
 * @property string|null $description Descrição da categoria
 * @property bool $is_active Se a categoria está ativa
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class Category extends Model
{
    /**
     * Campos que podem ser preenchidos em massa.
     * 
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'description',
        'is_active',
    ];

    /**
     * Conversão de tipos de campos.
     * 
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relacionamento: Uma categoria tem muitas denúncias.
     * 
     * @return HasMany
     */
    public function reports(): HasMany
    {
        return $this->hasMany(Report::class, 'category', 'name');
    }

    /**
     * Relacionamento: Uma categoria pode ser responsabilidade de várias secretárias.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */

    public function secretaries(): BelongsToMany
    {
        return $this->belongsToMany(
            Secretary::class,
            'category_secretary',
            'category_id',
            'secretary_id'
        );
    }
}
