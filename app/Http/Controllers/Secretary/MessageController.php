<?php

namespace App\Http\Controllers\Secretary;

use App\Http\Controllers\Controller;
use App\Mail\SecretaryBroadcast;
use App\Models\Message;
use App\Models\MessageRecipient;
use App\Models\MessageReply;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class MessageController extends Controller
{
    /**
     * Display sent messages log
     */
    public function index()
    {
        $messages = Message::with(['recipients.organization', 'replies'])
            ->where('sent_by', auth()->id())
            ->latest()
            ->paginate(15);

        return view('secretary.messages.index', compact('messages'));
    }

    /**
     * Show compose form
     */
    public function compose()
    {
        $organizations = Organization::where('status', 'approved')
            ->orderBy('name')
            ->get();

        return view('secretary.messages.compose', compact('organizations'));
    }

    /**
     * Send a new message
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'recipient_type' => 'required|in:all,multiple,single',
            'recipients' => 'required_if:recipient_type,multiple,single|array',
            'recipients.*' => 'exists:organizations,id',
        ]);

        DB::transaction(function () use ($validated) {
            // Create the message
            $message = Message::create([
                'subject' => $validated['subject'],
                'body' => $validated['body'],
                'sent_by' => auth()->id(),
                'recipient_type' => $validated['recipient_type'],
            ]);

            // Determine recipients
            if ($validated['recipient_type'] === 'all') {
                $organizations = Organization::where('status', 'approved')->get();
            } else {
                $organizations = Organization::whereIn('id', $validated['recipients'])->get();
            }

            // Create recipient records and send emails
            foreach ($organizations as $organization) {
                // Get the primary user for this organization
                $user = $organization->user;
                
                if ($user) {
                    MessageRecipient::create([
                        'message_id' => $message->id,
                        'organization_id' => $organization->id,
                        'user_id' => $user->id,
                    ]);

                    // Send email
                    Mail::to($user->email)->queue(new SecretaryBroadcast($message, $user));
                }
            }

            // Log activity
            activity()
                ->performedOn($message)
                ->causedBy(auth()->user())
                ->withProperties([
                    'recipient_type' => $validated['recipient_type'],
                    'recipient_count' => $organizations->count(),
                ])
                ->log('Message sent to organizations');
        });

        return redirect()->route('secretary.messages.index')
            ->with('success', 'Message sent successfully.');
    }

    /**
     * View conversation thread
     */
    public function show(Message $message)
    {
        // Ensure secretary can only view their own messages
        if ($message->sent_by !== auth()->id()) {
            abort(403);
        }

        $message->load(['recipients.organization', 'recipients.user', 'replies.sender']);

        return view('secretary.messages.show', compact('message'));
    }

    /**
     * Reply to a message
     */
    public function reply(Request $request, Message $message)
    {
        // Ensure secretary can only reply to their own messages
        if ($message->sent_by !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'body' => 'required|string',
        ]);

        DB::transaction(function () use ($message, $validated) {
            // Create the reply
            $reply = MessageReply::create([
                'message_id' => $message->id,
                'sent_by' => auth()->id(),
                'body' => $validated['body'],
            ]);

            // Send email notification to all recipients
            foreach ($message->recipients as $recipient) {
                Mail::to($recipient->user->email)->queue(new \App\Mail\SecretaryReply($message, $reply, $recipient->user));
            }

            // Log activity
            activity()
                ->performedOn($message)
                ->causedBy(auth()->user())
                ->log('Secretary replied to message');
        });

        return redirect()->route('secretary.messages.show', $message)
            ->with('success', 'Reply sent successfully.');
    }
}
