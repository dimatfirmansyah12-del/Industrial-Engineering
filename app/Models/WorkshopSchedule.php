<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;

class WorkshopSchedule extends Model
{
    protected $fillable = [
        'ie_request_id',
        'schedule_number',
        'planned_start_date',
        'planned_finish_date',
        'actual_start_date',
        'actual_finish_date',
        'pic_workshop',
        'estimated_duration',
        'schedule_status',
        'schedule_note',
        'tv_team_members',
        'reschedule_reason',
        'progress_status',
        'progress_percentage',
        'completed_qty',
        'progress_note',
        'problem_note',
        'started_at',
        'finished_at',
    ];

    protected $casts = [
        'planned_start_date' => 'date',
        'planned_finish_date' => 'date',
        'actual_start_date' => 'date',
        'actual_finish_date' => 'date',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'tv_team_members' => 'array',
        'completed_qty' => 'integer',
        'progress_percentage' => 'integer',
    ];

    public function ieRequest(): BelongsTo
    {
        return $this->belongsTo(IeRequest::class);
    }

    public function progressLogs(): HasMany
    {
        return $this->hasMany(WorkshopProgressLog::class);
    }

    public function finalCheck(): HasOne
    {
        return $this->hasOne(FinalCheck::class);
    }
}
