<?php

namespace App\Http\Controllers;

use App\Models\WorkshopSchedule;
use App\Models\RequestActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WorkshopProgressController extends Controller
{
    public function index(Request $request)
    {
        $query = WorkshopSchedule::query()
            ->whereIn('schedule_status', ['Ready to Work', 'In Progress', 'Finished'])
            ->with('ieRequest');

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('schedule_number', 'like', '%' . $search . '%')
                    ->orWhere('pic_workshop', 'like', '%' . $search . '%')
                    ->orWhereHas('ieRequest', function ($requestQuery) use ($search) {
                        $requestQuery->where('request_number', 'like', '%' . $search . '%')
                            ->orWhere('requester_name', 'like', '%' . $search . '%')
                            ->orWhere('department', 'like', '%' . $search . '%')
                            ->orWhere('line_area', 'like', '%' . $search . '%');
                    });
            });
        }

        if ($request->filled('progress_status')) {
            $query->where('progress_status', $request->progress_status);
        }

        $schedules = $query->latest()->paginate(10)->withQueryString();

        return view('workshop-progress.index', compact('schedules'));
    }

    public function show(WorkshopSchedule $workshopSchedule)
    {
        if (!in_array($workshopSchedule->schedule_status, ['Ready to Work', 'In Progress', 'Finished'])) {
            return redirect()
                ->back()
                ->with('error', 'Workshop progress hanya tersedia untuk schedule Ready to Work, In Progress, atau Finished.');
        }

        $workshopSchedule->load([
            'ieRequest',
            'progressLogs' => function ($query) {
                $query->with('user')->latest();
            },
        ]);

        return view('workshop-progress.show', compact('workshopSchedule'));
    }

    public function update(Request $request, WorkshopSchedule $workshopSchedule)
    {
        $workshopSchedule->loadMissing(['ieRequest.finalCheck', 'ieRequest.handover']);

        if (!in_array($workshopSchedule->schedule_status, ['Ready to Work', 'In Progress', 'Finished'], true)) {
            return redirect()
                ->back()
                ->with('error', 'Workshop progress hanya bisa di-update untuk schedule Ready to Work, In Progress, atau Finished.');
        }

        if ($workshopSchedule->ieRequest?->handover?->handover_status === 'Received') {
            return redirect()->back()->with('error', 'Workshop progress tidak bisa diubah karena handover sudah Received.');
        }

        if (
            $workshopSchedule->ieRequest?->finalCheck
            && $workshopSchedule->ieRequest->finalCheck->check_status === 'Passed'
            && $workshopSchedule->ieRequest->finalCheck->result_status === 'OK'
        ) {
            return redirect()->back()->with('error', 'Workshop progress tidak bisa diubah setelah final check Passed OK.');
        }

        $requestQty = max(1, (int) ($workshopSchedule->ieRequest?->request_qty ?? 1));
        $usesAutoQtyProgress = $requestQty > 1;
        $oldCompletedQty = min($requestQty, max(0, (int) ($workshopSchedule->completed_qty ?? 0)));
        $oldProgress = $workshopSchedule->progress_status . ' - ' . $workshopSchedule->progress_percentage . '%';

        if ($usesAutoQtyProgress) {
            $oldProgress .= ' - Qty ' . $oldCompletedQty . '/' . $requestQty;
        }

        $rules = [
            'progress_status' => 'required|string|in:Not Started,On Progress,Hold,Rework,Done',
            'progress_note' => 'nullable|string',
            'problem_note' => 'nullable|string',
            'photo_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ];

        if ($usesAutoQtyProgress) {
            $rules['completed_qty'] = 'required|integer|min:0|max:' . $requestQty;
            $rules['progress_percentage'] = 'nullable|integer|min:0|max:100';
        } else {
            $rules['progress_percentage'] = 'required|integer|min:0|max:100';
        }

        $validated = $request->validate($rules, [
            'completed_qty.required' => 'Qty selesai wajib diisi.',
            'completed_qty.max' => 'Qty selesai tidak boleh lebih besar dari qty request.',
        ]);

        if ($usesAutoQtyProgress) {
            $completedQty = (int) $validated['completed_qty'];
            $progressPercentage = (int) round(($completedQty / $requestQty) * 100);
        } else {
            $progressPercentage = (int) $validated['progress_percentage'];
            $completedQty = $progressPercentage >= 100 ? 1 : 0;
        }

        $progressPercentage = min(100, max(0, $progressPercentage));

        if (in_array($validated['progress_status'], ['Hold', 'Rework']) && empty($validated['problem_note'])) {
            return redirect()
                ->back()
                ->withErrors(['problem_note' => 'Problem note wajib diisi untuk status Hold atau Rework.'])
                ->withInput();
        }

        if ($validated['progress_status'] === 'Done' && $progressPercentage !== 100) {
            $errorField = $usesAutoQtyProgress ? 'completed_qty' : 'progress_percentage';
            $errorMessage = $usesAutoQtyProgress
                ? 'Qty selesai wajib sama dengan qty request jika status Done.'
                : 'Progress percentage wajib 100 jika status Done.';

            return redirect()
                ->back()
                ->withErrors([$errorField => $errorMessage])
                ->withInput();
        }

        $photoFile = null;

        if ($request->hasFile('photo_file')) {
            $photoFile = $request->file('photo_file')->store('workshop-progress/photos', 'public');
        }

        $updateData = [
            'progress_status' => $validated['progress_status'],
            'progress_percentage' => $progressPercentage,
            'completed_qty' => $completedQty,
            'progress_note' => $validated['progress_note'] ?? null,
            'problem_note' => $validated['problem_note'] ?? null,
        ];

        if (in_array($validated['progress_status'], ['On Progress', 'Hold', 'Rework'], true)) {
            $updateData['schedule_status'] = 'In Progress';

            if (!$workshopSchedule->started_at) {
                $updateData['started_at'] = now();
            }
        }

        if ($validated['progress_status'] === 'Not Started') {
            $updateData['schedule_status'] = 'Ready to Work';
        }

        if ($validated['progress_status'] === 'Done') {
            $updateData['schedule_status'] = 'Finished';
            $updateData['finished_at'] = now();
            $updateData['actual_finish_date'] = now()->toDateString();

            if (!$workshopSchedule->started_at) {
                $updateData['started_at'] = now();
            }
        }

        $workshopSchedule->update($updateData);

        RequestActivity::record(
            $workshopSchedule->ie_request_id,
            'Workshop Progress',
            'Workshop Progress Updated',
            $oldProgress,
            $validated['progress_status'] . ' - ' . $progressPercentage . '%' . ($usesAutoQtyProgress ? ' - Qty ' . $completedQty . '/' . $requestQty : ''),
            $validated['progress_note'] ?? $validated['problem_note'] ?? null
        );

        if (in_array($validated['progress_status'], ['On Progress', 'Hold', 'Rework'], true)) {
            $this->syncRequestStatus(
                $workshopSchedule->ieRequest,
                'Workshop On Progress',
                'Workshop Progress',
                'Workshop progress ' . strtolower($validated['progress_status'])
            );
        }

        if ($validated['progress_status'] === 'Not Started') {
            $this->syncRequestStatus($workshopSchedule->ieRequest, 'Workshop Scheduled', 'Workshop Progress', 'Workshop progress not started');
        }

        if ($validated['progress_status'] === 'Done') {
            $this->syncRequestStatus($workshopSchedule->ieRequest, 'Final Check', 'Workshop Progress', 'Workshop progress done');
        }

        $workshopSchedule->progressLogs()->create([
            'ie_request_id' => $workshopSchedule->ie_request_id,
            'user_id' => Auth::id(),
            'progress_status' => $validated['progress_status'],
            'progress_percentage' => $progressPercentage,
            'completed_qty' => $completedQty,
            'note' => $validated['progress_note'] ?? null,
            'problem_note' => $validated['problem_note'] ?? null,
            'photo_file' => $photoFile,
        ]);

        return redirect()->back()->with('success', 'Workshop progress berhasil diperbarui.');
    }

    public function storeLog(Request $request, WorkshopSchedule $workshopSchedule)
    {
        $validated = $request->validate([
            'note' => 'required|string',
            'problem_note' => 'nullable|string',
            'photo_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $photoFile = null;

        if ($request->hasFile('photo_file')) {
            $photoFile = $request->file('photo_file')->store('workshop-progress/photos', 'public');
        }

        $workshopSchedule->progressLogs()->create([
            'ie_request_id' => $workshopSchedule->ie_request_id,
            'user_id' => Auth::id(),
            'progress_status' => $workshopSchedule->progress_status,
            'progress_percentage' => $workshopSchedule->progress_percentage,
            'completed_qty' => $workshopSchedule->completed_qty,
            'note' => $validated['note'],
            'problem_note' => $validated['problem_note'] ?? null,
            'photo_file' => $photoFile,
        ]);

        return redirect()->back()->with('success', 'Catatan progress berhasil ditambahkan.');
    }
}
