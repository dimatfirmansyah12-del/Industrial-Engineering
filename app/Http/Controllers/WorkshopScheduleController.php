<?php

namespace App\Http\Controllers;

use App\Models\IeRequest;
use App\Models\RequestActivity;
use App\Models\WorkshopPerson;
use App\Models\WorkshopSchedule;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class WorkshopScheduleController extends Controller
{
    public function index(Request $request)
    {
        $query = IeRequest::query()
            ->whereHas('materials')
            ->whereDoesntHave('materials', function ($q) {
                $q->where('arrival_status', '!=', 'Complete');
            })
            ->with(['workshopSchedule', 'purchaseRequest'])
            ->withCount([
                'materials',
                'materials as complete_materials_count' => function ($q) {
                    $q->where('arrival_status', 'Complete');
                },
            ]);

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

        if ($request->schedule_status === 'No Schedule') {
            $query->doesntHave('workshopSchedule');
        }

        if (in_array($request->schedule_status, ['Scheduled', 'Rescheduled', 'Cancelled', 'Ready to Work', 'In Progress', 'Finished'])) {
            $query->whereHas('workshopSchedule', function ($q) use ($request) {
                $q->where('schedule_status', $request->schedule_status);
            });
        }

        if ($request->filled('pic_workshop')) {
            $query->whereHas('workshopSchedule', function ($q) use ($request) {
                $q->where('pic_workshop', $request->pic_workshop);
            });
        }

        if ($request->filled('planned_date')) {
            $query->whereHas('workshopSchedule', function ($q) use ($request) {
                $q->whereDate('planned_start_date', '<=', $request->planned_date)
                    ->whereDate('planned_finish_date', '>=', $request->planned_date);
            });
        }

        $requests = $query->latest()->paginate(10)->withQueryString();
        $workshopPeople = $this->workshopPeople();
        $workOptions = $this->workOptions();

        return view('workshop-schedules.index', compact('requests', 'workshopPeople', 'workOptions'));
    }

    public function show(IeRequest $ieRequest)
    {
        if (!$this->materialsComplete($ieRequest)) {
            return redirect()
                ->back()
                ->with('error', 'Workshop schedule belum bisa dibuat karena material belum lengkap.');
        }

        $ieRequest->load(['materials', 'purchaseRequest', 'workshopSchedule']);

        $totalMaterial = $ieRequest->materials->count();
        $completeMaterial = $ieRequest->materials->where('arrival_status', 'Complete')->count();
        $workshopPeople = $this->workshopPeople();

        return view('workshop-schedules.show', compact('ieRequest', 'totalMaterial', 'completeMaterial', 'workshopPeople'));
    }

    public function store(Request $request, IeRequest $ieRequest)
    {
        if (!$this->materialsComplete($ieRequest)) {
            return redirect()
                ->back()
                ->with('error', 'Workshop schedule belum bisa dibuat karena material belum lengkap.');
        }

        if ($ieRequest->workshopSchedule()->exists()) {
            return redirect()
                ->back()
                ->with('error', 'Request ini sudah memiliki workshop schedule.');
        }

        $validated = $this->validateSchedule($request);
        $teamMembers = $this->prepareTvTeamMembers($request);

        $schedule = $ieRequest->workshopSchedule()->create([
            'schedule_number' => $this->generateScheduleNumber(),
            'planned_start_date' => $validated['planned_start_date'],
            'planned_finish_date' => $validated['planned_finish_date'],
            'pic_workshop' => $validated['pic_workshop'],
            'estimated_duration' => $this->calculateDurationDays($validated['planned_start_date'], $validated['planned_finish_date']),
            'schedule_note' => $validated['schedule_note'] ?? null,
            'tv_team_members' => $teamMembers,
            'schedule_status' => 'Scheduled',
        ]);

        $this->syncRequestStatus($ieRequest, 'Workshop Scheduled', 'Workshop Schedule', 'Workshop schedule created');

        RequestActivity::record(
            $ieRequest->id,
            'Workshop Schedule',
            'Workshop Schedule Created',
            null,
            $schedule->schedule_number
        );

        return redirect()->back()->with('success', 'Workshop schedule berhasil dibuat.');
    }

    public function update(Request $request, WorkshopSchedule $workshopSchedule)
    {
        if (in_array($workshopSchedule->schedule_status, ['Cancelled', 'Finished'], true)) {
            return redirect()->back()->with('error', 'Workshop schedule yang Cancelled atau Finished tidak bisa diubah.');
        }

        $validated = $this->validateSchedule($request);
        $teamMembers = $this->prepareTvTeamMembers($request, $workshopSchedule->tv_team_members ?? []);

        $workshopSchedule->update([
            'planned_start_date' => $validated['planned_start_date'],
            'planned_finish_date' => $validated['planned_finish_date'],
            'pic_workshop' => $validated['pic_workshop'],
            'estimated_duration' => $this->calculateDurationDays($validated['planned_start_date'], $validated['planned_finish_date']),
            'schedule_note' => $validated['schedule_note'] ?? null,
            'tv_team_members' => $teamMembers,
        ]);

        return redirect()->back()->with('success', 'Workshop schedule berhasil diperbarui.');
    }

    public function ready(WorkshopSchedule $workshopSchedule)
    {
        if (! in_array($workshopSchedule->schedule_status, ['Scheduled', 'Rescheduled'], true)) {
            return redirect()->back()->with('error', 'Workshop hanya bisa dibuat Ready dari status Scheduled atau Rescheduled.');
        }

        $oldStatus = $workshopSchedule->schedule_status;

        $workshopSchedule->update([
            'schedule_status' => 'Ready to Work',
        ]);

        $this->syncRequestStatus($workshopSchedule->ieRequest, 'Workshop Scheduled', 'Workshop Schedule', 'Workshop ready to work');

        RequestActivity::record(
            $workshopSchedule->ie_request_id,
            'Workshop Schedule',
            'Workshop Ready',
            $oldStatus,
            'Ready to Work'
        );

        return redirect()->back()->with('success', 'Workshop schedule siap dikerjakan.');
    }

    public function reschedule(Request $request, WorkshopSchedule $workshopSchedule)
    {
        if (in_array($workshopSchedule->schedule_status, ['Cancelled', 'Finished'], true)) {
            return redirect()->back()->with('error', 'Workshop schedule yang Cancelled atau Finished tidak bisa di-reschedule.');
        }

        $validated = $request->validate([
            'planned_start_date' => 'required|date',
            'planned_finish_date' => 'required|date|after_or_equal:planned_start_date',
            'reschedule_reason' => 'required|string',
        ], [
            'reschedule_reason.required' => 'Alasan reschedule wajib diisi.',
        ]);

        $oldStatus = $workshopSchedule->schedule_status;

        $workshopSchedule->update([
            'planned_start_date' => $validated['planned_start_date'],
            'planned_finish_date' => $validated['planned_finish_date'],
            'estimated_duration' => $this->calculateDurationDays($validated['planned_start_date'], $validated['planned_finish_date']),
            'schedule_status' => 'Rescheduled',
            'reschedule_reason' => $validated['reschedule_reason'],
        ]);

        RequestActivity::record(
            $workshopSchedule->ie_request_id,
            'Workshop Schedule',
            'Workshop Rescheduled',
            $oldStatus,
            'Rescheduled',
            $validated['reschedule_reason']
        );

        return redirect()->back()->with('success', 'Workshop schedule berhasil di-reschedule.');
    }

    public function cancel(Request $request, WorkshopSchedule $workshopSchedule)
    {
        if (in_array($workshopSchedule->schedule_status, ['Cancelled', 'Finished'], true)) {
            return redirect()->back()->with('error', 'Workshop schedule yang Cancelled atau Finished tidak bisa dibatalkan.');
        }

        $validated = $request->validate([
            'reschedule_reason' => 'required|string',
        ], [
            'reschedule_reason.required' => 'Alasan cancel wajib diisi.',
        ]);

        $oldStatus = $workshopSchedule->schedule_status;

        $workshopSchedule->update([
            'schedule_status' => 'Cancelled',
            'reschedule_reason' => $validated['reschedule_reason'],
        ]);

        $this->syncRequestStatus($workshopSchedule->ieRequest, 'Material Complete', 'Workshop Schedule', 'Workshop schedule cancelled');

        RequestActivity::record(
            $workshopSchedule->ie_request_id,
            'Workshop Schedule',
            'Workshop Cancelled',
            $oldStatus,
            'Cancelled',
            $validated['reschedule_reason']
        );

        return redirect()->back()->with('success', 'Workshop schedule berhasil dibatalkan.');
    }

    public function updatePerson(Request $request, WorkshopPerson $workshopPerson)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'photo_position_x' => 'nullable|integer|min:0|max:100',
            'photo_position_y' => 'nullable|integer|min:0|max:100',
            'photo_zoom' => 'nullable|integer|min:80|max:160',
            'current_work' => 'nullable|string|max:500',
            'progress_percentage' => 'required|integer|min:0|max:100',
            'progress_note' => 'nullable|string|max:500',
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('workshop-people/photos', 'public');
        }

        $workshopPerson->update([
            'name' => $validated['name'],
            'photo' => $validated['photo'] ?? $workshopPerson->photo,
            'photo_position_x' => $validated['photo_position_x'] ?? $workshopPerson->photo_position_x,
            'photo_position_y' => $validated['photo_position_y'] ?? $workshopPerson->photo_position_y,
            'photo_zoom' => $validated['photo_zoom'] ?? $workshopPerson->photo_zoom,
            'current_work' => $validated['current_work'] ?? null,
            'progress_percentage' => $validated['progress_percentage'],
            'progress_note' => $validated['progress_note'] ?? null,
        ]);

        return redirect()->back()->with('success', 'Data workshop people berhasil diperbarui.');
    }

    protected function validateSchedule(Request $request): array
    {
        return $request->validate([
            'planned_start_date' => 'required|date',
            'planned_finish_date' => 'required|date|after_or_equal:planned_start_date',
            'pic_workshop' => 'required|string|max:255',
            'schedule_note' => 'nullable|string',
            'team_names' => 'nullable|array',
            'team_names.*' => 'nullable|string|max:255',
            'team_work_notes' => 'nullable|array',
            'team_work_notes.*' => 'nullable|string|max:255',
            'team_photos' => 'nullable|array',
            'team_photos.*' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);
    }

    protected function calculateDurationDays(string $plannedStartDate, string $plannedFinishDate): int
    {
        $startDate = Carbon::parse($plannedStartDate)->startOfDay();
        $finishDate = Carbon::parse($plannedFinishDate)->startOfDay();

        return (int) $startDate->diffInDays($finishDate) + 1;
    }

    protected function workshopPeople()
    {
        WorkshopPerson::ensureDefaults();

        return WorkshopPerson::query()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    protected function workOptions()
    {
        return WorkshopSchedule::query()
            ->with('ieRequest')
            ->whereIn('schedule_status', ['Scheduled', 'Rescheduled', 'Ready to Work', 'In Progress'])
            ->orderBy('planned_start_date')
            ->get()
            ->map(function (WorkshopSchedule $schedule) {
                $requestNumber = $schedule->ieRequest?->request_number ?? 'Request';
                $lineArea = $schedule->ieRequest?->line_area ?? $schedule->ieRequest?->department ?? 'Workshop';

                return trim($requestNumber . ' - ' . $lineArea . ' - ' . ($schedule->schedule_note ?? 'Pekerjaan workshop'));
            })
            ->unique()
            ->values();
    }

    protected function prepareTvTeamMembers(Request $request, array $existingMembers = []): array
    {
        $defaultNames = WorkshopPerson::defaultNames();
        $names = $request->input('team_names', $defaultNames);
        $workNotes = $request->input('team_work_notes', []);
        $members = [];

        foreach ($defaultNames as $index => $defaultName) {
            $existingMember = Arr::get($existingMembers, $index, []);
            $photoPath = Arr::get($existingMember, 'photo');

            if ($request->hasFile("team_photos.$index")) {
                $photoPath = $request->file("team_photos.$index")
                    ->store('workshop-team/photos', 'public');
            }

            $members[] = [
                'name' => trim($names[$index] ?? $defaultName) ?: $defaultName,
                'work_note' => trim($workNotes[$index] ?? ''),
                'photo' => $photoPath,
            ];
        }

        return $members;
    }

    protected function materialsComplete(IeRequest $ieRequest): bool
    {
        return $ieRequest->materials()->exists()
            && !$ieRequest->materials()->where('arrival_status', '!=', 'Complete')->exists();
    }

    protected function generateScheduleNumber(): string
    {
        $year = date('Y');

        $lastSchedule = WorkshopSchedule::where('schedule_number', 'like', 'WS-' . $year . '-%')
            ->orderByDesc('id')
            ->first();

        $nextNumber = 1;

        if ($lastSchedule) {
            $nextNumber = ((int) substr($lastSchedule->schedule_number, -4)) + 1;
        }

        return 'WS-' . $year . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }
}
