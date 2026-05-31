<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; background: #f5f0e8; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { background: #1a1f2e; color: #ffffff; padding: 30px 20px; text-align: center; }
        .header h1 { margin: 0; font-size: 24px; }
        .content { padding: 30px 20px; }
        .reply-box { background: #edf2ee; border-left: 4px solid #3a5a3e; padding: 20px; margin: 20px 0; border-radius: 5px; }
        .reply-header { font-size: 14px; color: #6b7280; margin-bottom: 10px; }
        .reply-body { color: #2c2c2c; white-space: pre-wrap; line-height: 1.8; }
        .button { display: inline-block; background: #c9a84c; color: #1a1f2e; padding: 12px 30px; text-decoration: none; border-radius: 5px; font-weight: 600; margin: 20px 0; }
        .button:hover { background: #a8863a; }
        .footer { background: #f5f0e8; padding: 20px; text-align: center; font-size: 12px; color: #6b7280; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>💬 New Reply to Your Message</h1>
        </div>
        <div class="content">
            <p>Hello,</p>
            <p><strong>{{ $orgEditor->name }}</strong> from <strong>{{ $orgEditor->organization->name }}</strong> has replied to your message:</p>

            <div style="background: #f5f0e8; padding: 15px; border-radius: 5px; margin: 15px 0;">
                <strong>Original Subject:</strong> {{ $message->subject }}
            </div>

            <div class="reply-box">
                <div class="reply-header">
                    Reply from {{ $orgEditor->name }} • {{ $reply->created_at->format('M d, Y · H:i') }}
                </div>
                <div class="reply-body">{{ $reply->body }}</div>
            </div>

            <p>Click the button below to view the full conversation and reply:</p>
            <a href="{{ route('secretary.messages.show', $message) }}" class="button">
                View Conversation
            </a>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
