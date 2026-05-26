<?php

namespace App\Http\Controllers;

use App\Models\IeRequest;
use App\Models\PurchaseRequest;
use App\Models\RequestActivity;
use App\Models\SapApproval;
use App\Services\RequestWorkflow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SapApprovalController extends Controller
{
    public function index(Request $request)
    {
        $query = IeRequest::query()
            ->where('drawing_status', 'Done')
            ->where('bom_status', IeRequest::BOM_SUBMITTED)
            ->whereHas('materials')
            ->with('sapApproval');

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('request_number', 'like', '%' . $search . '%')
                    ->orWhere('requester_name', 'like', '%' . $search . '%')
                    ->orWhere('department', 'like', '%' . $search . '%')
                    ->orWhere('line_area', 'like', '%' . $search . '%')
                    ->orWhereHas('sapApproval', function ($approvalQuery) use ($search) {
                        $approvalQuery->where('sap_number', 'like', '%' . $search . '%')
                            ->orWhere('sap_description', 'like', '%' . $search . '%');
                    });
            });
        }

        if ($request->approval_status === SapApproval::WAITING_SAP_INPUT) {
            $query->where(function ($statusQuery) {
                $statusQuery->whereDoesntHave('sapApproval')
                    ->orWhereHas('sapApproval', function ($approvalQuery) {
                        $approvalQuery->where('approval_status', SapApproval::WAITING_SAP_INPUT);
                    });
            });
        } elseif (in_array($request->approval_status, SapApproval::STATUSES, true)) {
            $query->whereHas('sapApproval', function ($q) use ($request) {
                $q->where('approval_status', $request->approval_status);
            });
        }

        $requests = $query->latest()->paginate(10)->withQueryString();

        return view('sap-approvals.index', [
            'requests' => $requests,
            'approvalStatuses' => SapApproval::STATUSES,
        ]);
    }

    public function show(IeRequest $ieRequest)
    {
        if (
            $ieRequest->drawing_status !== 'Done'
            || $ieRequest->bom_status !== IeRequest::BOM_SUBMITTED
            || ! $ieRequest->materials()->exists()
        ) {
            return redirect()
                ->back()
                ->with('error', 'SAP / PR Approval hanya bisa diproses setelah drawing Done dan BOM sudah disubmit.');
        }

        $ieRequest->load([
            'sapApproval',
            'purchaseRequest',
        ]);

        return view('sap-approvals.show', compact('ieRequest'));
    }

    public function storeSapInput(Request $request, IeRequest $ieRequest)
    {
        $this->ensureRole(['admin']);

        if (
            $ieRequest->drawing_status !== 'Done'
            || $ieRequest->bom_status !== IeRequest::BOM_SUBMITTED
            || ! $ieRequest->materials()->exists()
        ) {
            return redirect()->back()->with('error', 'No. PR SAP hanya bisa diinput setelah drawing Done dan BOM sudah disubmit.');
        }

        if ($ieRequest->purchaseRequest()->exists()) {
            return redirect()->back()->with('error', 'No. PR SAP tidak bisa diubah setelah request dikirim ke purchasing.');
        }

        $sapApproval = $ieRequest->sapApproval;

        if ($sapApproval && ! in_array($sapApproval->approval_status, [SapApproval::WAITING_SAP_INPUT, SapApproval::REJECTED], true)) {
            return redirect()->back()->with('error', 'No. PR SAP hanya bisa diinput saat status Waiting PR Input atau Rejected.');
        }

        $validated = $request->validate([
            'sap_description' => 'required|string',
            'sap_number' => [
                'required',
                'string',
                'max:255',
                Rule::unique('sap_approvals', 'sap_number')->ignore($sapApproval?->id),
                Rule::unique('purchase_requests', 'pr_number'),
            ],
            'purchase_value' => 'required|numeric|min:0',
        ]);

        $oldStatus = $sapApproval?->approval_status ?? SapApproval::WAITING_SAP_INPUT;

        $sapApproval = $ieRequest->sapApproval()->updateOrCreate(
            ['ie_request_id' => $ieRequest->id],
            [
                'sap_description' => $validated['sap_description'],
                'sap_number' => $validated['sap_number'],
                'purchase_value' => $validated['purchase_value'],
                'sap_input_date' => now()->toDateString(),
                'sap_input_by' => Auth::user()?->name,
                'sap_input_at' => now(),
                'approval_status' => SapApproval::WAITING_SECTION_HEAD,
                'section_head_status' => 'Waiting',
                'section_head_by' => null,
                'section_head_at' => null,
                'section_head_note' => null,
                'section_head_rejected_reason' => null,
                'division_head_status' => 'Waiting',
                'division_head_by' => null,
                'division_head_at' => null,
                'division_head_note' => null,
                'division_head_rejected_reason' => null,
                'director_status' => 'Waiting',
                'director_by' => null,
                'director_at' => null,
                'director_note' => null,
                'director_rejected_reason' => null,
                'sent_to_purchasing_by' => null,
                'sent_to_purchasing_at' => null,
            ]
        );

        $this->syncRequestStatus($ieRequest, RequestWorkflow::WAITING_SECTION_HEAD_APPROVAL, 'SAP / PR Approval', 'No. PR SAP submitted');

        RequestActivity::record(
            $ieRequest->id,
            'SAP / PR Approval',
            'No. PR SAP Submitted',
            $oldStatus,
            $sapApproval->approval_status,
            $sapApproval->sap_number
        );

        return redirect()->back()->with('success', 'No. PR SAP berhasil diinput dan dikirim ke Atasan IE Approval.');
    }

    public function sectionApprove(Request $request, SapApproval $sapApproval)
    {
        $this->ensureRole(['admin', 'section_head']);

        if ($sapApproval->approval_status !== SapApproval::WAITING_SECTION_HEAD) {
            return redirect()->back()->with('error', 'Atasan IE hanya bisa approve dari status Waiting Atasan IE Approval.');
        }

        $validated = $request->validate([
            'section_head_note' => 'nullable|string',
        ]);

        $sapApproval->update([
            'approval_status' => SapApproval::WAITING_DIVISION_HEAD,
            'section_head_status' => 'Approved',
            'section_head_by' => Auth::user()?->name,
            'section_head_at' => now(),
            'section_head_note' => $validated['section_head_note'] ?? null,
            'section_head_rejected_reason' => null,
        ]);

        $this->syncRequestStatus($sapApproval->ieRequest, RequestWorkflow::WAITING_DIVISION_HEAD_APPROVAL, 'SAP / PR Approval', 'Approved by Atasan IE');

        RequestActivity::record(
            $sapApproval->ie_request_id,
            'SAP / PR Approval',
            'Atasan IE Approved',
            SapApproval::WAITING_SECTION_HEAD,
            SapApproval::WAITING_DIVISION_HEAD,
            $validated['section_head_note'] ?? null
        );

        return redirect()->back()->with('success', 'Atasan IE Approval berhasil disetujui.');
    }

    public function sectionReject(Request $request, SapApproval $sapApproval)
    {
        $this->ensureRole(['admin', 'section_head']);

        if ($sapApproval->approval_status !== SapApproval::WAITING_SECTION_HEAD) {
            return redirect()->back()->with('error', 'Atasan IE hanya bisa reject dari status Waiting Atasan IE Approval.');
        }

        $validated = $request->validate([
            'section_head_rejected_reason' => 'required|string',
        ], [
            'section_head_rejected_reason.required' => 'Alasan reject Atasan IE wajib diisi.',
        ]);

        $sapApproval->update([
            'approval_status' => SapApproval::REJECTED,
            'section_head_status' => 'Rejected',
            'section_head_by' => Auth::user()?->name,
            'section_head_at' => now(),
            'section_head_rejected_reason' => $validated['section_head_rejected_reason'],
        ]);

        $this->syncRequestStatus($sapApproval->ieRequest, RequestWorkflow::SAP_APPROVAL_REJECTED, 'SAP / PR Approval', 'Rejected by Atasan IE');

        RequestActivity::record(
            $sapApproval->ie_request_id,
            'SAP / PR Approval',
            'Atasan IE Rejected',
            SapApproval::WAITING_SECTION_HEAD,
            SapApproval::REJECTED,
            $validated['section_head_rejected_reason']
        );

        return redirect()->back()->with('success', 'PR Approval berhasil di-reject oleh Atasan IE.');
    }

    public function divisionApprove(Request $request, SapApproval $sapApproval)
    {
        $this->ensureRole(['admin', 'division_head']);

        if ($sapApproval->approval_status !== SapApproval::WAITING_DIVISION_HEAD) {
            return redirect()->back()->with('error', 'Division Head hanya bisa approve dari status Waiting Division Head Approval.');
        }

        $validated = $request->validate([
            'division_head_note' => 'nullable|string',
        ]);

        $sapApproval->update([
            'approval_status' => SapApproval::WAITING_DIRECTOR,
            'division_head_status' => 'Approved',
            'division_head_by' => Auth::user()?->name,
            'division_head_at' => now(),
            'division_head_note' => $validated['division_head_note'] ?? null,
            'division_head_rejected_reason' => null,
            'director_status' => 'Waiting',
            'director_by' => null,
            'director_at' => null,
            'director_note' => null,
            'director_rejected_reason' => null,
        ]);

        $this->syncRequestStatus($sapApproval->ieRequest, RequestWorkflow::WAITING_DIRECTOR_APPROVAL, 'SAP / PR Approval', 'Approved by Division Head');

        RequestActivity::record(
            $sapApproval->ie_request_id,
            'SAP / PR Approval',
            'Division Head Approved',
            SapApproval::WAITING_DIVISION_HEAD,
            SapApproval::WAITING_DIRECTOR,
            $validated['division_head_note'] ?? null
        );

        return redirect()->back()->with('success', 'Division Head Approval berhasil disetujui dan dikirim ke Direktur.');
    }

    public function divisionReject(Request $request, SapApproval $sapApproval)
    {
        $this->ensureRole(['admin', 'division_head']);

        if ($sapApproval->approval_status !== SapApproval::WAITING_DIVISION_HEAD) {
            return redirect()->back()->with('error', 'Division Head hanya bisa reject dari status Waiting Division Head Approval.');
        }

        $validated = $request->validate([
            'division_head_rejected_reason' => 'required|string',
        ], [
            'division_head_rejected_reason.required' => 'Alasan reject Division Head wajib diisi.',
        ]);

        $sapApproval->update([
            'approval_status' => SapApproval::REJECTED,
            'division_head_status' => 'Rejected',
            'division_head_by' => Auth::user()?->name,
            'division_head_at' => now(),
            'division_head_rejected_reason' => $validated['division_head_rejected_reason'],
        ]);

        $this->syncRequestStatus($sapApproval->ieRequest, RequestWorkflow::SAP_APPROVAL_REJECTED, 'SAP / PR Approval', 'Rejected by Division Head');

        RequestActivity::record(
            $sapApproval->ie_request_id,
            'SAP / PR Approval',
            'Division Head Rejected',
            SapApproval::WAITING_DIVISION_HEAD,
            SapApproval::REJECTED,
            $validated['division_head_rejected_reason']
        );

        return redirect()->back()->with('success', 'PR Approval berhasil di-reject oleh Division Head.');
    }

    public function directorApprove(Request $request, SapApproval $sapApproval)
    {
        $this->ensureRole(['admin', 'director']);

        if ($sapApproval->approval_status !== SapApproval::WAITING_DIRECTOR) {
            return redirect()->back()->with('error', 'Direktur hanya bisa approve dari status Waiting Director Approval.');
        }

        $validated = $request->validate([
            'director_note' => 'nullable|string',
        ]);

        $sapApproval->loadMissing('ieRequest.purchaseRequest');

        if (! $sapApproval->sap_number) {
            return redirect()->back()->with('error', 'No. PR wajib ada sebelum Direktur approve.');
        }

        $prNumberUsed = PurchaseRequest::query()
            ->where('pr_number', $sapApproval->sap_number)
            ->where('ie_request_id', '!=', $sapApproval->ie_request_id)
            ->exists();

        if ($prNumberUsed) {
            return redirect()->back()->with('error', 'No. PR sudah dipakai request lain.');
        }

        DB::transaction(function () use ($sapApproval, $validated) {
            $sapApproval->update([
                'approval_status' => SapApproval::SENT_TO_PURCHASING,
                'director_status' => 'Approved',
                'director_by' => Auth::user()?->name,
                'director_at' => now(),
                'director_note' => $validated['director_note'] ?? null,
                'director_rejected_reason' => null,
                'sent_to_purchasing_by' => 'System',
                'sent_to_purchasing_at' => now(),
            ]);

            if (! $sapApproval->ieRequest?->purchaseRequest) {
                $sapApproval->ieRequest?->purchaseRequest()->create([
                    'pr_number' => $sapApproval->sap_number,
                    'pr_date' => $sapApproval->sap_input_date,
                    'total_budget' => $sapApproval->purchase_value ?? 0,
                    'pr_status' => 'Approved',
                    'requested_by' => $sapApproval->sap_input_by,
                    'approved_by' => Auth::user()?->name,
                    'approved_at' => now(),
                    'note' => $sapApproval->sap_description ?: 'Dibuat otomatis setelah Direktur approve SAP / PR Approval.',
                ]);
            }
        });

        $this->syncRequestStatus($sapApproval->ieRequest, RequestWorkflow::SENT_TO_PURCHASING, 'SAP / PR Approval', 'Approved by Director and sent to purchasing');

        RequestActivity::record(
            $sapApproval->ie_request_id,
            'SAP / PR Approval',
            'Director Approved and Sent to Purchasing',
            SapApproval::WAITING_DIRECTOR,
            SapApproval::SENT_TO_PURCHASING,
            $validated['director_note'] ?? null
        );

        return redirect()->back()->with('success', 'Director Approval berhasil disetujui dan request otomatis masuk ke purchasing.');
    }

    public function directorReject(Request $request, SapApproval $sapApproval)
    {
        $this->ensureRole(['admin', 'director']);

        if ($sapApproval->approval_status !== SapApproval::WAITING_DIRECTOR) {
            return redirect()->back()->with('error', 'Direktur hanya bisa reject dari status Waiting Director Approval.');
        }

        $validated = $request->validate([
            'director_rejected_reason' => 'required|string',
        ], [
            'director_rejected_reason.required' => 'Alasan reject Direktur wajib diisi.',
        ]);

        $sapApproval->update([
            'approval_status' => SapApproval::REJECTED,
            'director_status' => 'Rejected',
            'director_by' => Auth::user()?->name,
            'director_at' => now(),
            'director_rejected_reason' => $validated['director_rejected_reason'],
        ]);

        $this->syncRequestStatus($sapApproval->ieRequest, RequestWorkflow::SAP_APPROVAL_REJECTED, 'SAP / PR Approval', 'Rejected by Director');

        RequestActivity::record(
            $sapApproval->ie_request_id,
            'SAP / PR Approval',
            'Director Rejected',
            SapApproval::WAITING_DIRECTOR,
            SapApproval::REJECTED,
            $validated['director_rejected_reason']
        );

        return redirect()->back()->with('success', 'PR Approval berhasil di-reject oleh Direktur.');
    }

    private function ensureRole(array $roles): void
    {
        if (! in_array(Auth::user()?->role, $roles, true)) {
            abort(403);
        }
    }
}
