<?php

namespace App\Http\Controllers;

use App\Models\IeRequest;
use App\Models\MemoApprovalStep;
use App\Models\RequestActivity;
use Illuminate\Http\Request;

class DrawingProgressController extends Controller
{
    public function index(Request $request)
    {
        $query = IeRequest::query()
            ->where('memo_status', 'Approved')
            ->whereNotNull('memo_file')
            ->where('memo_file', '!=', '')
            ->whereHas('memoApprovalSteps')
            ->whereDoesntHave('memoApprovalSteps', function ($approvalQuery) {
                $approvalQuery->where('status', '!=', MemoApprovalStep::APPROVED);
            });

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('request_number', 'like', '%' . $search . '%')
                    ->orWhere('requester_name', 'like', '%' . $search . '%')
                    ->orWhere('department', 'like', '%' . $search . '%')
                    ->orWhere('line_area', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('drawing_status')) {
            $query->where('drawing_status', $request->drawing_status);
        }

        $requests = $query->latest()->paginate(10)->withQueryString();

        return view('drawing-progress.index', compact('requests'));
    }

    public function assign(Request $request, IeRequest $ieRequest)
    {
        if (! $this->memoFullyApproved($ieRequest)) {
            return redirect()->back()->with('error', 'Drafter tidak bisa di-assign sebelum semua memo approver selesai approve.');
        }

        $validated = $request->validate([
            'assigned_drafter' => 'required|string|max:255',
        ]);

        $ieRequest->update([
            'assigned_drafter' => $validated['assigned_drafter'],
        ]);

        return redirect()->back()->with('success', 'Assigned drafter berhasil disimpan.');
    }

    public function start(IeRequest $ieRequest)
    {
        if (! $this->memoFullyApproved($ieRequest)) {
            return redirect()->back()->with('error', 'Drawing tidak bisa dimulai sebelum semua memo approver selesai approve.');
        }

        $ieRequest->update([
            'drawing_status' => 'On Progress',
            'drawing_started_at' => $ieRequest->drawing_started_at ?? now(),
            'drawing_revision_note' => null,
        ]);

        $this->syncRequestStatus($ieRequest, 'Drawing On Progress', 'Drawing Progress', 'Drawing started');

        RequestActivity::record(
            $ieRequest->id,
            'Drawing Progress',
            'Drawing Started'
        );

        return redirect()->back()->with('success', 'Drawing berhasil dimulai.');
    }

    public function revision(Request $request, IeRequest $ieRequest)
    {
        if (! $this->memoFullyApproved($ieRequest)) {
            return redirect()->back()->with('error', 'Drawing belum bisa direvisi sebelum semua memo approver selesai approve.');
        }

        $validated = $request->validate([
            'drawing_revision_note' => 'required|string',
        ], [
            'drawing_revision_note.required' => 'Catatan revisi wajib diisi.',
        ]);

        $ieRequest->update([
            'drawing_status' => 'Revision',
            'drawing_revision_note' => $validated['drawing_revision_note'],
            'drawing_finished_at' => null,
        ]);

        $this->syncRequestStatus($ieRequest, 'Drawing On Progress', 'Drawing Progress', 'Drawing revision');

        RequestActivity::record(
            $ieRequest->id,
            'Drawing Progress',
            'Drawing Revision',
            null,
            'Revision',
            $validated['drawing_revision_note']
        );

        return redirect()->back()->with('success', 'Status drawing berhasil diubah ke Revision.');
    }

    public function done(Request $request, IeRequest $ieRequest)
    {
        if (! $this->memoFullyApproved($ieRequest)) {
            return redirect()->back()->with('error', 'Drawing belum bisa diselesaikan sebelum semua memo approver selesai approve.');
        }

        $validated = $request->validate([
            'drawing_note' => 'nullable|string',
            'drawing_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png,dwg,dxf|max:10240',
        ]);

        if ($request->hasFile('drawing_file')) {
            $validated['drawing_file'] = $request->file('drawing_file')->store('ie-requests/drawing', 'public');
        } else {
            unset($validated['drawing_file']);
        }

        $validated['drawing_status'] = 'Done';
        $validated['drawing_finished_at'] = now();
        $validated['drawing_revision_note'] = null;

        if (!$ieRequest->drawing_started_at) {
            $validated['drawing_started_at'] = now();
        }

        $ieRequest->update($validated);

        $this->syncRequestStatus($ieRequest, 'Drawing Done', 'Drawing Progress', 'Drawing done');

        RequestActivity::record(
            $ieRequest->id,
            'Drawing Progress',
            'Drawing Done'
        );

        return redirect()->back()->with('success', 'Drawing berhasil diselesaikan.');
    }

    private function memoFullyApproved(IeRequest $ieRequest): bool
    {
        if ($ieRequest->memo_status !== 'Approved' || ! $ieRequest->memo_file) {
            return false;
        }

        $ieRequest->loadMissing('memoApprovalSteps');

        return $ieRequest->memoApprovalSteps->isNotEmpty()
            && $ieRequest->memoApprovalSteps->every(
                fn (MemoApprovalStep $step) => $step->status === MemoApprovalStep::APPROVED
            );
    }
}
