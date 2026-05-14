<?php

namespace App\Providers;

use App\Models\Admin;
use App\Models\Report;
use App\Policies\AdminPolicy;
use App\Policies\ReportPolicy;
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
        Admin::class => AdminPolicy::class,
        Report::class => ReportPolicy::class,
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
