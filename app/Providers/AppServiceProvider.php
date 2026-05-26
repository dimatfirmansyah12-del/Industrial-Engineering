<?php

namespace App\Providers;

use App\Models\IeRequest;
use App\Models\MemoApprovalStep;
use App\Models\SapApproval;
use App\Models\WorkshopSchedule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer([
            'components.page-header',
            'components.sidebar',
            'components.dashboard-layout',
        ], function ($view) {
            $user = Auth::user();
            $moduleNotificationCounts = $this->moduleNotificationCounts($user);
            $memoApprovalNotificationCount = $moduleNotificationCounts['memo-approvals'] ?? 0;
            $memoApprovalNotifications = collect();

            if ($user && in_array($user->role, ['admin', 'approver'], true)) {
                $memoApprovalNotifications = $this->memoApprovalQuery($user)
                    ->with(['ieRequest', 'approver'])
                    ->latest('updated_at')
                    ->limit(5)
                    ->get();
            }

            $view->with([
                'moduleNotificationCounts' => $moduleNotificationCounts,
                'memoApprovalNotificationCount' => $memoApprovalNotificationCount,
                'memoApprovalNotifications' => $memoApprovalNotifications,
            ]);
        });
    }

    private function moduleNotificationCounts($user): array
    {
        $counts = [
            'memo-approvals' => 0,
            'drawing-progress' => 0,
            'material-bom' => 0,
            'sap-approvals' => 0,
            'budget-pr' => 0,
            'material-arrivals' => 0,
            'workshop-schedules' => 0,
            'workshop-progress' => 0,
            'final-checks' => 0,
            'handovers' => 0,
        ];

        if (! $user) {
            return $counts;
        }

        $role = $user->role ?? 'customer';
        $canSee = fn (array $roles) => $role === 'admin' || in_array($role, $roles, true);

        if ($canSee(['approver'])) {
            $counts['memo-approvals'] = $this->memoApprovalQuery($user)->count();
        }

        if ($canSee(['drafter'])) {
            $counts['drawing-progress'] = IeRequest::query()
                ->where('memo_status', 'Approved')
                ->whereNotNull('memo_file')
                ->where('memo_file', '!=', '')
                ->whereHas('memoApprovalSteps')
                ->whereDoesntHave('memoApprovalSteps', function ($approvalQuery) {
                    $approvalQuery->where('status', '!=', MemoApprovalStep::APPROVED);
                })
                ->whereIn('drawing_status', ['Not Started', 'Revision'])
                ->count();

            $counts['material-bom'] = IeRequest::query()
                ->where('drawing_status', 'Done')
                ->where(function ($query) {
                    $query->whereNull('bom_status')
                        ->orWhereIn('bom_status', [IeRequest::BOM_NO_BOM, IeRequest::BOM_DRAFT]);
                })
                ->whereDoesntHave('purchaseRequest')
                ->whereDoesntHave('sapApproval', function ($query) {
                    $query->whereNotIn('approval_status', [
                        SapApproval::WAITING_SAP_INPUT,
                        SapApproval::REJECTED,
                    ]);
                })
                ->count();
        }

        if ($canSee(['section_head', 'division_head', 'director'])) {
            $sapQuery = IeRequest::query()
                ->where('drawing_status', 'Done')
                ->where('bom_status', IeRequest::BOM_SUBMITTED)
                ->whereHas('materials')
                ->whereDoesntHave('purchaseRequest');

            if ($role === 'section_head') {
                $sapQuery->whereHas('sapApproval', function ($query) {
                    $query->where('approval_status', SapApproval::WAITING_SECTION_HEAD);
                });
            } elseif ($role === 'division_head') {
                $sapQuery->whereHas('sapApproval', function ($query) {
                    $query->where('approval_status', SapApproval::WAITING_DIVISION_HEAD);
                });
            } elseif ($role === 'director') {
                $sapQuery->whereHas('sapApproval', function ($query) {
                    $query->where('approval_status', SapApproval::WAITING_DIRECTOR);
                });
            } else {
                $sapQuery->where(function ($query) {
                    $query->whereDoesntHave('sapApproval')
                        ->orWhereHas('sapApproval', function ($approvalQuery) {
                            $approvalQuery->whereIn('approval_status', [
                                SapApproval::WAITING_SAP_INPUT,
                                SapApproval::WAITING_SECTION_HEAD,
                                SapApproval::WAITING_DIVISION_HEAD,
                                SapApproval::WAITING_DIRECTOR,
                            ]);
                        });
                });
            }

            $counts['sap-approvals'] = $sapQuery->count();
        }

        if ($canSee(['purchasing'])) {
            $counts['budget-pr'] = IeRequest::query()
                ->whereHas('materials')
                ->whereHas('sapApproval', function ($query) {
                    $query->where('approval_status', SapApproval::SENT_TO_PURCHASING);
                })
                ->whereHas('purchaseRequest', function ($query) {
                    $query->whereIn('pr_status', ['Draft', 'Waiting Approval', 'Approved', 'Rejected']);
                })
                ->count();

            $counts['material-arrivals'] = IeRequest::query()
                ->whereHas('purchaseRequest', function ($query) {
                    $query->where('pr_status', 'PO Created');
                })
                ->whereHas('materials', function ($query) {
                    $query->where('arrival_status', '!=', 'Complete');
                })
                ->count();
        }

        if ($canSee(['workshop'])) {
            $counts['workshop-schedules'] = IeRequest::query()
                ->whereHas('materials')
                ->whereDoesntHave('materials', function ($query) {
                    $query->where('arrival_status', '!=', 'Complete');
                })
                ->where(function ($query) {
                    $query->whereDoesntHave('workshopSchedule')
                        ->orWhereHas('workshopSchedule', function ($scheduleQuery) {
                            $scheduleQuery->whereIn('schedule_status', ['Scheduled', 'Rescheduled']);
                        });
                })
                ->count();

            $counts['workshop-progress'] = WorkshopSchedule::query()
                ->whereIn('schedule_status', ['Ready to Work', 'In Progress'])
                ->whereIn('progress_status', ['Not Started', 'On Progress', 'Hold', 'Rework'])
                ->count();
        }

        if ($canSee(['qc'])) {
            $counts['final-checks'] = IeRequest::query()
                ->whereHas('workshopSchedule', function ($query) {
                    $query->where('progress_status', 'Done');
                })
                ->where(function ($query) {
                    $query->whereDoesntHave('finalCheck')
                        ->orWhereHas('finalCheck', function ($checkQuery) {
                            $checkQuery->whereIn('check_status', ['Waiting Check', 'Checking', 'Need Rework', 'Failed']);
                        });
                })
                ->count();
        }

        if ($canSee(['manager'])) {
            $counts['handovers'] = IeRequest::query()
                ->whereHas('finalCheck', function ($query) {
                    $query->where('check_status', 'Passed')
                        ->where('result_status', 'OK');
                })
                ->where(function ($query) {
                    $query->whereDoesntHave('handover')
                        ->orWhereHas('handover', function ($handoverQuery) {
                            $handoverQuery->whereIn('handover_status', ['Waiting Handover', 'Handover Process', 'Rejected']);
                        });
                })
                ->count();
        }

        return $counts;
    }

    private function memoApprovalQuery($user)
    {
        $query = MemoApprovalStep::query()
            ->where('status', MemoApprovalStep::WAITING)
            ->whereHas('ieRequest', function ($requestQuery) {
                $requestQuery->whereNotNull('memo_file')
                    ->where('memo_file', '!=', '')
                    ->where('memo_status', 'Waiting Approval');
            });

        if (($user->role ?? null) !== 'admin') {
            $query->where('approver_user_id', $user->id);
        }

        return $query;
    }
}
