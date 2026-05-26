<?php

namespace App\Http\Controllers;

use App\Models\IeRequest;
use App\Models\RequestActivity;
use App\Models\RequestMaterial;
use App\Services\RequestWorkflow;
use Illuminate\Http\Request;

class MaterialArrivalController extends Controller
{
    public function index(Request $request)
    {
        $query = IeRequest::query()
            ->whereHas('purchaseRequest', function ($q) {
                $q->where('pr_status', 'PO Created');
            })
            ->whereHas('materials')
            ->with('purchaseRequest')
            ->withCount([
                'materials',
                'materials as complete_materials_count' => function ($q) {
                    $q->where('arrival_status', 'Complete');
                },
            ])
            ->withSum('materials', 'arrived_qty');

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('request_number', 'like', '%' . $search . '%')
                    ->orWhere('requester_name', 'like', '%' . $search . '%')
                    ->orWhere('department', 'like', '%' . $search . '%')
                    ->orWhere('line_area', 'like', '%' . $search . '%');
            });
        }

        if ($request->arrival_status === 'Waiting Material') {
            $query->whereDoesntHave('materials', function ($q) {
                $q->where('arrived_qty', '>', 0);
            });
        }

        if ($request->arrival_status === 'Partial Arrived') {
            $query->whereHas('materials', function ($q) {
                $q->where('arrived_qty', '>', 0);
            })->whereHas('materials', function ($q) {
                $q->where('arrival_status', '!=', 'Complete');
            });
        }

        if ($request->arrival_status === 'Complete') {
            $query->whereDoesntHave('materials', function ($q) {
                $q->where('arrival_status', '!=', 'Complete');
            });
        }

        $requests = $query->latest()->paginate(10)->withQueryString();

        return view('material-arrivals.index', compact('requests'));
    }

    public function show(IeRequest $ieRequest)
    {
        if (!$ieRequest->purchaseRequest || $ieRequest->purchaseRequest->pr_status !== 'PO Created') {
            return redirect()
                ->back()
                ->with('error', 'Material arrival hanya bisa diproses setelah PR status PO Created.');
        }

        $ieRequest->load(['purchaseRequest', 'materials' => function ($query) {
            $query->latest();
        }]);

        $totalMaterial = $ieRequest->materials->count();
        $completeMaterial = $ieRequest->materials->where('arrival_status', 'Complete')->count();
        $partialMaterial = $ieRequest->materials->where('arrival_status', 'Partial Arrived')->count();
        $waitingMaterial = $ieRequest->materials->where('arrival_status', 'Waiting Material')->count();

        return view('material-arrivals.show', compact(
            'ieRequest',
            'totalMaterial',
            'completeMaterial',
            'partialMaterial',
            'waitingMaterial'
        ));
    }

    public function updateMaterial(Request $request, RequestMaterial $requestMaterial)
    {
        $requestMaterial->loadMissing('ieRequest.purchaseRequest');

        if ($this->requestClosed($requestMaterial->ieRequest)) {
            return redirect()->back()->with('error', 'Material arrival tidak bisa diubah karena request sudah Closed.');
        }

        if (!$requestMaterial->ieRequest?->purchaseRequest || $requestMaterial->ieRequest->purchaseRequest->pr_status !== 'PO Created') {
            return redirect()
                ->back()
                ->with('error', 'Kedatangan material hanya bisa di-update setelah PR status PO Created.');
        }

        $validated = $request->validate([
            'arrived_qty' => 'required|numeric|min:0',
            'arrival_date' => 'nullable|date',
            'arrival_note' => 'nullable|string',
        ]);

        if ((float) $validated['arrived_qty'] > (float) $requestMaterial->qty) {
            return redirect()
                ->back()
                ->withErrors(['arrived_qty' => 'Arrived qty tidak boleh lebih besar dari qty material.'])
                ->withInput();
        }

        $oldArrivalStatus = $requestMaterial->arrival_status;
        $arrivalStatus = 'Waiting Material';

        if ((float) $validated['arrived_qty'] > 0 && (float) $validated['arrived_qty'] < (float) $requestMaterial->qty) {
            $arrivalStatus = 'Partial Arrived';
        }

        if ((float) $validated['arrived_qty'] >= (float) $requestMaterial->qty) {
            $arrivalStatus = 'Complete';
        }

        $requestMaterial->update([
            'arrived_qty' => $validated['arrived_qty'],
            'arrival_status' => $arrivalStatus,
            'arrival_date' => $validated['arrival_date'] ?? null,
            'arrival_note' => $validated['arrival_note'] ?? null,
        ]);

        $this->syncRequestMaterialStatus($requestMaterial->ieRequest);

        RequestActivity::record(
            $requestMaterial->ie_request_id,
            'Material Arrival',
            'Material Arrival Updated',
            $oldArrivalStatus,
            $arrivalStatus,
            $requestMaterial->material_name
        );

        return redirect()->back()->with('success', 'Data kedatangan material berhasil diperbarui.');
    }

    public function complete(IeRequest $ieRequest)
    {
        if ($this->requestClosed($ieRequest)) {
            return redirect()->back()->with('error', 'Material complete tidak bisa diproses karena request sudah Closed.');
        }

        if (!$ieRequest->purchaseRequest || $ieRequest->purchaseRequest->pr_status !== 'PO Created') {
            return redirect()
                ->back()
                ->with('error', 'Material complete hanya bisa dicek setelah PR status PO Created.');
        }

        if (!$ieRequest->materials()->exists()) {
            return redirect()->back()->with('error', 'Request ini belum memiliki material.');
        }

        if ($ieRequest->materials()->where('arrival_status', '!=', 'Complete')->exists()) {
            return redirect()->back()->with('error', 'Material belum lengkap.');
        }

        $this->syncRequestStatus($ieRequest, 'Material Complete', 'Material Arrival', 'All material complete');

        return redirect()->back()->with('success', 'Material sudah complete dan siap masuk Workshop Schedule.');
    }

    private function syncRequestMaterialStatus(?IeRequest $ieRequest): void
    {
        if (!$ieRequest || !$ieRequest->materials()->exists()) {
            return;
        }

        if (!$ieRequest->materials()->where('arrival_status', '!=', 'Complete')->exists()) {
            $this->syncRequestStatus($ieRequest, 'Material Complete', 'Material Arrival', 'All material complete');

            return;
        }

        if ($ieRequest->purchaseRequest?->pr_status === 'PO Created') {
            $this->syncRequestStatus($ieRequest, 'Waiting Material', 'Material Arrival', 'Material still waiting or partial');
        }
    }

    private function requestClosed(?IeRequest $ieRequest): bool
    {
        if (! $ieRequest) {
            return false;
        }

        $ieRequest->loadMissing('handover');

        return $ieRequest->status === RequestWorkflow::CLOSED
            || $ieRequest->handover?->handover_status === 'Received';
    }
}
