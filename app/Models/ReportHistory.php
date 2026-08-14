<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportHistory extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = [
        'report_id',
        'action',
        'actor_name',
        'actor_role',
        'description',
    ];

    /**
     * Relacionamento com a denúncia.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }

    /**
     * Cria um registro de histórico para a denúncia.
     *
     * @param Report $report
     * @param string $action
     * @param string|null $description
     * @return ReportHistory
     */
    public static function log(Report $report, string $action, ?string $description = null): self
    {
        // Define os valores padrão para caso seja um visitante/anônimo
        $actorName = 'Visitante / Anônimo';
        $actorRole = 'Visitante';

        // Verifica se há alguém logado para pegar o nome e a função
        if (auth()->check()) {
            $user = auth()->user();
            $actorName = $user->name ?? 'Cidadão';
            $actorRole = 'Cidadão';
        } elseif (auth('secretary')->check()) {
            $actorName = auth('secretary')->user()->name;
            $actorRole = 'Secretaria';
        } elseif (auth('admin')->check()) {
            $actorName = auth('admin')->user()->name;
            $actorRole = 'Administrador';
        }

        // Salva o histórico no banco de dados
        return self::create([
            'report_id' => $report->id,
            'action' => $action,
            'actor_name' => $actorName,
            'actor_role' => $actorRole,
            'description' => $description,
        ]);
    }
}