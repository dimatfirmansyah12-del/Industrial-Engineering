<?php

use App\Http\Controllers\BudgetPrController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DrawingProgressController;
use App\Http\Controllers\FinalCheckController;
use App\Http\Controllers\HandoverController;
use App\Http\Controllers\IeRequestController;
use App\Http\Controllers\LineAreaController;
use App\Http\Controllers\MaterialArrivalController;
use App\Http\Controllers\MaterialBomController;
use App\Http\Controllers\MemoApprovalController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RequestCommentController;
use App\Http\Controllers\SapApprovalController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WorkshopProgressController;
use App\Http\Controllers\WorkshopScheduleController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('ie-requests', IeRequestController::class)
        ->only(['create', 'store'])
        ->middleware('role:admin,customer');

    Route::resource('ie-requests', IeRequestController::class)
        ->only(['edit', 'update'])
        ->middleware('role:admin');

    Route::resource('ie-requests', IeRequestController::class)
        ->only(['destroy'])
        ->middleware('role:admin');

    Route::get('/ie-requests-kanban', [IeRequestController::class, 'kanban'])
        ->name('ie-requests.kanban');

    Route::get('/ie-requests/{ieRequest}/print', [IeRequestController::class, 'print'])
        ->name('ie-requests.print');

    Route::resource('ie-requests', IeRequestController::class)
        ->only(['index', 'show']);

    Route::patch('/ie-requests/{ieRequest}/status', [IeRequestController::class, 'updateStatus'])
        ->middleware('role:admin')
        ->name('ie-requests.update-status');

    Route::patch('/ie-requests/{ieRequest}/priority', [IeRequestController::class, 'updatePriority'])
        ->middleware('role:admin,drafter,workshop,purchasing,qc')
        ->name('ie-requests.update-priority');

    Route::post('/ie-requests/{ieRequest}/comments', [RequestCommentController::class, 'store'])
        ->name('ie-requests.comments.store');

    Route::delete('/ie-requests/comments/{requestComment}', [RequestCommentController::class, 'destroy'])
        ->name('ie-requests.comments.destroy');

    Route::middleware('role:admin,approver')->group(function () {
        Route::get('/memo-approvals', [MemoApprovalController::class, 'index'])
            ->name('memo-approvals.index');

        Route::patch('/memo-approvals/{memoApprovalStep}/approve', [MemoApprovalController::class, 'approve'])
            ->name('memo-approvals.approve');

        Route::patch('/memo-approvals/{memoApprovalStep}/reject', [MemoApprovalController::class, 'reject'])
            ->name('memo-approvals.reject');

        Route::get('/handovers', [HandoverController::class, 'index'])
            ->name('handovers.index');

        Route::get('/handovers/{ieRequest}', [HandoverController::class, 'show'])
            ->name('handovers.show');

        Route::post('/handovers/{ieRequest}', [HandoverController::class, 'store'])
            ->name('handovers.store');

        Route::patch('/handovers/{handover}/process', [HandoverController::class, 'process'])
            ->name('handovers.process');

        Route::patch('/handovers/{handover}/received', [HandoverController::class, 'received'])
            ->name('handovers.received');

        Route::patch('/handovers/{handover}/reject', [HandoverController::class, 'reject'])
            ->name('handovers.reject');

    });

    Route::middleware('role:admin,manager')->group(function () {
        Route::get('/reports', [IeRequestController::class, 'report'])
            ->name('reports.index');

        Route::get('/ie-requests-export', [IeRequestController::class, 'export'])
            ->name('ie-requests.export');
    });

    Route::middleware('role:admin,drafter')->group(function () {
        Route::get('/drawing-progress', [DrawingProgressController::class, 'index'])
            ->name('drawing-progress.index');

        Route::patch('/drawing-progress/{ieRequest}/start', [DrawingProgressController::class, 'start'])
            ->name('drawing-progress.start');

        Route::patch('/drawing-progress/{ieRequest}/revision', [DrawingProgressController::class, 'revision'])
            ->name('drawing-progress.revision');

        Route::patch('/drawing-progress/{ieRequest}/done', [DrawingProgressController::class, 'done'])
            ->name('drawing-progress.done');

        Route::patch('/drawing-progress/{ieRequest}/assign', [DrawingProgressController::class, 'assign'])
            ->name('drawing-progress.assign');

        Route::get('/material-bom', [MaterialBomController::class, 'index'])
            ->name('material-bom.index');

        Route::get('/material-bom/{ieRequest}', [MaterialBomController::class, 'show'])
            ->name('material-bom.show');

        Route::patch('/material-bom/{ieRequest}/submit', [MaterialBomController::class, 'submit'])
            ->name('material-bom.submit');

        Route::patch('/material-bom/{ieRequest}/revise', [MaterialBomController::class, 'revise'])
            ->name('material-bom.revise');

        Route::post('/material-bom/{ieRequest}/materials', [MaterialBomController::class, 'store'])
            ->name('material-bom.materials.store');

        Route::put('/material-bom/materials/{requestMaterial}', [MaterialBomController::class, 'update'])
            ->name('material-bom.materials.update');

        Route::delete('/material-bom/materials/{requestMaterial}', [MaterialBomController::class, 'destroy'])
            ->name('material-bom.materials.destroy');
    });

    Route::middleware('role:admin,section_head,division_head,director')->group(function () {
        Route::get('/sap-approvals', [SapApprovalController::class, 'index'])
            ->name('sap-approvals.index');

        Route::get('/sap-approvals/{ieRequest}', [SapApprovalController::class, 'show'])
            ->name('sap-approvals.show');

        Route::post('/sap-approvals/{ieRequest}/sap-input', [SapApprovalController::class, 'storeSapInput'])
            ->name('sap-approvals.sap-input');

        Route::patch('/sap-approvals/{sapApproval}/section-approve', [SapApprovalController::class, 'sectionApprove'])
            ->name('sap-approvals.section-approve');

        Route::patch('/sap-approvals/{sapApproval}/section-reject', [SapApprovalController::class, 'sectionReject'])
            ->name('sap-approvals.section-reject');

        Route::patch('/sap-approvals/{sapApproval}/division-approve', [SapApprovalController::class, 'divisionApprove'])
            ->name('sap-approvals.division-approve');

        Route::patch('/sap-approvals/{sapApproval}/division-reject', [SapApprovalController::class, 'divisionReject'])
            ->name('sap-approvals.division-reject');

        Route::patch('/sap-approvals/{sapApproval}/director-approve', [SapApprovalController::class, 'directorApprove'])
            ->name('sap-approvals.director-approve');

        Route::patch('/sap-approvals/{sapApproval}/director-reject', [SapApprovalController::class, 'directorReject'])
            ->name('sap-approvals.director-reject');

    });

    Route::middleware('role:admin,purchasing')->group(function () {
        Route::get('/budget-pr', [BudgetPrController::class, 'index'])
            ->name('budget-pr.index');

        Route::get('/budget-pr/{ieRequest}', [BudgetPrController::class, 'show'])
            ->name('budget-pr.show');

        Route::patch('/budget-pr/{purchaseRequest}/po-created', [BudgetPrController::class, 'poCreated'])
            ->name('budget-pr.po-created');

        Route::get('/material-arrivals', [MaterialArrivalController::class, 'index'])
            ->name('material-arrivals.index');

        Route::get('/material-arrivals/{ieRequest}', [MaterialArrivalController::class, 'show'])
            ->name('material-arrivals.show');

        Route::patch('/material-arrivals/materials/{requestMaterial}', [MaterialArrivalController::class, 'updateMaterial'])
            ->name('material-arrivals.update-material');

        Route::patch('/material-arrivals/{ieRequest}/complete', [MaterialArrivalController::class, 'complete'])
            ->name('material-arrivals.complete');
    });

    Route::middleware('role:admin,workshop')->group(function () {
        Route::get('/workshop-schedules', [WorkshopScheduleController::class, 'index'])
            ->name('workshop-schedules.index');

        Route::get('/workshop-schedules/{ieRequest}', [WorkshopScheduleController::class, 'show'])
            ->name('workshop-schedules.show');

        Route::post('/workshop-schedules/{ieRequest}', [WorkshopScheduleController::class, 'store'])
            ->name('workshop-schedules.store');

        Route::put('/workshop-schedules/{workshopSchedule}', [WorkshopScheduleController::class, 'update'])
            ->name('workshop-schedules.update');

        Route::patch('/workshop-schedules/{workshopSchedule}/ready', [WorkshopScheduleController::class, 'ready'])
            ->name('workshop-schedules.ready');

        Route::patch('/workshop-schedules/{workshopSchedule}/reschedule', [WorkshopScheduleController::class, 'reschedule'])
            ->name('workshop-schedules.reschedule');

        Route::patch('/workshop-schedules/{workshopSchedule}/cancel', [WorkshopScheduleController::class, 'cancel'])
            ->name('workshop-schedules.cancel');

        Route::patch('/workshop-people/{workshopPerson}', [WorkshopScheduleController::class, 'updatePerson'])
            ->name('workshop-schedules.people.update');

        Route::get('/workshop-progress', [WorkshopProgressController::class, 'index'])
            ->name('workshop-progress.index');

        Route::get('/workshop-progress/{workshopSchedule}', [WorkshopProgressController::class, 'show'])
            ->name('workshop-progress.show');

        Route::patch('/workshop-progress/{workshopSchedule}/update', [WorkshopProgressController::class, 'update'])
            ->name('workshop-progress.update');

        Route::post('/workshop-progress/{workshopSchedule}/logs', [WorkshopProgressController::class, 'storeLog'])
            ->name('workshop-progress.logs.store');
    });

    Route::middleware('role:admin,qc')->group(function () {
        Route::get('/final-checks', [FinalCheckController::class, 'index'])
            ->name('final-checks.index');

        Route::get('/final-checks/{ieRequest}', [FinalCheckController::class, 'show'])
            ->name('final-checks.show');

        Route::post('/final-checks/{ieRequest}', [FinalCheckController::class, 'store'])
            ->name('final-checks.store');

        Route::patch('/final-checks/{finalCheck}/checking', [FinalCheckController::class, 'checking'])
            ->name('final-checks.checking');

        Route::patch('/final-checks/{finalCheck}/passed', [FinalCheckController::class, 'passed'])
            ->name('final-checks.passed');

        Route::patch('/final-checks/{finalCheck}/need-rework', [FinalCheckController::class, 'needRework'])
            ->name('final-checks.need-rework');

        Route::patch('/final-checks/{finalCheck}/failed', [FinalCheckController::class, 'failed'])
            ->name('final-checks.failed');
    });

    Route::resource('departments', DepartmentController::class)
        ->except(['show'])
        ->middleware('role:admin');

    Route::resource('line-areas', LineAreaController::class)
        ->except(['show'])
        ->middleware('role:admin');

    Route::middleware('role:admin')->group(function () {
        Route::get('/users', [UserController::class, 'index'])
            ->name('users.index');

        Route::get('/users/{user}/edit', [UserController::class, 'edit'])
            ->name('users.edit');

        Route::put('/users/{user}', [UserController::class, 'update'])
            ->name('users.update');
    });
});

require __DIR__.'/auth.php';
