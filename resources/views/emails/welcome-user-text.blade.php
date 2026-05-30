Welcome to Cohort, {{ $userName }}!

Your account has been created successfully. You can now access the Cohort Web App.

Email: {{ $userEmail }}
Temporary Password: {{ $temporaryPassword }}
Role: {{ ucfirst(str_replace('_', ' ', $role)) }}
@if($organizationName)
Organization: {{ $organizationName }}
@endif

IMPORTANT: Please change your password after your first login for security purposes.

Login to Your Account: {{ $loginUrl }}

---

Getting Started:

@if($role === 'org_editor')
- Update your organization profile
- Submit stories about your work
- Upload resources to share with the community
- Track your submission status
@elseif($role === 'secretary')
- Review pending submissions
- Manage user accounts
- Create and publish events
- Manage tags and categories
@endif

If you have any questions, please don't hesitate to reach out to our support team.

---
This email was sent by Cohort Web App
Visit our website: {{ route('home') }}
