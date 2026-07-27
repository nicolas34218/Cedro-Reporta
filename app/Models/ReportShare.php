<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportShare extends Model
{
    use HasFactory;

    /**
     * Os atributos que são preenchíveis em massa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'report_id',
        'source_secretary_id',
        'destination_secretary_id',
        'user_id', // Usuário (Admin) que realizou a ação
        'status', // PENDENTE, ACEITO, REJEITADO
        'justification', // Justificativa obrigatória ao compartilhar
        'response_justification', // Justificativa obrigatória ao rejeitar
    ];

    /**
     * Relacionamento: O compartilhamento pertence a uma Denúncia.
     */
    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }

    /**
     * Relacionamento: A secretaria que originou o compartilhamento.
     */
    public function sourceSecretary(): BelongsTo
    {
        return $this->belongsTo(Secretary::class, 'source_secretary_id');
    }

    /**
     * Relacionamento: A secretaria que é o destino do compartilhamento.
     */
    public function destinationSecretary(): BelongsTo
    {
        return $this->belongsTo(Secretary::class, 'destination_secretary_id');
    }

    /**
     * Relacionamento: O usuário (administrador) responsável pelo compartilhamento.
     */
    public function user(): BelongsTo
    {
        // Utilizando Admin::class pois foi identificado na arquitetura do projeto
        return $this->belongsTo(Admin::class, 'user_id');
    }
}