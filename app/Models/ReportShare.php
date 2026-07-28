<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportShare extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_id',
        'from_secretary_id',
        'to_secretary_id',
        'message',
        'status',
        'response',
        'shared_at',
        'responded_at',
    ];

    protected $casts = [
        'shared_at' => 'datetime',
        'responded_at' => 'datetime',
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
}