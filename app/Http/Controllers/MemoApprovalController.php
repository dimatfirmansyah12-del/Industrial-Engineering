<?php

namespace App\Http\Controllers;

use App\Models\IeRequest;
use App\Models\MemoApprovalStep;
use App\Models\RequestActivity;
use App\Services\RequestWorkflow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MemoApprovalController extends Controller
{
    public function index()
    {
        $query = MemoApprovalStep::query()
            ->with(['ieRequest', 'approver'])
            ->where('status', MemoApprovalStep::WAITING)
            ->whereHas('ieRequest', function ($requestQuery) {
                $requestQuery->whereNotNull('memo_file')
                    ->where('memo_file', '!=', '')
                    ->where('memo_status', 'Waiting Approval');
            });

        if (Auth::user()?->role !== 'admin') {
            $query->where('approver_user_id', Auth::id());
        }

        $approvalSteps = $query
            ->orderBy('created_at')
            ->paginate(10);

        return view('memo-approvals.index', compact('approvalSteps'));
    }

    public function approve(Request $request, MemoApprovalStep $memoApprovalStep)
    {
        $memoApprovalStep->loadMissing('ieRequest', 'approver');
        $ieRequest = $memoApprovalStep->ieRequest;

        $this->authorizeStep($memoApprovalStep);

        if (!$ieRequest->memo_file) {
            return redirect()
                ->route('memo-approvals.index')
                ->with('error', 'Request ini tidak memiliki file memo untuk di-approve.');
        }

        if ($memoApprovalStep->status !== MemoApprovalStep::WAITING) {
            return redirect()
                ->route('memo-approvals.index')
                ->with('error', 'Approval step ini tidak sedang menunggu approval.');
        }

        $validated = $request->validate([
            'approval_note' => 'nullable|string',
        ]);

        $memoApprovalStep->update([
            'status' => MemoApprovalStep::APPROVED,
            'approved_at' => now(),
            'rejected_at' => null,
            'note' => $validated['approval_note'] ?? null,
            'rejected_reason' => null,
        ]);

        $nextStep = $ieRequest->memoApprovalSteps()
            ->where('sequence', '>', $memoApprovalStep->sequence)
            ->where('status', MemoApprovalStep::PENDING)
            ->orderBy('sequence')
            ->first();

        if ($nextStep) {
            $nextStep->update([
                'status' => MemoApprovalStep::WAITING,
            ]);

            $ieRequest->update([
                'memo_approved_by' => Auth::user()->name,
                'memo_approved_at' => now(),
                'memo_rejected_reason' => null,
                'memo_approval_note' => $memoApprovalStep->approval_label . ' approved. Waiting ' . $nextStep->approval_label . '.',
            ]);
        } else {
            $oldStatus = $ieRequest->memo_status;

            $ieRequest->update([
                'memo_status' => 'Approved',
                'memo_approved_by' => Auth::user()->name,
                'memo_approved_at' => now(),
                'memo_rejected_reason' => null,
                'memo_approval_note' => 'All memo approvers approved.',
            ]);

            $this->syncRequestStatus($ieRequest, RequestWorkflow::MEMO_APPROVED, 'Memo Approval', 'All memo approvers approved');

            RequestActivity::record(
                $ieRequest->id,
                'Memo Approval',
                'Memo Fully Approved',
                $oldStatus,
                'Approved'
            );
        }

        RequestActivity::record(
            $ieRequest->id,
            'Memo Approval',
            'Memo Step Approved',
            MemoApprovalStep::WAITING,
            MemoApprovalStep::APPROVED,
            $memoApprovalStep->approval_label . ' - ' . Auth::user()->name
        );

        return redirect()
            ->route('memo-approvals.index')
            ->with('success', 'Memo approval step berhasil di-approve.');
    }

    public function reject(Request $request, MemoApprovalStep $memoApprovalStep)
    {
        $memoApprovalStep->loadMissing('ieRequest', 'approver');
        $ieRequest = $memoApprovalStep->ieRequest;

        $this->authorizeStep($memoApprovalStep);

        if (!$ieRequest->memo_file) {
            return redirect()
                ->route('memo-approvals.index')
                ->with('error', 'Request ini tidak memiliki file memo untuk di-reject.');
        }

        if ($memoApprovalStep->status !== MemoApprovalStep::WAITING) {
            return redirect()
                ->route('memo-approvals.index')
                ->with('error', 'Approval step ini tidak sedang menunggu approval.');
        }

        $validated = $request->validate([
            'memo_rejected_reason' => 'required|string',
        ], [
            'memo_rejected_reason.required' => 'Alasan reject memo wajib diisi.',
        ]);

        $oldStatus = $ieRequest->memo_status;

        $memoApprovalStep->update([
            'status' => MemoApprovalStep::REJECTED,
            'approved_at' => null,
            'rejected_at' => now(),
            'note' => null,
            'rejected_reason' => $validated['memo_rejected_reason'],
        ]);

        $ieRequest->update([
            'memo_status' => 'Rejected',
            'memo_approved_by' => Auth::user()->name,
            'memo_approved_at' => now(),
            'memo_rejected_reason' => $validated['memo_rejected_reason'],
            'memo_approval_note' => $memoApprovalStep->approval_label . ' rejected: ' . $validated['memo_rejected_reason'],
        ]);

        $this->syncRequestStatus($ieRequest, RequestWorkflow::REQUEST_SUBMITTED, 'Memo Approval', 'Memo rejected');

        RequestActivity::record(
            $ieRequest->id,
            'Memo Approval',
            'Memo Rejected',
            $oldStatus,
            'Rejected',
            $memoApprovalStep->approval_label . ' - ' . $validated['memo_rejected_reason']
        );

        return redirect()
            ->route('memo-approvals.index')
            ->with('success', 'Memo berhasil di-reject.');
    }

    private function authorizeStep(MemoApprovalStep $memoApprovalStep): void
    {
        if (Auth::user()?->role === 'admin') {
            return;
        }

        if ((int) $memoApprovalStep->approver_user_id !== Auth::id()) {
            abort(403);
        }
    }
}
