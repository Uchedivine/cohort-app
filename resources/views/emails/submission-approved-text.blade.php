Great news!

Your {{ $submittableType }} titled "{{ $submittableTitle }}" has been approved and published.

Reviewed by: {{ $reviewerName }}
@if($reviewerNotes)
Notes: {{ $reviewerNotes }}
@endif

Your content is now live and visible to the public. Thank you for your contribution to the Cohort community!

@if($viewUrl)
View Published Content: {{ $viewUrl }}
@endif

---
This email was sent by Cohort Web App
Visit our website: {{ route('home') }}
