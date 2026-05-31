<?php

namespace App\Http\Controllers\OrgEditor;

use App\Http\Controllers\Controller;
use App\Mail\OrgEditorReply;
use App\Models\Message;
use App\Models\MessageReply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class MessageController extends Controller
{
    /**
     * Display inbox
     */
    public function index()
    {
        $user = auth()->user();

        $messages = Message::whereHas('recipients', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->with(['sender', 'recipients' => function ($query) use ($user) {
                $query->where('user_id', $user->id);
            }, 'replies'])
            ->latest()
            ->paginate(15);

        return view('org-editor.messages.index', compact('messages'));
    }

    /**
     * View message and reply form
     */
    public function show(Message $message)
    {
        $user = auth()->user();

        // Ensure user is a recipient of this message
        $recipient = $message->recipients()->where('user_id', $user->id)->first();
        
        if (!$recipient) {
            abort(403, 'You do not have access to this message.');
        }

        // Mark as read
        if (!$recipient->read_at) {
            $recipient->update(['read_at' => now()]);
        }

        $message->load(['sender', 'replies.sender']);

        return view('org-editor.messages.show', compact('message', 'recipient'));
    }

    /**
     * Reply to a message
     */
    public function reply(Request $request, Message $message)
    {
        $user = auth()->user();

        // Ensure user is a recipient of this message
        $recipient = $message->recipients()->where('user_id', $user->id)->first();
        
        if (!$recipient) {
            abort(403, 'You do not have access to this message.');
        }

        $validated = $request->validate([
            'body' => 'required|string',
        ]);

        DB::transaction(function () use ($message, $validated, $user) {
            // Create the reply
            $reply = MessageReply::create([
                'message_id' => $message->id,
                'sent_by' => $user->id,
                'body' => $validated['body'],
            ]);

            // Send email notification to secretary
            Mail::to($message->sender->email)->queue(new OrgEditorReply($message, $reply, $user));

            // Log activity
            activity()
                ->performedOn($message)
                ->causedBy($user)
                ->withProperties(['organization' => $user->organization->name])
                ->log('Organization replied to message');
        });

        return redirect()->route('org-editor.messages.show', $message)
            ->with('success', 'Reply sent successfully.');
    }

    /**
     * Get unread message count for badge
     */
    public static function getUnreadCount()
    {
        return Message::whereHas('recipients', function ($query) {
                $query->where('user_id', auth()->id())
                      ->whereNull('read_at');
            })
            ->count();
    }
}
