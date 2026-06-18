<?php

namespace App\Models;

use App\Enums\TransferStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Registra o encaminhamento de uma denúncia de uma secretaria para outra,
 * incluindo a justificativa e a decisão (aceite ou rejeição) da secretaria de destino.
 *
 * @property int $id
 * @property int $report_id
 * @property int $from_secretary_id
 * @property int $to_secretary_id
 * @property string $justification
 * @property string $status
 * @property string|null $rejection_reason
 * @property \Carbon\Carbon|null $decided_at
 */
class ReportTransfer extends Model
{
    protected $fillable = [
        'report_id',
        'from_secretary_id',
        'to_secretary_id',
        'justification',
        'status',
        'rejection_reason',
        'decided_at',
    ];

    protected $casts = [
        'decided_at' => 'datetime',
    ];

    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }

    public function fromSecretary(): BelongsTo
    {
        return $this->belongsTo(Secretary::class, 'from_secretary_id');
    }

    public function toSecretary(): BelongsTo
    {
        return $this->belongsTo(Secretary::class, 'to_secretary_id');
    }

    public function isPending(): bool
    {
        return $this->status === TransferStatus::PENDING;
    }

    public function scopePending($query)
    {
        return $query->where('status', TransferStatus::PENDING);
    }
}
