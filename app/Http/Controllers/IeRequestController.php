<?php

namespace App\Http\Controllers;


use App\Models\IeRequest;
use App\Models\Department;
use App\Models\LineArea;
use App\Models\MemoApprovalStep;
use App\Models\RequestActivity;
use App\Models\User;
use App\Services\RequestWorkflow;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class IeRequestController extends Controller
{
    public function index(Request $request)
{
    $query = IeRequest::query()
        ->with('user');

    $this->scopeCustomerRequests($query);

    if ($request->filled('search')) {
        $search = $request->search;

        $query->where(function ($q) use ($search) {
            $q->where('request_number', 'like', '%' . $search . '%')
                ->orWhere('requester_name', 'like', '%' . $search . '%')
                ->orWhere('department', 'like', '%' . $search . '%')
                ->orWhere('line_area', 'like', '%' . $search . '%')
                ->orWhere('request_type', 'like', '%' . $search . '%');
        });
    }

    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    if ($request->filled('priority')) {
        $query->where('priority', $request->priority);
    }

    $this->applyDeadlineFilter($query, $request->deadline);

    $requests = $query->latest()->paginate(10)->withQueryString();

    return view('ie-requests.index', compact('requests'));
}

    public function kanban(Request $request)
    {
        $statuses = RequestWorkflow::STATUSES;

        $query = IeRequest::query()
            ->with([
                'user',
                'materials',
                'sapApproval',
                'purchaseRequest',
                'workshopSchedule',
                'finalCheck',
                'handover',
            ])
            ->whereIn('status', $statuses);

        $this->scopeCustomerRequests($query);

        if ($request->filled('department')) {
            $query->where('department', $request->department);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        $this->applyDeadlineFilter($query, $request->deadline);

        $requests = $query->latest()->get()->groupBy('status');

        $departments = IeRequest::query()
            ->when(Auth::user()?->role === 'customer', function ($departmentQuery) {
                $departmentQuery->where('user_id', Auth::id());
            })
            ->whereNotNull('department')
            ->where('department', '!=', '')
            ->distinct()
            ->orderBy('department')
            ->pluck('department');

        return view('ie-requests.kanban', compact('statuses', 'requests', 'departments'));
    }

    public function create()
{
    $year = date('Y');

    $lastRequest = IeRequest::whereYear('created_at', $year)
        ->orderBy('id', 'desc')
        ->first();

    if ($lastRequest) {
        $lastNumber = (int) substr($lastRequest->request_number, -4);
        $newNumber = $lastNumber + 1;
    } else {
        $newNumber = 1;
    }

    $requestNumber = 'IE-' . $year . '-' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);

    $departments = Department::where('status', 'Active')
        ->orderBy('name')
        ->get();

    $lineAreas = LineArea::where('status', 'Active')
        ->orderBy('name')
        ->get();

    $approvers = $this->approverOptions();

    return view('ie-requests.create', compact('requestNumber', 'departments', 'lineAreas', 'approvers'));
}

    public function store(Request $request)
    {
        $validated = $request->validate([
            'request_number' => 'required|unique:ie_requests,request_number',
            'request_date' => 'required|date',
            'requester_name' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'line_area' => 'nullable|string|max:255',
            'request_type' => 'required|string|max:255',
            'request_qty' => 'required|integer|min:1',
            'description' => 'required|string',
            'priority' => ['required', Rule::in(['Low', 'Medium', 'High', 'Urgent'])],
            'target_date' => 'nullable|date',
            'pic_drafter' => 'nullable|string|max:255',
            'pic_workshop' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'memo_file' => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120',
            'drawing_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png,dwg,dxf|max:10240',
            'approver_user_ids' => 'required|array|min:1',
            'approver_user_ids.*' => 'required|integer|distinct|exists:users,id',
        ]);

        $this->validateApprovers($validated['approver_user_ids']);

        if ($request->hasFile('memo_file')) {
        $validated['memo_file'] = $request->file('memo_file')->store('ie-requests/memo', 'public');
    }

    if ($request->hasFile('drawing_file')) {
        $validated['drawing_file'] = $request->file('drawing_file')->store('ie-requests/drawing', 'public');
    }

        $validated['status'] = RequestWorkflow::REQUEST_SUBMITTED;
        $validated['user_id'] = Auth::id();

        $approverUserIds = $validated['approver_user_ids'];
        unset($validated['approver_user_ids']);

        $ieRequest = IeRequest::create($validated);

        $this->createMemoApprovalSteps($ieRequest, $approverUserIds);

        RequestActivity::record(
            $ieRequest->id,
            'Request',
            'Created Request',
            null,
            $ieRequest->request_number
        );

        return redirect()->route('ie-requests.index')
            ->with('success', 'Request berhasil ditambahkan.');
    }

    public function show(IeRequest $ieRequest)
    {
        $this->authorizeCustomerOwner($ieRequest);

        $ieRequest->load([
            'user',
            'memoApprovalSteps.approver',
            'materials',
            'sapApproval',
            'purchaseRequest',
            'workshopSchedule',
            'finalCheck',
            'handover',
            'comments' => function ($query) {
                $query->with('user')->latest();
            },
            'activities' => function ($query) {
                $query->with('user')->latest()->take(20);
            },
        ]);

        return view('ie-requests.show', compact('ieRequest'));
    }

    public function print(IeRequest $ieRequest)
    {
        $this->authorizeCustomerOwner($ieRequest);

        $ieRequest->load([
            'user',
            'memoApprovalSteps.approver',
            'materials',
            'sapApproval',
            'purchaseRequest',
            'workshopSchedule',
            'finalCheck',
            'handover',
            'comments.user',
            'activities.user',
        ]);

        return view('ie-requests.print', compact('ieRequest'));
    }

    public function edit(IeRequest $ieRequest)
{
    $this->authorizeCustomerOwner($ieRequest);

    $departments = Department::where('status', 'Active')
        ->orderBy('name')
        ->get();

    $lineAreas = LineArea::where('status', 'Active')
        ->orderBy('name')
        ->get();

    $ieRequest->load('memoApprovalSteps.approver');
    $approvers = $this->approverOptions();

    return view('ie-requests.edit', compact('ieRequest', 'departments', 'lineAreas', 'approvers'));
}   

    public function update(Request $request, IeRequest $ieRequest)
    {
        $this->authorizeCustomerOwner($ieRequest);

        $validated = $request->validate([
            'request_number' => 'required|unique:ie_requests,request_number,' . $ieRequest->id,
            'request_date' => 'required|date',
            'requester_name' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'line_area' => 'nullable|string|max:255',
            'request_type' => 'required|string|max:255',
            'request_qty' => 'required|integer|min:1',
            'description' => 'required|string',
            'priority' => ['required', Rule::in(['Low', 'Medium', 'High', 'Urgent'])],
            'target_date' => 'nullable|date',
            'pic_drafter' => 'nullable|string|max:255',
            'pic_workshop' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'memo_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120',
            'drawing_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png,dwg,dxf|max:10240',
        ]);

        if ($request->hasFile('memo_file')) {
        $validated['memo_file'] = $request->file('memo_file')->store('ie-requests/memo', 'public');
    }

    if ($request->hasFile('drawing_file')) {
        $validated['drawing_file'] = $request->file('drawing_file')->store('ie-requests/drawing', 'public');
    }

        if ($request->hasFile('memo_file') && $ieRequest->memo_status === 'Rejected') {
            $validated['memo_status'] = 'Waiting Approval';
            $validated['memo_approved_by'] = null;
            $validated['memo_approved_at'] = null;
            $validated['memo_rejected_reason'] = null;
            $validated['memo_approval_note'] = null;
        }

        $ieRequest->update($validated);

        if ($request->hasFile('memo_file') && $ieRequest->memo_status === 'Waiting Approval') {
            $this->resetMemoApprovalSteps($ieRequest);
        }

        RequestActivity::record(
            $ieRequest->id,
            'Request',
            'Updated Request',
            null,
            null,
            'Request data updated'
        );

        return redirect()->route('ie-requests.index')
            ->with('success', 'Request berhasil diperbarui.');
    }
public function updateStatus(Request $request, IeRequest $ieRequest)
{
    $oldStatus = $ieRequest->status;

    $validated = $request->validate([
        'status' => ['required', Rule::in(RequestWorkflow::STATUSES)],
    ]);

    [$allowed, $message] = app(RequestWorkflow::class)->canManuallySetStatus($ieRequest, $validated['status']);

    if (! $allowed) {
        return redirect()->back()->with('error', $message);
    }

    $ieRequest->update([
        'status' => $validated['status'],
    ]);

    RequestActivity::record(
        $ieRequest->id,
        'Request',
        'Status Updated',
        $oldStatus,
        $validated['status']
    );

    return redirect()->back()->with('success', 'Status request berhasil diperbarui.');
}
public function updatePriority(Request $request, IeRequest $ieRequest)
{
    $oldPriority = $ieRequest->priority;

    $validated = $request->validate([
        'priority' => ['required', Rule::in(['Low', 'Medium', 'High', 'Urgent'])],
    ]);

    $ieRequest->update([
        'priority' => $validated['priority'],
    ]);

    RequestActivity::record(
        $ieRequest->id,
        'Request',
        'Priority Updated',
        $oldPriority,
        $validated['priority']
    );

    return redirect()->back()->with('success', 'Priority request berhasil diperbarui.');
}

public function report(Request $request)
{
    $query = IeRequest::query()
        ->with('user');

    $this->scopeCustomerRequests($query);

    if ($request->filled('start_date')) {
        $query->whereDate('request_date', '>=', $request->start_date);
    }

    if ($request->filled('end_date')) {
        $query->whereDate('request_date', '<=', $request->end_date);
    }

    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    if ($request->filled('priority')) {
        $query->where('priority', $request->priority);
    }

    if ($request->filled('department')) {
        $query->where('department', 'like', '%' . $request->department . '%');
    }

    $this->applyDeadlineFilter($query, $request->deadline);

    $requests = $query->latest()->paginate(10)->withQueryString();

    $totalRequest = (clone $query)->count();
    $completed = (clone $query)->whereIn('status', ['Completed', 'Closed'])->count();
    $waitingMaterial = (clone $query)->where('status', 'Waiting Material')->count();
    $urgent = (clone $query)->where('priority', 'Urgent')->count();

    return view('reports.index', compact(
        'requests',
        'totalRequest',
        'completed',
        'waitingMaterial',
        'urgent'
    ));
}

public function export(Request $request)
{
    $query = IeRequest::query()
        ->with('user');

    $this->scopeCustomerRequests($query);

    if ($request->filled('start_date')) {
    $query->whereDate('request_date', '>=', $request->start_date);
}

if ($request->filled('end_date')) {
    $query->whereDate('request_date', '<=', $request->end_date);
}

if ($request->filled('department')) {
    $query->where('department', 'like', '%' . $request->department . '%');
}

    $this->applyDeadlineFilter($query, $request->deadline);

    if ($request->filled('search')) {
        $search = $request->search;

        $query->where(function ($q) use ($search) {
            $q->where('request_number', 'like', '%' . $search . '%')
                ->orWhere('requester_name', 'like', '%' . $search . '%')
                ->orWhere('department', 'like', '%' . $search . '%')
                ->orWhere('line_area', 'like', '%' . $search . '%')
                ->orWhere('request_type', 'like', '%' . $search . '%');
        });
    }

    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    if ($request->filled('priority')) {
        $query->where('priority', $request->priority);
    }

    $requests = $query->latest()->get();

    $fileName = 'report-ie-requests-' . date('Y-m-d-H-i-s') . '.csv';

    $headers = [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => "attachment; filename=\"$fileName\"",
    ];

    $callback = function () use ($requests) {
        $file = fopen('php://output', 'w');

        // Supaya Excel bisa membaca karakter Indonesia dengan baik
        fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

        fputcsv($file, [
            'No',
            'No Request',
            'Tanggal Request',
            'Requester',
            'Created By',
            'Department',
            'Line / Area',
            'Jenis Request',
            'Qty Request',
            'Priority',
            'Status',
            'Target Selesai',
            'Deadline Status',
            'Delay Days',
            'PIC Drafter',
            'PIC Workshop',
            'Deskripsi',
            'Catatan',
            'File Memo',
            'File Drawing',
            'Created At',
            'Updated At',
        ]);

        foreach ($requests as $index => $request) {
            fputcsv($file, [
                $index + 1,
                $request->request_number,
                $request->request_date,
                $request->requester_name,
                $request->user?->name ?? '-',
                $request->department,
                $request->line_area,
                $request->request_type,
                $request->request_qty ?? 1,
                $request->priority,
                $request->status,
                $request->target_date,
                $this->deadlineStatusText($request),
                $request->is_delay ? $request->delay_days : 0,
                $request->pic_drafter,
                $request->pic_workshop,
                $request->description,
                $request->notes,
                $request->memo_file ? asset('storage/' . $request->memo_file) : '',
                $request->drawing_file ? asset('storage/' . $request->drawing_file) : '',
                $request->created_at,
                $request->updated_at,
            ]);
        }

        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}
    public function destroy(IeRequest $ieRequest)
    {
        if (Auth::user()?->role !== 'admin') {
            abort(403);
        }

        $ieRequest->delete();

        return redirect()->route('ie-requests.index')
            ->with('success', 'Request berhasil dihapus.');
    }

    private function scopeCustomerRequests($query): void
    {
        if (Auth::user()?->role === 'customer') {
            $query->where('user_id', Auth::id());
        }
    }

    private function authorizeCustomerOwner(IeRequest $ieRequest): void
    {
        if (Auth::user()?->role === 'customer' && (int) $ieRequest->user_id !== Auth::id()) {
            abort(403);
        }
    }

    private function applyDeadlineFilter($query, ?string $deadline): void
    {
        if ($deadline === 'delay') {
            $query->whereNotIn('status', ['Completed', 'Closed'])
                ->whereNotNull('target_date')
                ->whereDate('target_date', '<', Carbon::today());
        }

        if ($deadline === 'due_soon') {
            $query->whereNotIn('status', ['Completed', 'Closed'])
                ->whereNotNull('target_date')
                ->whereDate('target_date', '>=', Carbon::today())
                ->whereDate('target_date', '<=', Carbon::today()->copy()->addDays(3));
        }
    }

    private function deadlineStatusText(IeRequest $ieRequest): string
    {
        if (! $ieRequest->target_date) {
            return 'No Target';
        }

        if ($ieRequest->is_delay) {
            return 'Delay ' . $ieRequest->delay_days . ' hari';
        }

        if ($ieRequest->is_due_soon) {
            return 'Due Soon ' . $ieRequest->due_soon_days . ' hari lagi';
        }

        return 'On Track';
    }

    private function approverOptions()
    {
        return User::query()
            ->whereIn('role', ['admin', 'approver'])
            ->orderByRaw("CASE WHEN role = 'admin' THEN 0 ELSE 1 END")
            ->orderBy('position')
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'role', 'position']);
    }

    private function validateApprovers(array $approverUserIds): void
    {
        $validCount = User::query()
            ->whereIn('id', $approverUserIds)
            ->whereIn('role', ['admin', 'approver'])
            ->count();

        if ($validCount !== count($approverUserIds)) {
            throw ValidationException::withMessages([
                'approver_user_ids' => 'Approver harus user dengan role admin atau approver.',
            ]);
        }
    }

    private function createMemoApprovalSteps(IeRequest $ieRequest, array $approverUserIds): void
    {
        $approvers = User::query()
            ->whereIn('id', $approverUserIds)
            ->get()
            ->keyBy('id');

        foreach (array_values($approverUserIds) as $index => $approverUserId) {
            $approver = $approvers->get((int) $approverUserId);

            MemoApprovalStep::create([
                'ie_request_id' => $ieRequest->id,
                'sequence' => $index + 1,
                'approval_label' => $approver?->position ?: ($approver?->role === 'admin' ? 'Admin IE' : 'Approver'),
                'approver_user_id' => $approverUserId,
                'status' => $index === 0 ? MemoApprovalStep::WAITING : MemoApprovalStep::PENDING,
            ]);
        }
    }

    private function resetMemoApprovalSteps(IeRequest $ieRequest): void
    {
        $steps = $ieRequest->memoApprovalSteps()->orderBy('sequence')->get();

        foreach ($steps as $index => $step) {
            $step->update([
                'status' => $index === 0 ? MemoApprovalStep::WAITING : MemoApprovalStep::PENDING,
                'approved_at' => null,
                'rejected_at' => null,
                'note' => null,
                'rejected_reason' => null,
            ]);
        }

        RequestActivity::record(
            $ieRequest->id,
            'Memo Approval',
            'Memo Approval Reset',
            'Rejected',
            'Waiting Approval',
            'Memo file updated after reject'
        );
    }
}
