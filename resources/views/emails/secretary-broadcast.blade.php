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
        .message-box { background: #f5f0e8; border-left: 4px solid #c9a84c; padding: 20px; margin: 20px 0; border-radius: 5px; }
        .message-subject { font-size: 18px; font-weight: 600; color: #1a1f2e; margin-bottom: 15px; }
        .message-body { color: #2c2c2c; white-space: pre-wrap; line-height: 1.8; }
        .button { display: inline-block; background: #c9a84c; color: #1a1f2e; padding: 12px 30px; text-decoration: none; border-radius: 5px; font-weight: 600; margin: 20px 0; }
        .button:hover { background: #a8863a; }
        .footer { background: #f5f0e8; padding: 20px; text-align: center; font-size: 12px; color: #6b7280; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📬 New Message from Secretary</h1>
        </div>
        <div class="content">
            <p>Hello {{ $recipient->name }},</p>
            <p>You have received a new message from the secretary:</p>

            <div class="message-box">
                <div class="message-subject">{{ $message->subject }}</div>
                <div class="message-body">{{ $message->body }}</div>
            </div>

            <p>Click the button below to view the full message and reply:</p>
            <a href="{{ route('org-editor.messages.show', $message) }}" class="button">
                View Message & Reply
            </a>

            <p style="margin-top: 30px; font-size: 14px; color: #6b7280;">
                This message was sent to {{ $recipient->organization->name }}.
            </p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
