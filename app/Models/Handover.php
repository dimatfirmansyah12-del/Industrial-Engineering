<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Handover extends Model
{
    protected $fillable = [
        'ie_request_id',
        'final_check_id',
        'handover_number',
        'handover_date',
        'handed_over_by',
        'received_by',
        'receiver_department',
        'handover_status',
        'handover_note',
        'receiver_note',
        'evidence_file',
    ];

    protected $casts = [
        'handover_date' => 'date',
    ];

    public function ieRequest(): BelongsTo
    {
        return $this->belongsTo(IeRequest::class);
    }

    public function finalCheck(): BelongsTo
    {
        return $this->belongsTo(FinalCheck::class);
    }
}
