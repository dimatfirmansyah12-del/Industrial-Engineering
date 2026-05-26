<?php

namespace App\Http\Controllers;

use App\Models\IeRequest;
use App\Models\SapApproval;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $baseQuery = $this->filteredRequestQuery($request);

        $totalRequest = (clone $baseQuery)->count();

        $waitingMemoApproval = (clone $baseQuery)
            ->where('memo_status', 'Waiting Approval')
            ->count();

        $drawingProgress = (clone $baseQuery)
            ->whereIn('drawing_status', ['On Progress', 'Revision'])
            ->count();

        $waitingSapInput = (clone $baseQuery)
            ->where('drawing_status', 'Done')
            ->where('bom_status', IeRequest::BOM_SUBMITTED)
            ->whereHas('materials')
            ->whereDoesntHave('purchaseRequest')
            ->where(function ($query) {
                $query->whereDoesntHave('sapApproval')
                    ->orWhereHas('sapApproval', function ($approvalQuery) {
                        $approvalQuery->where('approval_status', SapApproval::WAITING_SAP_INPUT);
                    });
            })
            ->count();

        $waitingInternalApproval = (clone $baseQuery)
            ->whereHas('sapApproval', function ($query) {
                $query->whereIn('approval_status', [
                    SapApproval::WAITING_SECTION_HEAD,
                    SapApproval::WAITING_DIVISION_HEAD,
                    SapApproval::WAITING_DIRECTOR,
                ]);
            })
            ->count();

        $waitingMaterial = (clone $baseQuery)
            ->whereHas('purchaseRequest', function ($query) {
                $query->where('pr_status', 'PO Created');
            })
            ->whereHas('materials', function ($query) {
                $query->whereIn('arrival_status', ['Waiting Material', 'Partial Arrived']);
            })
            ->count();

        $workshopProgress = (clone $baseQuery)
            ->whereHas('workshopSchedule', function ($query) {
                $query->whereIn('progress_status', ['On Progress', 'Hold', 'Rework']);
            })
            ->count();

        $finalCheckCount = (clone $baseQuery)
            ->whereHas('finalCheck', function ($query) {
                $query->whereIn('check_status', ['Waiting Check', 'Checking', 'Need Rework']);
            })
            ->count();

        $waitingHandover = (clone $baseQuery)
            ->where(function ($query) {
                $query->whereHas('handover', function ($handoverQuery) {
                    $handoverQuery->whereIn('handover_status', ['Waiting Handover', 'Handover Process']);
                })->orWhere(function ($noHandoverQuery) {
                    $noHandoverQuery->whereDoesntHave('handover')
                        ->whereHas('finalCheck', function ($checkQuery) {
                            $checkQuery->where('check_status', 'Passed')
                                ->where('result_status', 'OK');
                        });
                });
            })
            ->count();

        $delay = (clone $baseQuery)
            ->whereNotIn('status', ['Completed', 'Closed'])
            ->whereNotNull('target_date')
            ->whereDate('target_date', '<', Carbon::today())
            ->count();

        $dueSoon = (clone $baseQuery)
            ->whereNotIn('status', ['Completed', 'Closed'])
            ->whereNotNull('target_date')
            ->whereDate('target_date', '>=', Carbon::today())
            ->whereDate('target_date', '<=', Carbon::today()->copy()->addDays(3))
            ->count();

        $closed = (clone $baseQuery)
            ->where('status', 'Closed')
            ->count();

        $delayRequests = (clone $baseQuery)
            ->whereNotIn('status', ['Completed', 'Closed'])
            ->whereNotNull('target_date')
            ->whereDate('target_date', '<', Carbon::today())
            ->orderBy('target_date')
            ->take(5)
            ->get();

        $dueSoonRequests = (clone $baseQuery)
            ->whereNotIn('status', ['Completed', 'Closed'])
            ->whereNotNull('target_date')
            ->whereDate('target_date', '>=', Carbon::today())
            ->whereDate('target_date', '<=', Carbon::today()->copy()->addDays(3))
            ->orderBy('target_date')
            ->take(5)
            ->get();

        $urgentRequests = (clone $baseQuery)
            ->where('priority', 'Urgent')
            ->where('status', '!=', 'Closed')
            ->latest()
            ->take(5)
            ->get();

        $waitingMemoRequests = (clone $baseQuery)
            ->where('memo_status', 'Waiting Approval')
            ->latest()
            ->take(5)
            ->get();

        $waitingMaterialRequests = (clone $baseQuery)
            ->where('status', '!=', 'Closed')
            ->whereHas('purchaseRequest', function ($query) {
                $query->where('pr_status', 'PO Created');
            })
            ->whereHas('materials', function ($query) {
                $query->whereIn('arrival_status', ['Waiting Material', 'Partial Arrived']);
            })
            ->latest()
            ->take(5)
            ->get();

        $waitingFinalCheckRequests = (clone $baseQuery)
            ->whereHas('workshopSchedule', function ($scheduleQuery) {
                $scheduleQuery->where('progress_status', 'Done');
            })
            ->where(function ($query) {
                $query->whereDoesntHave('finalCheck')
                    ->orWhereHas('finalCheck', function ($checkQuery) {
                        $checkQuery->whereIn('check_status', ['Waiting Check', 'Checking', 'Need Rework']);
                    });
            })
            ->latest()
            ->take(5)
            ->get();

        $waitingHandoverRequests = (clone $baseQuery)
            ->whereHas('finalCheck', function ($checkQuery) {
                $checkQuery->where('check_status', 'Passed')
                    ->where('result_status', 'OK');
            })
            ->where(function ($query) {
                $query->whereDoesntHave('handover')
                    ->orWhereHas('handover', function ($handoverQuery) {
                        $handoverQuery->whereIn('handover_status', ['Waiting Handover', 'Handover Process']);
                    });
            })
            ->latest()
            ->take(5)
            ->get();

        $todayMemoWork = (clone $baseQuery)
            ->where('memo_status', 'Waiting Approval')
            ->latest()
            ->take(4)
            ->get();

        $todayDrawingWork = (clone $baseQuery)
            ->whereIn('drawing_status', ['On Progress', 'Revision'])
            ->latest()
            ->take(4)
            ->get();

        $todayMaterialWork = (clone $baseQuery)
            ->where('status', '!=', 'Closed')
            ->whereHas('purchaseRequest', function ($query) {
                $query->where('pr_status', 'PO Created');
            })
            ->whereHas('materials', function ($query) {
                $query->whereIn('arrival_status', ['Waiting Material', 'Partial Arrived']);
            })
            ->latest()
            ->take(4)
            ->get();

        $todayWorkshopWork = (clone $baseQuery)
            ->with('workshopSchedule')
            ->whereHas('workshopSchedule', function ($query) {
                $query->where(function ($dateQuery) {
                    $dateQuery->whereDate('planned_start_date', '<=', Carbon::today())
                        ->whereDate('planned_finish_date', '>=', Carbon::today());
                })->orWhereIn('progress_status', ['On Progress', 'Hold', 'Rework']);
            })
            ->latest()
            ->take(4)
            ->get();

        $todayFinalCheckWork = (clone $baseQuery)
            ->whereHas('workshopSchedule', function ($scheduleQuery) {
                $scheduleQuery->where('progress_status', 'Done');
            })
            ->where(function ($query) {
                $query->whereDoesntHave('finalCheck')
                    ->orWhereHas('finalCheck', function ($checkQuery) {
                        $checkQuery->whereIn('check_status', ['Waiting Check', 'Checking', 'Need Rework']);
                    });
            })
            ->latest()
            ->take(4)
            ->get();

        $todayHandoverWork = (clone $baseQuery)
            ->whereHas('finalCheck', function ($checkQuery) {
                $checkQuery->where('check_status', 'Passed')
                    ->where('result_status', 'OK');
            })
            ->where(function ($query) {
                $query->whereDoesntHave('handover')
                    ->orWhereHas('handover', function ($handoverQuery) {
                        $handoverQuery->whereIn('handover_status', ['Waiting Handover', 'Handover Process']);
                    });
            })
            ->latest()
            ->take(4)
            ->get();

        $latestRequests = (clone $baseQuery)
            ->latest()
            ->take(5)
            ->get();

        $pipelineRequests = (clone $baseQuery)
            ->with(['materials', 'sapApproval', 'purchaseRequest', 'workshopSchedule', 'finalCheck', 'handover'])
            ->where('status', '!=', 'Closed')
            ->latest()
            ->take(10)
            ->get();

        $pipelineRequests->each(function ($ieRequest) {
            $ieRequest->material_arrival_overall_status = $this->getMaterialArrivalStatus($ieRequest);
        });

        $statusData = (clone $baseQuery)
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->orderBy('status')
            ->get();

        $statusLabels = $statusData->pluck('status');
        $statusTotals = $statusData->pluck('total');

        $departmentData = (clone $baseQuery)
            ->selectRaw('department, COUNT(*) as total')
            ->groupBy('department')
            ->orderBy('department')
            ->get();

        $departmentLabels = $departmentData->pluck('department');
        $departmentTotals = $departmentData->pluck('total');

        $priorityData = (clone $baseQuery)
            ->selectRaw('priority, COUNT(*) as total')
            ->groupBy('priority')
            ->orderBy('priority')
            ->get();

        $priorityLabels = $priorityData->pluck('priority');
        $priorityTotals = $priorityData->pluck('total');

        $monthQuery = $this->filteredRequestQuery($request)
            ->selectRaw('MONTH(request_date) as month, COUNT(*) as total')
            ->whereNotNull('request_date');

        if (!$request->filled('year') && !$request->filled('month')) {
            $monthQuery->whereYear('request_date', now()->year);
        }

        $monthData = $monthQuery
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        $monthLabels = [
            'Jan',
            'Feb',
            'Mar',
            'Apr',
            'May',
            'Jun',
            'Jul',
            'Aug',
            'Sep',
            'Oct',
            'Nov',
            'Dec',
        ];

        $monthTotals = collect(range(1, 12))
            ->map(function ($month) use ($monthData) {
                return (int) ($monthData[$month] ?? 0);
            })
            ->values();

        $departments = IeRequest::query()
            ->when($request->user()?->role === 'customer', function ($query) use ($request) {
                $query->where('user_id', $request->user()->id);
            })
            ->whereNotNull('department')
            ->where('department', '!=', '')
            ->distinct()
            ->orderBy('department')
            ->pluck('department');

        $years = IeRequest::query()
            ->when($request->user()?->role === 'customer', function ($query) use ($request) {
                $query->where('user_id', $request->user()->id);
            })
            ->whereNotNull('request_date')
            ->selectRaw('YEAR(request_date) as year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year');

        if (!$years->contains(now()->year)) {
            $years->prepend(now()->year);
        }

        return view('dashboard', compact(
            'totalRequest',
            'waitingMemoApproval',
            'drawingProgress',
            'waitingSapInput',
            'waitingInternalApproval',
            'waitingMaterial',
            'workshopProgress',
            'finalCheckCount',
            'waitingHandover',
            'delay',
            'dueSoon',
            'closed',
            'delayRequests',
            'dueSoonRequests',
            'urgentRequests',
            'waitingMemoRequests',
            'waitingMaterialRequests',
            'waitingFinalCheckRequests',
            'waitingHandoverRequests',
            'todayMemoWork',
            'todayDrawingWork',
            'todayMaterialWork',
            'todayWorkshopWork',
            'todayFinalCheckWork',
            'todayHandoverWork',
            'latestRequests',
            'pipelineRequests',
            'statusLabels',
            'statusTotals',
            'departmentLabels',
            'departmentTotals',
            'priorityLabels',
            'priorityTotals',
            'monthLabels',
            'monthTotals',
            'departments',
            'years'
        ));
    }

    private function filteredRequestQuery(Request $request): Builder
    {
        return IeRequest::query()
            ->when($request->user()?->role === 'customer', function ($query) use ($request) {
                $query->where('user_id', $request->user()->id);
            })
            ->when($request->filled('month'), function ($query) use ($request) {
                $query->whereMonth('request_date', $request->month);
            })
            ->when($request->filled('year'), function ($query) use ($request) {
                $query->whereYear('request_date', $request->year);
            })
            ->when($request->filled('department'), function ($query) use ($request) {
                $query->where('department', $request->department);
            });
    }

    private function getMaterialArrivalStatus(IeRequest $ieRequest): string
    {
        if ($ieRequest->materials->isEmpty()) {
            return 'No Material';
        }

        if (! $ieRequest->purchaseRequest || $ieRequest->purchaseRequest->pr_status !== 'PO Created') {
            return 'Not Ordered';
        }

        if ($ieRequest->materials->every(fn ($material) => $material->arrival_status === 'Complete')) {
            return 'Complete';
        }

        if ($ieRequest->materials->contains(fn ($material) => (float) $material->arrived_qty > 0)) {
            return 'Partial Arrived';
        }

        return 'Waiting Material';
    }
}
