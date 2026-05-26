<?php

namespace App\Http\Controllers;

use App\Models\IeRequest;
use App\Models\PurchaseRequest;
use App\Models\RequestActivity;
use App\Models\SapApproval;
use App\Services\RequestWorkflow;
use Illuminate\Http\Request;

class BudgetPrController extends Controller
{
    public function index(Request $request)
    {
        $query = IeRequest::query()
            ->whereHas('materials')
            ->whereHas('sapApproval', function ($q) {
                $q->where('approval_status', SapApproval::SENT_TO_PURCHASING);
            })
            ->with(['purchaseRequest', 'sapApproval'])
            ->withSum('materials', 'total_price');

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('request_number', 'like', '%' . $search . '%')
                    ->orWhere('requester_name', 'like', '%' . $search . '%')
                    ->orWhere('department', 'like', '%' . $search . '%')
                    ->orWhere('line_area', 'like', '%' . $search . '%');
            });
        }

        if ($request->pr_status === 'No PR') {
            $query->doesntHave('purchaseRequest');
        }

        if (in_array($request->pr_status, ['Draft', 'Waiting Approval', 'Approved', 'Rejected', 'PO Created'])) {
            $query->whereHas('purchaseRequest', function ($q) use ($request) {
                $q->where('pr_status', $request->pr_status);
            });
        }

        $requests = $query->latest()->paginate(10)->withQueryString();

        return view('budget-pr.index', compact('requests'));
    }

    public function show(IeRequest $ieRequest)
    {
        if (!$ieRequest->materials()->exists()) {
            return redirect()
                ->back()
                ->with('error', 'Purchasing belum bisa diproses karena request ini belum punya material/BOM.');
        }

        if (! $this->readyForPurchasing($ieRequest)) {
            return redirect()
                ->back()
                ->with('error', 'PR belum bisa diproses sebelum No. PR SAP disetujui Atasan IE, Division Head, Director, dan dikirim ke Purchasing.');
        }

        $ieRequest->load([
            'purchaseRequest',
            'sapApproval',
        ]);

        return view('budget-pr.show', compact('ieRequest'));
    }

    public function poCreated(PurchaseRequest $purchaseRequest)
    {
        if ($this->requestClosed($purchaseRequest)) {
            return redirect()->back()->with('error', 'PR tidak bisa diubah karena request sudah Closed.');
        }

        if ($purchaseRequest->pr_status !== 'Approved') {
            return redirect()->back()->with('error', 'PO hanya bisa dibuat setelah PR Approved.');
        }

        $oldStatus = $purchaseRequest->pr_status;

        $purchaseRequest->update([
            'pr_status' => 'PO Created',
        ]);

        $this->syncRequestStatus($purchaseRequest->ieRequest, RequestWorkflow::WAITING_MATERIAL, 'Budget / PR', 'PO created');

        RequestActivity::record(
            $purchaseRequest->ie_request_id,
            'Budget / PR',
            'PO Created',
            $oldStatus,
            'PO Created'
        );

        return redirect()->back()->with('success', 'PR berhasil ditandai sebagai PO Created.');
    }

    private function requestClosed(PurchaseRequest $purchaseRequest): bool
    {
        $purchaseRequest->loadMissing('ieRequest.handover');

        return $purchaseRequest->ieRequest?->status === RequestWorkflow::CLOSED
            || $purchaseRequest->ieRequest?->handover?->handover_status === 'Received';
    }

    private function readyForPurchasing(IeRequest $ieRequest): bool
    {
        $ieRequest->loadMissing('sapApproval');

        return $ieRequest->sapApproval?->approval_status === SapApproval::SENT_TO_PURCHASING;
    }
}
