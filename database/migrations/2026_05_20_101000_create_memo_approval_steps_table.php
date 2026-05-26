<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('memo_approval_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ie_request_id')->constrained('ie_requests')->cascadeOnDelete();
            $table->unsignedInteger('sequence');
            $table->string('approval_label');
            $table->foreignId('approver_user_id')->constrained('users')->cascadeOnDelete();
            $table->string('status')->default('Pending');
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->text('note')->nullable();
            $table->text('rejected_reason')->nullable();
            $table->timestamps();

            $table->unique(['ie_request_id', 'sequence']);
            $table->index(['approver_user_id', 'status']);
        });

        $admin = DB::table('users')->where('role', 'admin')->orderBy('id')->first();

        if (! $admin) {
            return;
        }

        $requests = DB::table('ie_requests')
            ->whereNotNull('memo_file')
            ->where('memo_file', '!=', '')
            ->get(['id', 'memo_status', 'memo_approved_at', 'memo_rejected_reason', 'memo_approval_note']);

        foreach ($requests as $request) {
            $stepStatus = match ($request->memo_status) {
                'Approved' => 'Approved',
                'Rejected' => 'Rejected',
                default => 'Waiting',
            };

            DB::table('memo_approval_steps')->insert([
                'ie_request_id' => $request->id,
                'sequence' => 1,
                'approval_label' => 'IE Admin',
                'approver_user_id' => $admin->id,
                'status' => $stepStatus,
                'approved_at' => $stepStatus === 'Approved' ? ($request->memo_approved_at ?? now()) : null,
                'rejected_at' => $stepStatus === 'Rejected' ? now() : null,
                'note' => $request->memo_approval_note,
                'rejected_reason' => $stepStatus === 'Rejected' ? $request->memo_rejected_reason : null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('memo_approval_steps');
    }
};
