<?php

namespace App\Http\Controllers;

use App\Models\IeRequest;
use App\Models\RequestActivity;
use App\Models\RequestComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class RequestCommentController extends Controller
{
    public function store(Request $request, IeRequest $ieRequest)
    {
        $validated = $request->validate([
            'comment' => 'required|string',
            'attachment_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx,xlsx|max:5120',
        ]);

        $attachmentFile = null;

        if ($request->hasFile('attachment_file')) {
            $attachmentFile = $request->file('attachment_file')
                ->store('request-comments/attachments', 'public');
        }

        $comment = $ieRequest->comments()->create([
            'user_id' => Auth::id(),
            'comment' => $validated['comment'],
            'attachment_file' => $attachmentFile,
        ]);

        RequestActivity::record(
            $ieRequest->id,
            'Comment',
            'Comment Added',
            null,
            null,
            Str::limit($comment->comment, 100)
        );

        return redirect()->back()->with('success', 'Komentar berhasil ditambahkan.');
    }

    public function destroy(RequestComment $requestComment)
    {
        if (Auth::user()?->role !== 'admin' && Auth::id() !== $requestComment->user_id) {
            abort(403);
        }

        $ieRequestId = $requestComment->ie_request_id;
        $note = Str::limit($requestComment->comment, 100);

        $requestComment->delete();

        RequestActivity::record(
            $ieRequestId,
            'Comment',
            'Comment Deleted',
            null,
            null,
            $note
        );

        return redirect()->back()->with('success', 'Komentar berhasil dihapus.');
    }
}
