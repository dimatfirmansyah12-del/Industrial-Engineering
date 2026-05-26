<?php

namespace App\Http\Controllers;

use App\Models\IeRequest;
use App\Models\RequestActivity;
use App\Models\RequestMaterial;
use App\Models\SapApproval;
use App\Services\RequestWorkflow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MaterialBomController extends Controller
{
    public function index(Request $request)
    {
        $query = IeRequest::query()
            ->where('drawing_status', 'Done')
            ->with('sapApproval')
            ->withCount('materials');

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('request_number', 'like', '%' . $search . '%')
                    ->orWhere('requester_name', 'like', '%' . $search . '%')
                    ->orWhere('department', 'like', '%' . $search . '%')
                    ->orWhere('line_area', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('bom_status') && in_array($request->bom_status, IeRequest::BOM_STATUSES, true)) {
            $query->where('bom_status', $request->bom_status);
        }

        $requests = $query->latest()->paginate(10)->withQueryString();

        return view('material-bom.index', [
            'requests' => $requests,
            'bomStatuses' => IeRequest::BOM_STATUSES,
        ]);
    }

    public function show(IeRequest $ieRequest)
    {
        if ($ieRequest->drawing_status !== 'Done') {
            return redirect()
                ->back()
                ->with('error', 'BOM hanya bisa dibuat setelah drawing status Done.');
        }

        $ieRequest->load([
            'sapApproval',
            'purchaseRequest',
            'materials' => function ($query) {
                $query->latest();
            },
        ]);

        return view('material-bom.show', [
            'ieRequest' => $ieRequest,
            'materialCategories' => $this->materialCategories(),
            'canEditBom' => $this->canEditBom($ieRequest),
            'canSubmitBom' => $this->canSubmitBom($ieRequest),
            'canReviseBom' => $this->canReviseBom($ieRequest),
        ]);
    }

    public function store(Request $request, IeRequest $ieRequest)
    {
        if ($ieRequest->drawing_status !== 'Done') {
            return redirect()
                ->back()
                ->with('error', 'Material belum bisa ditambahkan sebelum drawing Done.');
        }

        $ieRequest->loadMissing('purchaseRequest', 'sapApproval');

        if (! $this->canEditBom($ieRequest)) {
            return redirect()
                ->back()
                ->with('error', 'Material/BOM hanya bisa ditambahkan saat BOM masih Draft. Jika sudah submitted, klik Revisi BOM terlebih dahulu.');
        }

        $validated = $this->validateMaterial($request);
        $validated['material_name'] = $validated['material_category'];
        $validated['material_status'] = 'Need Purchase';
        $validated['estimated_price'] = 0;
        $validated['total_price'] = 0;

        $material = $ieRequest->materials()->create($validated);

        $ieRequest->update([
            'bom_status' => IeRequest::BOM_DRAFT,
            'bom_submitted_by' => null,
            'bom_submitted_at' => null,
        ]);

        RequestActivity::record(
            $ieRequest->id,
            'Material / BOM',
            'Material Added',
            null,
            $material->material_name
        );

        $this->syncRequestStatus(
            $ieRequest,
            RequestWorkflow::BOM_DRAFT,
            'Material / BOM',
            'BOM material added as draft'
        );

        return redirect()->back()->with('success', 'Material berhasil ditambahkan.');
    }

    public function submit(IeRequest $ieRequest)
    {
        $ieRequest->loadMissing('sapApproval', 'purchaseRequest');

        if (! $this->canSubmitBom($ieRequest)) {
            return redirect()
                ->back()
                ->with('error', 'BOM belum bisa disubmit. Pastikan drawing Done, material sudah ada, dan belum masuk proses SAP/PR.');
        }

        $oldStatus = $ieRequest->bom_status ?? IeRequest::BOM_NO_BOM;

        $ieRequest->update([
            'bom_status' => IeRequest::BOM_SUBMITTED,
            'bom_submitted_by' => Auth::user()?->name,
            'bom_submitted_at' => now(),
            'bom_revision_note' => null,
        ]);

        if ($ieRequest->sapApproval?->approval_status === SapApproval::REJECTED) {
            $ieRequest->sapApproval->update([
                'approval_status' => SapApproval::WAITING_SAP_INPUT,
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
            ]);
        }

        RequestActivity::record(
            $ieRequest->id,
            'Material / BOM',
            'BOM Submitted',
            $oldStatus,
            IeRequest::BOM_SUBMITTED
        );

        $this->syncRequestStatus(
            $ieRequest,
            RequestWorkflow::WAITING_SAP_INPUT,
            'Material / BOM',
            'BOM submitted and waiting PR input'
        );

        return redirect()->back()->with('success', 'BOM berhasil disubmit dan masuk ke Waiting PR Input.');
    }

    public function revise(Request $request, IeRequest $ieRequest)
    {
        $ieRequest->loadMissing('sapApproval', 'purchaseRequest');

        if (! $this->canReviseBom($ieRequest)) {
            return redirect()
                ->back()
                ->with('error', 'BOM tidak bisa direvisi karena sudah masuk proses SAP/PR atau belum submitted.');
        }

        $validated = $request->validate([
            'bom_revision_note' => 'nullable|string',
        ]);

        $oldStatus = $ieRequest->bom_status ?? IeRequest::BOM_NO_BOM;

        $ieRequest->update([
            'bom_status' => IeRequest::BOM_DRAFT,
            'bom_submitted_by' => null,
            'bom_submitted_at' => null,
            'bom_revision_note' => $validated['bom_revision_note'] ?? null,
        ]);

        RequestActivity::record(
            $ieRequest->id,
            'Material / BOM',
            'BOM Revision Opened',
            $oldStatus,
            IeRequest::BOM_DRAFT,
            $validated['bom_revision_note'] ?? null
        );

        $this->syncRequestStatus(
            $ieRequest,
            RequestWorkflow::BOM_DRAFT,
            'Material / BOM',
            'BOM revision opened'
        );

        return redirect()->back()->with('success', 'BOM dibuka kembali sebagai Draft untuk revisi.');
    }

    public function update(Request $request, RequestMaterial $requestMaterial)
    {
        $requestMaterial->loadMissing('ieRequest.purchaseRequest', 'ieRequest.sapApproval');

        if ($requestMaterial->ieRequest?->drawing_status !== 'Done') {
            return redirect()->back()->with('error', 'Material/BOM hanya bisa diubah setelah drawing Done.');
        }

        if (! $this->canEditBom($requestMaterial->ieRequest)) {
            return redirect()->back()->with('error', 'Material/BOM hanya bisa diubah saat BOM masih Draft.');
        }

        $validated = $this->validateMaterial($request);
        $validated['material_name'] = $validated['material_category'];
        $validated['material_status'] = $requestMaterial->material_status ?? 'Need Purchase';
        $validated['estimated_price'] = $requestMaterial->estimated_price ?? 0;
        $validated['total_price'] = $validated['qty'] * $validated['estimated_price'];

        $oldValue = $requestMaterial->material_name;

        $requestMaterial->update($validated);

        RequestActivity::record(
            $requestMaterial->ie_request_id,
            'Material / BOM',
            'Material Updated',
            $oldValue,
            $requestMaterial->material_name
        );

        return redirect()->back()->with('success', 'Material berhasil diperbarui.');
    }

    public function destroy(RequestMaterial $requestMaterial)
    {
        $requestMaterial->loadMissing('ieRequest.purchaseRequest', 'ieRequest.sapApproval');

        if (! $this->canEditBom($requestMaterial->ieRequest)) {
            return redirect()->back()->with('error', 'Material/BOM hanya bisa dihapus saat BOM masih Draft.');
        }

        $ieRequestId = $requestMaterial->ie_request_id;
        $ieRequest = $requestMaterial->ieRequest;
        $materialName = $requestMaterial->material_name;

        $requestMaterial->delete();

        RequestActivity::record(
            $ieRequestId,
            'Material / BOM',
            'Material Deleted',
            $materialName,
            null
        );

        if (! RequestMaterial::where('ie_request_id', $ieRequestId)->exists()) {
            $ieRequest->update([
                'bom_status' => IeRequest::BOM_NO_BOM,
                'bom_submitted_by' => null,
                'bom_submitted_at' => null,
            ]);

            $this->syncRequestStatus(
                $ieRequest,
                RequestWorkflow::DRAWING_DONE,
                'Material / BOM',
                'All BOM materials deleted'
            );
        }

        return redirect()->back()->with('success', 'Material berhasil dihapus.');
    }

    protected function validateMaterial(Request $request): array
    {
        return $request->validate([
            'material_category' => 'required|string|max:255',
            'specification' => 'nullable|string',
            'qty' => 'required|numeric|min:0',
            'unit' => 'nullable|string|max:50',
            'note' => 'nullable|string',
        ]);
    }

    private function materialCategories(): array
    {
        return [
            'Square Pipe' => 'batang',
            'Plate' => 'lembar',
            'Baut' => 'pcs',
            'Angkur' => 'pcs',
            'Plastik Lembaran' => 'lembar',
            'Cat' => 'kaleng',
            'Bearing' => 'pcs',
            'Roda' => 'pcs',
            'Bracket' => 'pcs',
            'Lainnya' => '',
        ];
    }

    private function sapApprovalAlreadyStarted(?IeRequest $ieRequest): bool
    {
        if (! $ieRequest) {
            return false;
        }

        $ieRequest->loadMissing('sapApproval');

        return $ieRequest->sapApproval
            && ! in_array($ieRequest->sapApproval->approval_status, [
                SapApproval::WAITING_SAP_INPUT,
                SapApproval::REJECTED,
            ], true);
    }

    private function canEditBom(?IeRequest $ieRequest): bool
    {
        if (! $ieRequest || $ieRequest->drawing_status !== 'Done') {
            return false;
        }

        $ieRequest->loadMissing('purchaseRequest', 'sapApproval');

        return ! $ieRequest->purchaseRequest
            && ! $this->sapApprovalAlreadyStarted($ieRequest)
            && $ieRequest->bom_status !== IeRequest::BOM_SUBMITTED;
    }

    private function canSubmitBom(?IeRequest $ieRequest): bool
    {
        if (! $ieRequest || $ieRequest->drawing_status !== 'Done') {
            return false;
        }

        $ieRequest->loadMissing('purchaseRequest', 'sapApproval');

        return $ieRequest->materials()->exists()
            && ! $ieRequest->purchaseRequest
            && ! $this->sapApprovalAlreadyStarted($ieRequest)
            && $ieRequest->bom_status !== IeRequest::BOM_SUBMITTED;
    }

    private function canReviseBom(?IeRequest $ieRequest): bool
    {
        if (! $ieRequest || $ieRequest->drawing_status !== 'Done') {
            return false;
        }

        $ieRequest->loadMissing('purchaseRequest', 'sapApproval');

        return $ieRequest->materials()->exists()
            && ! $ieRequest->purchaseRequest
            && ! $this->sapApprovalAlreadyStarted($ieRequest)
            && $ieRequest->bom_status === IeRequest::BOM_SUBMITTED;
    }
}
