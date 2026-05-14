<?php

namespace App\Models;

use Database\Factories\SecretaryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'category', 'is_active'])]
#[Hidden(['password', 'remember_token'])]
class Secretary extends Authenticatable
{
    /** @use HasFactory<SecretaryFactory> */
    use HasFactory, Notifiable;

    protected $table = 'secretaries';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Relacionamento com denúncias atribuídas a esta secretária.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function assignedReports()
    {
        return $this->hasMany(Report::class, 'assigned_secretary_id');
    }

    /**
     * Escopo para filtrar por categoria.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $category
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Identificador do tipo de usuário.
     *
     * @return string
     */
    public function userType(): string
    {
        return 'Secretário';
    }
}
