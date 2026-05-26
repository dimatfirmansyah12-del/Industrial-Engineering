<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class RequestMaterial extends Model
{
    protected $fillable = [
        'ie_request_id',
        'material_category',
        'material_name',
        'specification',
        'qty',
        'unit',
        'estimated_price',
        'total_price',
        'material_status',
        'note',
        'arrived_qty',
        'arrival_status',
        'arrival_date',
        'arrival_note',
    ];

    protected $casts = [
        'qty' => 'decimal:2',
        'estimated_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'arrived_qty' => 'decimal:2',
        'arrival_date' => 'date',
    ];

    public function ieRequest(): BelongsTo
    {
        return $this->belongsTo(IeRequest::class);
    }
}
