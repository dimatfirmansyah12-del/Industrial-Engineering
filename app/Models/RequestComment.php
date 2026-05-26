<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RequestComment extends Model
{
    protected $fillable = [
        'ie_request_id',
        'user_id',
        'comment',
        'attachment_file',
    ];

    public function ieRequest(): BelongsTo
    {
        return $this->belongsTo(IeRequest::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
