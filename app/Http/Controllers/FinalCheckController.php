<?php

namespace App\Http\Controllers;

use App\Models\FinalCheck;
use App\Models\IeRequest;
use App\Models\RequestActivity;
use App\Services\RequestWorkflow;
use Illuminate\Http\Request;

class FinalCheckController extends Controller
{
    public function index(Request $request)
    {
        $query = IeRequest::query()
            ->whereHas('workshopSchedule', function ($scheduleQuery) {
                $scheduleQuery->where('progress_status', 'Done');
            })
            ->with(['workshopSchedule', 'finalCheck']);

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('request_number', 'like', '%' . $search . '%')
                    ->orWhere('requester_name', 'like', '%' . $search . '%')
                    ->orWhere('department', 'like', '%' . $search . '%')
                    ->orWhere('line_area', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('check_status')) {
            if ($request->check_status === 'No Check') {
                $query->whereDoesntHave('finalCheck');
            } else {
                $query->whereHas('finalCheck', function ($checkQuery) use ($request) {
                    $checkQuery->where('check_status', $request->check_status);
                });
            }
        }

        $ieRequests = $query->latest()->paginate(10)->withQueryString();

        return view('final-checks.index', compact('ieRequests'));
    }

    public function show(IeRequest $ieRequest)
    {
        $ieRequest->load(['workshopSchedule', 'finalCheck']);

        if (!$this->workshopProgressDone($ieRequest)) {
            return redirect()
                ->route('final-checks.index')
                ->with('error', 'Final check hanya tersedia jika workshop progress sudah Done.');
        }

        return view('final-checks.show', compact('ieRequest'));
    }

