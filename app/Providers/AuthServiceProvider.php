<?php

namespace App\Providers;

use App\Models\User;
use App\Policies\AdminPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

/**
 * Service Provider de autenticação.
 *
 * Registra policies e eventos de autenticação.
 */
class AuthServiceProvider extends ServiceProvider
{
    /**
     * Mapeamento de policies.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        User::class => AdminPolicy::class,
    ];

    /**
     * Registra serviços de autenticação.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap serviços de autenticação.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}
