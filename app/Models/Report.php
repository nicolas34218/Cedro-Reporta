<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
 * @property string|null $location_address
 * @property float|null $latitude
 * @property float|null $longitude
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
        'location_address',
        'latitude',
        'longitude',
        'image_path',
        'secretary_id',
        'priority',
        'priority_justification',
        'priority_assigned_at',
        'is_anonymous',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'priority_assigned_at' => 'datetime',
        'is_anonymous' => 'boolean',
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    /**
     * Relacionamento com o cidadão que fez a denúncia.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function citizen(): BelongsTo
    {
        return $this->belongsTo(Citizen::class, 'user_id');
    }

    /**
     * Relacionamento com a secretária responsável pela denúncia.
     * A denúncia é automaticamente atribuída a uma secretária baseado na categoria.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function secretary(): BelongsTo
    {
        return $this->belongsTo(Secretary::class, 'secretary_id');
    }

    /**
     * Histórico de transferências desta denúncia entre secretarias.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function transfers(): HasMany
    {
        return $this->hasMany(ReportTransfer::class)->latest();
    }

    /**
     * Compartilhamentos desta denúncia entre secretarias.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function shares(): HasMany
    {
        return $this->hasMany(ReportShare::class)->latest();
    }

    /**
     * Histórico completo de atualizações desta denúncia (criação, status,
     * prioridade, transferências e compartilhamentos), do mais recente
     * para o mais antigo.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function histories(): HasMany
    {
        return $this->hasMany(ReportHistory::class)->latest();
    }

    /**
     * Relacionamento: Secretarias que TAMBÉM são responsáveis pela denúncia
     * através de um compartilhamento que foi ACEITO.
     * 
     * Aqui utilizamos a tabela 'report_shares' como uma tabela Pivot (N:N).
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function sharedSecretaries(): BelongsToMany
    {
        return $this->belongsToMany(
            Secretary::class,
            'report_shares',
            'report_id',
            'to_secretary_id'
        )
        ->withPivot(['status', 'from_secretary_id', 'message', 'response', 'shared_at', 'responded_at'])
        ->wherePivot('status', 'accepted')
        ->withTimestamps();
    }

    /**
     * Verifica se a secretaria é responsável por esta denúncia: ou por ser
     * a atual detentora, ou por ela ter sido compartilhada com a secretaria.
     */
    public function isResponsibleSecretary(Secretary $secretary): bool
    {
        return $this->secretary_id === $secretary->id
            || $this->shares()
                    ->where('to_secretary_id', $secretary->id)
                    ->whereIn('status', ['accepted', 'pending'])
                    ->exists();
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