    public function store(Request $request, IeRequest $ieRequest)
    {
        $ieRequest->load(['workshopSchedule', 'finalCheck']);

        if (!$this->workshopProgressDone($ieRequest)) {
            return redirect()
                ->back()
                ->with('error', 'Final check belum bisa dibuat karena workshop progress belum Done.');
        }

        if ($ieRequest->finalCheck) {
            return redirect()
                ->back()
                ->with('error', 'Final check untuk request ini sudah ada.');
        }

        $validated = $request->validate([
            'check_date' => 'nullable|date',
            'checked_by' => 'required|string|max:255',
            'final_note' => 'nullable|string',
            'evidence_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $evidenceFile = null;

        if ($request->hasFile('evidence_file')) {
            $evidenceFile = $request->file('evidence_file')->store('final-checks/evidence', 'public');
        }

        $ieRequest->finalCheck()->create([
            'workshop_schedule_id' => $ieRequest->workshopSchedule?->id,
            'check_date' => $validated['check_date'] ?? null,
            'checked_by' => $validated['checked_by'],
            'check_status' => 'Waiting Check',
            'final_note' => $validated['final_note'] ?? null,
            'evidence_file' => $evidenceFile,
        ]);

        $this->syncRequestStatus($ieRequest, 'Final Check', 'Final Check', 'Final check created');

        return redirect()->back()->with('success', 'Final check berhasil dibuat.');
    }

    public function checking(FinalCheck $finalCheck)
    {
        if ($this->handoverReceived($finalCheck)) {
            return redirect()->back()->with('error', 'Final check tidak bisa diubah karena handover sudah Received.');
        }

        $oldStatus = $finalCheck->check_status;

        $finalCheck->update([
            'check_status' => 'Checking',
            'check_date' => $finalCheck->check_date ?? now()->toDateString(),
        ]);

        $this->syncRequestStatus($finalCheck->ieRequest, 'Final Check', 'Final Check', 'Final check started');

        RequestActivity::record(
            $finalCheck->ie_request_id,
            'Final Check',
            'Final Check Started',
            $oldStatus,
            'Checking'
        );

        return redirect()->back()->with('success', 'Final check mulai diproses.');
    }

    public function passed(Request $request, FinalCheck $finalCheck)
    {
        if ($this->handoverReceived($finalCheck)) {
            return redirect()->back()->with('error', 'Final check tidak bisa diubah karena handover sudah Received.');
        }

        $validated = $request->validate([
            'final_note' => 'nullable|string',
            'correction_note' => 'nullable|string',
            'evidence_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $updateData = [
            'check_status' => 'Passed',
            'result_status' => 'OK',
            'problem_note' => null,
            'correction_note' => $validated['correction_note'] ?? null,
            'final_note' => $validated['final_note'] ?? $finalCheck->final_note,
            'check_date' => $finalCheck->check_date ?? now()->toDateString(),
        ];

        if ($request->hasFile('evidence_file')) {
            $updateData['evidence_file'] = $request->file('evidence_file')->store('final-checks/evidence', 'public');
        }

        $oldStatus = $finalCheck->check_status;

        $finalCheck->update($updateData);

        $this->syncRequestStatus($finalCheck->ieRequest, RequestWorkflow::WAITING_HANDOVER, 'Final Check', 'Final check passed OK');

        RequestActivity::record(
            $finalCheck->ie_request_id,
            'Final Check',
            'Final Check Passed',
            $oldStatus,
            'Passed',
            $validated['final_note'] ?? null
        );

        return redirect()->back()->with('success', 'Final check berhasil dipass-kan dengan result OK.');
    }

    public function needRework(Request $request, FinalCheck $finalCheck)
    {
        if ($this->handoverReceived($finalCheck)) {
            return redirect()->back()->with('error', 'Final check tidak bisa diubah karena handover sudah Received.');
        }

        $validated = $request->validate([
            'problem_note' => 'required|string',
            'correction_note' => 'nullable|string',
        ]);

        $oldStatus = $finalCheck->check_status;

        $finalCheck->update([
            'check_status' => 'Need Rework',
            'result_status' => 'NG',
            'problem_note' => $validated['problem_note'],
            'correction_note' => $validated['correction_note'] ?? null,
            'check_date' => $finalCheck->check_date ?? now()->toDateString(),
        ]);

        if ($finalCheck->workshopSchedule) {
            $finalCheck->workshopSchedule->update([
                'progress_status' => 'Rework',
                'schedule_status' => 'In Progress',
                'problem_note' => $validated['problem_note'],
            ]);
        }

        $this->syncRequestStatus($finalCheck->ieRequest, 'Workshop On Progress', 'Final Check', 'Need rework from final check');

        RequestActivity::record(
            $finalCheck->ie_request_id,
            'Final Check',
            'Final Check Need Rework',
            $oldStatus,
            'Need Rework',
            $validated['problem_note']
        );

        return redirect()
            ->route('final-checks.index')
            ->with('success', 'Final check ditandai Need Rework dan workshop progress dikembalikan ke Rework.');
    }

    public function failed(Request $request, FinalCheck $finalCheck)
    {
        if ($this->handoverReceived($finalCheck)) {
            return redirect()->back()->with('error', 'Final check tidak bisa diubah karena handover sudah Received.');
        }

        $validated = $request->validate([
            'problem_note' => 'required|string',
        ]);

        $oldStatus = $finalCheck->check_status;

        $finalCheck->update([
            'check_status' => 'Failed',
            'result_status' => 'NG',
            'problem_note' => $validated['problem_note'],
            'check_date' => $finalCheck->check_date ?? now()->toDateString(),
        ]);

        $this->syncRequestStatus($finalCheck->ieRequest, 'Final Check', 'Final Check', 'Final check failed');

        RequestActivity::record(
            $finalCheck->ie_request_id,
            'Final Check',
            'Final Check Failed',
            $oldStatus,
            'Failed',
            $validated['problem_note']
        );

        return redirect()->back()->with('success', 'Final check ditandai Failed.');
    }

    private function workshopProgressDone(IeRequest $ieRequest): bool
    {
        return $ieRequest->workshopSchedule
            && $ieRequest->workshopSchedule->progress_status === 'Done';
    }

    private function handoverReceived(FinalCheck $finalCheck): bool
    {
        $finalCheck->loadMissing('handover');

        return $finalCheck->handover
            && $finalCheck->handover->handover_status === 'Received';
    }
}
