<?php

namespace App\Http\Controllers;

use App\Models\IeRequest;
use App\Models\RequestActivity;
use App\Services\RequestWorkflow;

abstract class Controller
{
    protected function syncRequestStatus(?IeRequest $ieRequest, string $status, string $module, ?string $note = null): void
    {
        if (! $ieRequest || $ieRequest->status === $status || ! RequestWorkflow::isValidStatus($status)) {
            return;
        }

        if ($ieRequest->status === RequestWorkflow::CLOSED && $status !== RequestWorkflow::CLOSED) {
            return;
        }

        $oldStatus = $ieRequest->status;

        $ieRequest->update([
            'status' => $status,
        ]);

        RequestActivity::record(
            $ieRequest->id,
            $module,
            'Request Status Synced',
            $oldStatus,
            $status,
            $note
        );
    }
}
