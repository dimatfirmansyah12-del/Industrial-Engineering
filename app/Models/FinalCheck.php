<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class FinalCheck extends Model
{
    protected $fillable = [
        'ie_request_id',
        'workshop_schedule_id',
        'check_date',
        'checked_by',
        'check_status',
        'result_status',
        'problem_note',
        'correction_note',
        'final_note',
        'evidence_file',
    ];

    protected $casts = [
        'check_date' => 'date',
    ];

    public function ieRequest(): BelongsTo
    {
        return $this->belongsTo(IeRequest::class);
    }

    public function workshopSchedule(): BelongsTo
    {
        return $this->belongsTo(WorkshopSchedule::class);
    }

    public function handover(): HasOne
    {
        return $this->hasOne(Handover::class);
    }
}
