<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model para categorias de den�ncias.
 * 
 * @property int $id
 * @property string $name Nome da categoria (ex: Ilumina��o P�blica)
 * @property string|null $description Descri��o da categoria
 * @property bool $is_active Se a categoria est� ativa
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
        'secretary_id',
    ];

    /**
     * Convers�o de tipos de campos.
     * 
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relacionamento: Uma categoria tem muitas den�ncias.
     * Busca reports onde o campo 'category' (string) corresponde ao nome da categoria.
     * 
     * @return HasMany
     */
    public function reports(): HasMany
    {
        return $this->hasMany(Report::class, 'category', 'name');
    }

    /**
     * Relacionamento: Uma categoria pertence a uma única secretaria responsável.
     *
     * @return BelongsTo
     */
    public function secretary(): BelongsTo
    {
        return $this->belongsTo(Secretary::class, 'secretary_id');
    }

}
