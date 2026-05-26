<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class WorkshopProgressLog extends Model
{
    protected $fillable = [
        'workshop_schedule_id',
        'ie_request_id',
        'user_id',
        'progress_status',
        'progress_percentage',
        'completed_qty',
        'note',
        'problem_note',
        'photo_file',
    ];

    protected $casts = [
        'progress_percentage' => 'integer',
        'completed_qty' => 'integer',
    ];

    public function workshopSchedule(): BelongsTo
    {
        return $this->belongsTo(WorkshopSchedule::class);
    }

    public function ieRequest(): BelongsTo
    {
        return $this->belongsTo(IeRequest::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
