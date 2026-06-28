<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Registra uma atualização realizada em uma denúncia (criação, mudança de
 * status, prioridade, transferência ou compartilhamento entre secretarias),
 * permitindo que o cidadão e as secretarias responsáveis acompanhem o
 * histórico completo de eventos.
 *
 * @property int $id
 * @property int $report_id
 * @property string $action
 * @property string|null $description
 * @property string $actor_name
 * @property string $actor_role
 * @property \Carbon\Carbon $created_at
 */
class ReportHistory extends Model
{
    protected $fillable = [
        'report_id',
        'action',
        'description',
        'actor_name',
        'actor_role',
    ];

    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }

    /**
     * Registra uma nova entrada no histórico da denúncia, identificando
     * automaticamente o usuário autenticado responsável pela ação.
     */
    public static function log(Report $report, string $action, ?string $description = null): self
    {
        [$actorName, $actorRole] = self::resolveActor();

        return self::create([
            'report_id' => $report->id,
            'action' => $action,
            'description' => $description,
            'actor_name' => $actorName,
            'actor_role' => $actorRole,
        ]);
    }

    /**
     * Identifica o usuário autenticado (em qualquer um dos guards) que
     * está realizando a ação, para registrar "quem" gerou a atualização.
     *
     * @return array{0: string, 1: string}
     */
    private static function resolveActor(): array
    {
        if ($secretary = auth('secretary')->user()) {
            return [$secretary->name, 'Secretaria'];
        }

        if ($admin = auth('admin')->user()) {
            return [$admin->name, 'Administrador'];
        }

        if ($citizen = auth('citizen')->user()) {
            return [$citizen->name, 'Cidadão'];
        }

        return ['Visitante', 'Sistema'];
    }
}
