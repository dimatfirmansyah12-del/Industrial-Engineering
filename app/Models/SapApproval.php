<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SapApproval extends Model
{
    public const WAITING_PR_INPUT = 'Waiting PR Input';
    public const WAITING_ATASAN_IE = 'Waiting Atasan IE Approval';
    public const WAITING_DIVISION_HEAD = 'Waiting Division Head Approval';
    public const WAITING_DIRECTOR = 'Waiting Director Approval';
    public const SENT_TO_PURCHASING = 'Sent to Purchasing';
    public const REJECTED = 'Rejected';

    public const WAITING_SAP_INPUT = self::WAITING_PR_INPUT;
    public const WAITING_SECTION_HEAD = self::WAITING_ATASAN_IE;

    public const STATUSES = [
        self::WAITING_PR_INPUT,
        self::WAITING_ATASAN_IE,
        self::WAITING_DIVISION_HEAD,
        self::WAITING_DIRECTOR,
        self::SENT_TO_PURCHASING,
        self::REJECTED,
    ];

    protected $fillable = [
        'ie_request_id',
        'sap_description',
        'sap_number',
        'purchase_value',
        'sap_input_date',
        'sap_file',
        'sap_note',
        'sap_input_by',
        'sap_input_at',
        'approval_status',
        'section_head_status',
        'section_head_by',
        'section_head_at',
        'section_head_note',
        'section_head_rejected_reason',
        'division_head_status',
        'division_head_by',
        'division_head_at',
        'division_head_note',
        'division_head_rejected_reason',
        'director_status',
        'director_by',
        'director_at',
        'director_note',
        'director_rejected_reason',
        'sent_to_purchasing_by',
        'sent_to_purchasing_at',
    ];

    protected $casts = [
        'sap_input_date' => 'date',
        'purchase_value' => 'decimal:2',
        'sap_input_at' => 'datetime',
        'section_head_at' => 'datetime',
        'division_head_at' => 'datetime',
        'director_at' => 'datetime',
        'sent_to_purchasing_at' => 'datetime',
    ];

    public function ieRequest(): BelongsTo
    {
        return $this->belongsTo(IeRequest::class);
    }
}
