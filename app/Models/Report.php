<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modelo de denúncia/relatório.
 *
 * @property int $id
 * @property int $user_id
 * @property string $title
 * @property string $description
 * @property string $category
 * @property string $status
 * @property string|null $location
 * @property string|null $image_path
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Report extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'category',
        'status',
        'location',
        'image_path',
        'secretary_id',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relacionamento com o cidadão que fez a denúncia.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function citizen()
    {
        return $this->belongsTo(Citizen::class, 'user_id');
    }

    /**
     * Alias para o cidadão (para compatibilidade com código existente).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->citizen();
    }

    /**
     * Relacionamento com a secretária responsável pela denúncia.
     * A denúncia é automaticamente atribuída a uma secretária baseado na categoria.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function secretary()
    {
        return $this->belongsTo(Secretary::class, 'secretary_id');
    }

    /**
     * Alias para compatibilidade (antes era assignedSecretary).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function assignedSecretary()
    {
        return $this->secretary();
    }

    /**
     * Scope para filtrar denúncias por categoria.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string|array $categories
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByCategory($query, $categories)
    {
        if (empty($categories)) {
            return $query;
        }

        // Permite tanto string única quanto array de categorias
        $categories = is_array($categories) ? $categories : [$categories];

        return $query->whereIn('category', $categories);
    }

    /**
     * Scope para filtrar denúncias por localização.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $location
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByLocation($query, $location)
    {
        if (empty($location)) {
            return $query;
        }

        return $query->where('location', 'like', '%' . $location . '%');
    }

    /**
     * Scope para filtrar denúncias por status.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string|array $statuses
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByStatus($query, $statuses)
    {
        if (empty($statuses)) {
            return $query;
        }

        // Permite tanto string única quanto array de status
        $statuses = is_array($statuses) ? $statuses : [$statuses];

        return $query->whereIn('status', $statuses);
    }

    /**
     * Scope para aplicar múltiplos filtros simultaneamente.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $filters
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilter($query, array $filters)
    {
        if (isset($filters['category']) && !empty($filters['category'])) {
            $query->byCategory($filters['category']);
        }

        if (isset($filters['location']) && !empty($filters['location'])) {
            $query->byLocation($filters['location']);
        }

        if (isset($filters['status']) && !empty($filters['status'])) {
            $query->byStatus($filters['status']);
        }

        return $query;
    }
}
