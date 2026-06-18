<?php

namespace App\Models;

use Database\Factories\SecretaryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'email', 'password', 'is_active', 'admin_id'])]
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
        return $this->hasMany(Report::class, 'secretary_id');
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

    /**
     * Relacionamento: Uma secretária pode ser responsável por várias categorias.
     *
     * @return HasMany
     */
    public function categories(): HasMany
    {
        return $this->hasMany(Category::class, 'secretary_id');
    }

    public function creator()
    {
    return $this->belongsTo(Admin::class, 'admin_id');
    }

    /**
     * Transferências de denúncias recebidas de outras secretarias.
     *
     * @return HasMany
     */
    public function incomingTransfers(): HasMany
    {
        return $this->hasMany(ReportTransfer::class, 'to_secretary_id');
    }

    /**
     * Transferências de denúncias enviadas para outras secretarias.
     *
     * @return HasMany
     */
    public function outgoingTransfers(): HasMany
    {
        return $this->hasMany(ReportTransfer::class, 'from_secretary_id');
    }

    /**
     * Compartilhamentos recebidos por esta secretária.
     *
     * @return HasMany
     */
    public function receivedShares(): HasMany
    {
        return $this->hasMany(ReportShare::class, 'to_secretary_id');
    }

    /**
     * Denúncias compartilhadas com esta secretária.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function sharedReports()
    {
        return $this->hasManyThrough(Report::class, ReportShare::class, 'to_secretary_id', 'id', 'id', 'report_id');
    }

}
