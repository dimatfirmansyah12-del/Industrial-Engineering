<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class PurchaseRequest extends Model
{
    protected $fillable = [
        'ie_request_id',
        'pr_number',
        'pr_date',
        'total_budget',
        'pr_status',
        'requested_by',
        'approved_by',
        'approved_at',
        'rejected_reason',
        'note',
    ];

    protected $casts = [
        'pr_date' => 'date',
        'total_budget' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    public function ieRequest(): BelongsTo
    {
        return $this->belongsTo(IeRequest::class);
    }
}
