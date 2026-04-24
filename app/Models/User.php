<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'user_type', 'is_active'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

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
     * Verifica se o usuário é administrador.
     *
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->user_type === 'Admin';
    }

    /**
     * Verifica se o usuário é secretário.
     *
     * @return bool
     */
    public function isSecretary(): bool
    {
        return $this->user_type === 'Secretário';
    }

    /**
     * Verifica se o usuário é cidadão.
     *
     * @return bool
     */
    public function isCitizen(): bool
    {
        return $this->user_type === 'Cidadão';
    }

    /**
     * Verifica se o usuário tem acesso ao painel administrativo.
     *
     * @return bool
     */
    public function canAccessAdminPanel(): bool
    {
        return $this->isAdmin() || $this->isSecretary();
    }

    /**
     * Relacionamento com denúncias.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function reports()
    {
        return $this->hasMany(Report::class);
    }
}
