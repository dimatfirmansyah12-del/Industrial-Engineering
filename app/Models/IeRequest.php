<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class IeRequest extends Model
{
    public const BOM_NO_BOM = 'No BOM';
    public const BOM_DRAFT = 'BOM Draft';
    public const BOM_SUBMITTED = 'BOM Submitted';

    public const BOM_STATUSES = [
        self::BOM_NO_BOM,
        self::BOM_DRAFT,
        self::BOM_SUBMITTED,
    ];

    protected $fillable = [
        'user_id',
        'request_number',
        'request_date',
        'requester_name',
        'department',
        'line_area',
        'request_type',
        'request_qty',
        'description',
        'priority',
        'status',
        'target_date',
        'pic_drafter',
        'pic_workshop',
        'notes',
        'memo_file',
        'drawing_file',
        'bom_status',
        'bom_submitted_by',
        'bom_submitted_at',
        'bom_revision_note',
        'memo_status',
        'memo_approved_by',
        'memo_approved_at',
        'memo_rejected_reason',
        'memo_approval_note',
        'drawing_status',
        'drawing_started_at',
        'drawing_finished_at',
        'drawing_revision_note',
        'drawing_note',
        'assigned_drafter',
    ];

    protected $casts = [
        'memo_approved_at' => 'datetime',
        'drawing_started_at' => 'datetime',
        'drawing_finished_at' => 'datetime',
        'bom_submitted_at' => 'datetime',
        'target_date' => 'date',
    ];

    public function getIsDelayAttribute(): bool
    {
        return $this->target_date
            && !in_array($this->status, ['Completed', 'Closed'], true)
            && $this->target_date->lt(Carbon::today());
    }

    public function getDelayDaysAttribute(): int
    {
        if (! $this->is_delay) {
            return 0;
        }

        return (int) $this->target_date->diffInDays(Carbon::today());
    }

    public function getIsDueSoonAttribute(): bool
    {
        if (! $this->target_date || in_array($this->status, ['Completed', 'Closed'], true)) {
            return false;
        }

        $today = Carbon::today();

        return $this->target_date->gte($today)
            && $this->target_date->lte($today->copy()->addDays(3));
    }

    public function getDueSoonDaysAttribute(): ?int
    {
        if (! $this->is_due_soon) {
            return null;
        }

        return (int) Carbon::today()->diffInDays($this->target_date);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function materials(): HasMany
    {
        return $this->hasMany(RequestMaterial::class);
    }

    public function memoApprovalSteps(): HasMany
    {
        return $this->hasMany(MemoApprovalStep::class)->orderBy('sequence');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(RequestActivity::class)->latest();
    }

    public function comments(): HasMany
    {
        return $this->hasMany(RequestComment::class)->latest();
    }

    public function purchaseRequest(): HasOne
    {
        return $this->hasOne(PurchaseRequest::class);
    }

    public function sapApproval(): HasOne
    {
        return $this->hasOne(SapApproval::class);
    }

    public function workshopSchedule(): HasOne
    {
        return $this->hasOne(WorkshopSchedule::class);
    }

    public function finalCheck(): HasOne
    {
        return $this->hasOne(FinalCheck::class);
    }

    public function handover(): HasOne
    {
        return $this->hasOne(Handover::class);
    }
}
