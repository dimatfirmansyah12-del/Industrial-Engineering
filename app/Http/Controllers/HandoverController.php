<?php

namespace App\Http\Controllers;

use App\Models\Handover;
use App\Models\IeRequest;
use App\Models\RequestActivity;
use App\Services\RequestWorkflow;
use Illuminate\Http\Request;

class HandoverController extends Controller
{
    public function index(Request $request)
    {
        $query = IeRequest::query()
            ->whereHas('finalCheck', function ($checkQuery) {
                $checkQuery->where('check_status', 'Passed')
                    ->where('result_status', 'OK');
            })
            ->with(['finalCheck', 'handover']);

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('request_number', 'like', '%' . $search . '%')
                    ->orWhere('requester_name', 'like', '%' . $search . '%')
                    ->orWhere('department', 'like', '%' . $search . '%')
                    ->orWhere('line_area', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('handover_status')) {
            if ($request->handover_status === 'No Handover') {
                $query->whereDoesntHave('handover');
            } else {
                $query->whereHas('handover', function ($handoverQuery) use ($request) {
                    $handoverQuery->where('handover_status', $request->handover_status);
                });
            }
        }

        $ieRequests = $query->latest()->paginate(10)->withQueryString();

        return view('handovers.index', compact('ieRequests'));
    }

    public function show(IeRequest $ieRequest)
    {
        $ieRequest->load(['finalCheck', 'handover']);

        if (!$this->finalCheckPassedOk($ieRequest)) {
            return redirect()
                ->route('handovers.index')
                ->with('error', 'Handover hanya tersedia jika final check sudah Passed dan result OK.');
        }

        return view('handovers.show', compact('ieRequest'));
    }

    public function store(Request $request, IeRequest $ieRequest)
    {
        $ieRequest->load(['finalCheck', 'handover']);

        if (!$this->finalCheckPassedOk($ieRequest)) {
            return redirect()
                ->back()
                ->with('error', 'Handover belum bisa dibuat karena final check belum Passed OK.');
        }

        if ($ieRequest->handover) {
            return redirect()
                ->back()
                ->with('error', 'Handover untuk request ini sudah ada.');
        }

        $validated = $request->validate([
            'handover_date' => 'nullable|date',
            'handed_over_by' => 'required|string|max:255',
            'received_by' => 'nullable|string|max:255',
            'receiver_department' => 'nullable|string|max:255',
            'handover_note' => 'nullable|string',
            'evidence_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $evidenceFile = null;

        if ($request->hasFile('evidence_file')) {
            $evidenceFile = $request->file('evidence_file')->store('handovers/evidence', 'public');
        }

        $ieRequest->handover()->create([
            'final_check_id' => $ieRequest->finalCheck?->id,
            'handover_number' => $this->generateHandoverNumber(),
            'handover_date' => $validated['handover_date'] ?? null,
            'handed_over_by' => $validated['handed_over_by'],
            'received_by' => $validated['received_by'] ?? null,
            'receiver_department' => $validated['receiver_department'] ?? null,
            'handover_status' => 'Waiting Handover',
            'handover_note' => $validated['handover_note'] ?? null,
            'evidence_file' => $evidenceFile,
        ]);

        $this->syncRequestStatus($ieRequest, RequestWorkflow::WAITING_HANDOVER, 'Handover', 'Handover created');

        return redirect()->back()->with('success', 'Handover berhasil dibuat.');
    }

    public function process(Handover $handover)
    {
        if (! in_array($handover->handover_status, ['Waiting Handover', 'Rejected'], true)) {
            return redirect()->back()->with('error', 'Handover hanya bisa diproses dari status Waiting Handover atau Rejected.');
        }

        $oldStatus = $handover->handover_status;

        $handover->update([
            'handover_status' => 'Handover Process',
        ]);

        $this->syncRequestStatus($handover->ieRequest, RequestWorkflow::WAITING_HANDOVER, 'Handover', 'Handover in process');

        RequestActivity::record(
            $handover->ie_request_id,
            'Handover',
            'Handover Process',
            $oldStatus,
            'Handover Process'
        );

        return redirect()->back()->with('success', 'Handover masuk proses serah terima.');
    }

    public function received(Request $request, Handover $handover)
    {
        if (! in_array($handover->handover_status, ['Waiting Handover', 'Handover Process', 'Rejected'], true)) {
            return redirect()->back()->with('error', 'Handover hanya bisa diterima dari status Waiting Handover, Handover Process, atau Rejected.');
        }

        $validated = $request->validate([
            'received_by' => 'required|string|max:255',
            'receiver_department' => 'nullable|string|max:255',
            'receiver_note' => 'nullable|string',
            'evidence_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $updateData = [
            'handover_status' => 'Received',
            'received_by' => $validated['received_by'],
            'receiver_department' => $validated['receiver_department'] ?? null,
            'receiver_note' => $validated['receiver_note'] ?? null,
        ];

        if ($request->hasFile('evidence_file')) {
            $updateData['evidence_file'] = $request->file('evidence_file')->store('handovers/evidence', 'public');
        }

        $oldStatus = $handover->handover_status;

        $handover->update($updateData);
        $this->syncRequestStatus($handover->ieRequest, RequestWorkflow::CLOSED, 'Handover', 'Handover received');

        RequestActivity::record(
            $handover->ie_request_id,
            'Handover',
            'Handover Received',
            $oldStatus,
            'Received',
            $validated['receiver_note'] ?? null
        );

        RequestActivity::record(
            $handover->ie_request_id,
            'Handover',
            'Request Closed',
            null,
            'Closed'
        );

        return redirect()->back()->with('success', 'Handover berhasil diterima dan request ditutup.');
    }

    public function reject(Request $request, Handover $handover)
    {
        if (! in_array($handover->handover_status, ['Waiting Handover', 'Handover Process'], true)) {
            return redirect()->back()->with('error', 'Handover hanya bisa ditolak dari status Waiting Handover atau Handover Process.');
        }

        $validated = $request->validate([
            'receiver_note' => 'required|string',
        ], [
            'receiver_note.required' => 'Alasan reject handover wajib diisi.',
        ]);

        $oldStatus = $handover->handover_status;

        $handover->update([
            'handover_status' => 'Rejected',
            'receiver_note' => $validated['receiver_note'],
        ]);

        $this->syncRequestStatus($handover->ieRequest, RequestWorkflow::WAITING_HANDOVER, 'Handover', 'Handover rejected');

        RequestActivity::record(
            $handover->ie_request_id,
            'Handover',
            'Handover Rejected',
            $oldStatus,
            'Rejected',
            $validated['receiver_note']
        );

        return redirect()->back()->with('success', 'Handover ditolak. Status request utama tidak ditutup.');
    }

    protected function generateHandoverNumber(): string
    {
        $year = date('Y');

        $lastHandover = Handover::where('handover_number', 'like', 'HO-' . $year . '-%')
            ->orderByDesc('id')
            ->first();

        $nextNumber = 1;

        if ($lastHandover) {
            $nextNumber = ((int) substr($lastHandover->handover_number, -4)) + 1;
        }

        return 'HO-' . $year . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    private function finalCheckPassedOk(IeRequest $ieRequest): bool
    {
        return $ieRequest->finalCheck
            && $ieRequest->finalCheck->check_status === 'Passed'
            && $ieRequest->finalCheck->result_status === 'OK';
    }
}
