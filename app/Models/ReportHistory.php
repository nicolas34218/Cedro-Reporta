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
}