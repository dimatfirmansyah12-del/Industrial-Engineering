<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Throwable;

class RequestActivity extends Model
{
    protected $fillable = [
        'ie_request_id',
        'user_id',
        'module',
        'action',
        'old_value',
        'new_value',
        'note',
    ];

    public function ieRequest(): BelongsTo
    {
        return $this->belongsTo(IeRequest::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function record(
        int $ieRequestId,
        ?string $module,
        string $action,
        ?string $oldValue = null,
        ?string $newValue = null,
        ?string $note = null
    ): void {
        try {
            self::create([
                'ie_request_id' => $ieRequestId,
                'user_id' => Auth::id(),
                'module' => $module,
                'action' => $action,
                'old_value' => $oldValue,
                'new_value' => $newValue,
                'note' => $note,
            ]);
        } catch (Throwable $exception) {
            report($exception);
        }
    }
}
