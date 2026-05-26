<?php

namespace App\Services;

use App\Models\IeRequest;
use App\Models\RequestActivity;
use App\Models\SapApproval;

class RequestWorkflow
{
    public const REQUEST_SUBMITTED = 'Request Submitted';
    public const MEMO_APPROVED = 'Memo Approved';
    public const DRAWING_ON_PROGRESS = 'Drawing On Progress';
    public const DRAWING_DONE = 'Drawing Done';
    public const BOM_DRAFT = 'BOM Draft';
    public const WAITING_SAP_INPUT = 'Waiting PR Input';
    public const WAITING_SECTION_HEAD_APPROVAL = 'Waiting Atasan IE Approval';
    public const WAITING_DIVISION_HEAD_APPROVAL = 'Waiting Division Head Approval';
    public const WAITING_DIRECTOR_APPROVAL = 'Waiting Director Approval';
    public const SENT_TO_PURCHASING = 'Sent to Purchasing';
    public const SAP_APPROVAL_REJECTED = 'PR Approval Rejected';
    public const WAITING_MATERIAL = 'Waiting Material';
    public const MATERIAL_COMPLETE = 'Material Complete';
    public const WORKSHOP_SCHEDULED = 'Workshop Scheduled';
    public const WORKSHOP_ON_PROGRESS = 'Workshop On Progress';
    public const FINAL_CHECK = 'Final Check';
    public const WAITING_HANDOVER = 'Waiting Handover';
    public const COMPLETED = 'Completed';
    public const CLOSED = 'Closed';

    public const STATUSES = [
        self::REQUEST_SUBMITTED,
        self::MEMO_APPROVED,
        self::DRAWING_ON_PROGRESS,
        self::DRAWING_DONE,
        self::BOM_DRAFT,
        self::WAITING_SAP_INPUT,
        self::WAITING_SECTION_HEAD_APPROVAL,
        self::WAITING_DIVISION_HEAD_APPROVAL,
        self::WAITING_DIRECTOR_APPROVAL,
        self::SENT_TO_PURCHASING,
        self::SAP_APPROVAL_REJECTED,
        self::WAITING_MATERIAL,
        self::MATERIAL_COMPLETE,
        self::WORKSHOP_SCHEDULED,
        self::WORKSHOP_ON_PROGRESS,
        self::FINAL_CHECK,
        self::WAITING_HANDOVER,
        self::COMPLETED,
        self::CLOSED,
    ];

    public static function isValidStatus(string $status): bool
    {
        return in_array($status, self::STATUSES, true);
    }

    public function deriveStatus(IeRequest $ieRequest): string
    {
        $ieRequest->loadMissing([
            'materials',
            'sapApproval',
            'purchaseRequest',
            'workshopSchedule',
            'finalCheck',
            'handover',
        ]);

        if ($this->handoverReceived($ieRequest)) {
            return self::CLOSED;
        }

        if ($this->finalCheckPassedOk($ieRequest)) {
            return self::WAITING_HANDOVER;
        }

        if ($ieRequest->finalCheck || $ieRequest->workshopSchedule?->progress_status === 'Done') {
            return self::FINAL_CHECK;
        }

        if ($ieRequest->workshopSchedule) {
            if (in_array($ieRequest->workshopSchedule->progress_status, ['On Progress', 'Hold', 'Rework'], true)) {
                return self::WORKSHOP_ON_PROGRESS;
            }

            if ($ieRequest->workshopSchedule->schedule_status !== 'Cancelled') {
                return self::WORKSHOP_SCHEDULED;
            }
        }

        if ($this->materialsComplete($ieRequest)) {
            return self::MATERIAL_COMPLETE;
        }

        if ($ieRequest->purchaseRequest && $ieRequest->purchaseRequest->pr_status === 'PO Created') {
            return self::WAITING_MATERIAL;
        }

        if ($ieRequest->purchaseRequest && $ieRequest->purchaseRequest->pr_status !== 'Rejected') {
            return self::SENT_TO_PURCHASING;
        }

        if (
            $ieRequest->drawing_status === 'Done'
            && $ieRequest->materials->isNotEmpty()
            && $ieRequest->bom_status !== IeRequest::BOM_SUBMITTED
        ) {
            return self::BOM_DRAFT;
        }

        if ($ieRequest->sapApproval) {
            return match ($ieRequest->sapApproval->approval_status) {
                SapApproval::WAITING_SECTION_HEAD => self::WAITING_SECTION_HEAD_APPROVAL,
                SapApproval::WAITING_DIVISION_HEAD => self::WAITING_DIVISION_HEAD_APPROVAL,
                SapApproval::WAITING_DIRECTOR => self::WAITING_DIRECTOR_APPROVAL,
                SapApproval::SENT_TO_PURCHASING => self::SENT_TO_PURCHASING,
                SapApproval::REJECTED => self::SAP_APPROVAL_REJECTED,
                default => self::WAITING_SAP_INPUT,
            };
        }

        if (
            $ieRequest->drawing_status === 'Done'
            && $ieRequest->materials->isNotEmpty()
            && $ieRequest->bom_status === IeRequest::BOM_SUBMITTED
        ) {
            return self::WAITING_SAP_INPUT;
        }

        if ($ieRequest->drawing_status === 'Done') {
            return self::DRAWING_DONE;
        }

        if (in_array($ieRequest->drawing_status, ['On Progress', 'Revision'], true)) {
            return self::DRAWING_ON_PROGRESS;
        }

        if ($ieRequest->memo_status === 'Approved') {
            return self::MEMO_APPROVED;
        }

        return self::REQUEST_SUBMITTED;
    }

    public function syncDerivedStatus(IeRequest $ieRequest, string $module = 'Workflow Sync', ?string $note = null): void
    {
        $targetStatus = $this->deriveStatus($ieRequest);

        if ($ieRequest->status === $targetStatus) {
            return;
        }

        $oldStatus = $ieRequest->status;

        $ieRequest->update([
            'status' => $targetStatus,
        ]);

        RequestActivity::record(
            $ieRequest->id,
            $module,
            'Request Status Resynced',
            $oldStatus,
            $targetStatus,
            $note
        );
    }

    public function canManuallySetStatus(IeRequest $ieRequest, string $status): array
    {
        if (! self::isValidStatus($status)) {
            return [false, 'Status request tidak valid.'];
        }

        $ieRequest->loadMissing([
            'materials',
            'sapApproval',
            'purchaseRequest',
            'workshopSchedule',
            'finalCheck',
            'handover',
        ]);

        $derivedStatus = $this->deriveStatus($ieRequest);

        if ($status !== $derivedStatus) {
            return [false, 'Status utama harus mengikuti data proses. Status yang sesuai saat ini: ' . $derivedStatus . '.'];
        }

        return [true, null];
    }

    private function materialsComplete(IeRequest $ieRequest): bool
    {
        return $ieRequest->materials->isNotEmpty()
            && $ieRequest->materials->every(fn ($material) => $material->arrival_status === 'Complete');
    }

    private function finalCheckPassedOk(IeRequest $ieRequest): bool
    {
        return $ieRequest->finalCheck
            && $ieRequest->finalCheck->check_status === 'Passed'
            && $ieRequest->finalCheck->result_status === 'OK';
    }

    private function handoverReceived(IeRequest $ieRequest): bool
    {
        return $ieRequest->handover
            && $ieRequest->handover->handover_status === 'Received';
    }
}
