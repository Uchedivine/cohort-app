# Org Editor Event Submission - Implementation Complete

## Overview
Implemented full event submission capability for org editors, following the same pattern as stories and resources. Org editors can now submit events for secretary approval before they go live on the public site.

## Changes Made

### 1. Database Migration
**File**: `database/migrations/2026_05_31_053547_add_organization_id_to_events_table.php`
- Added `organization_id` column to `events` table
- Foreign key constraint to `organizations` table
- Nullable to support existing secretary-created events

### 2. Event Model Updates
**File**: `app/Models/Event.php`
- Added `organization_id` to `$fillable` array
- Added `organization()` relationship
- Added `submissions()` polymorphic relationship

### 3. OrgEditor EventController
**File**: `app/Http/Controllers/OrgEditor/EventController.php`
- `index()` - List all events for the org editor's organization
- `create()` - Show event creation form
- `store()` - Create event and submission record
- `edit()` - Show edit form (for draft/needs_changes/submitted status)
- `update()` - Update event and create new submission
- `resubmit()` - Resubmit rejected events
- `authorizeEvent()` - Authorization helper

**Features**:
- File upload for banner images using FileUploadService
- Automatic submission creation using SubmissionService
- Activity logging
- Tag syncing
- SDG selection
- Status management (submitted → needs_changes → resubmit → approved → published)

### 4. Routes
**File**: `routes/web.php`
Added org editor event routes:
```php
GET  /org-editor/events              → index
GET  /org-editor/events/create       → create
POST /org-editor/events              → store
GET  /org-editor/events/{event}/edit → edit
PUT  /org-editor/events/{event}      → update
POST /org-editor/events/{event}/resubmit → resubmit
```

### 5. Views

#### Index View
**File**: `resources/views/org-editor/events/index.blade.php`
- Lists all events with status badges
- Shows event date, location, virtual indicator
- Displays reviewer feedback when present
- Edit button for draft/needs_changes/submitted
- Resubmit button for rejected (if allowed)
- View public link for published events
- Status filter dropdown
- Empty state with call-to-action

#### Create View
**File**: `resources/views/org-editor/events/create.blade.php`
- Event title (required)
- Description
- Start date & time (required)
- End date & time
- Location
- Virtual meeting link
- RSVP/registration link
- Banner image upload
- SDG selection (1-17)
- Tag selection
- Submit for review button

#### Edit View
**File**: `resources/views/org-editor/events/edit.blade.php`
- Same fields as create
- Pre-populated with existing data
- Shows reviewer feedback at top (if present)
- Current banner image preview
- Resubmit for review button

### 6. Dashboard Integration
**File**: `resources/views/org-editor/dashboard.blade.php`
Added two new cards:
- **Submit an Event** (📅) - Links to create form
- **My Events** (🗓️) - Links to events index

## How It Works

### Submission Flow
1. **Org editor creates event** → Status: `submitted`
2. **Submission record created** → Appears in secretary queue
3. **Secretary reviews** → Can approve, reject, or request changes
4. **If approved** → Status: `published`, appears on public site
5. **If rejected** → Org editor can resubmit (if allowed)
6. **If needs changes** → Org editor edits and resubmits

### Secretary Side
**No changes needed!** The existing submission queue at `/secretary/submissions` already handles events automatically because it uses polymorphic relationships. Events will appear alongside stories and resources.

### Authorization
- Org editors can only view/edit events belonging to their organization
- Only events with status `draft`, `needs_changes`, or `submitted` can be edited
- Published events are read-only

### Email Notifications
Events use the existing notification system:
- Secretary gets notified when event is submitted
- Org editor gets notified when event is approved/rejected/needs changes

## Database Schema

### events table (updated)
```
id
user_id
organization_id          ← NEW
title
slug
banner_image
description
start_date
end_date
location
virtual_link
rsvp_link
sdgs (json)
status (draft/submitted/needs_changes/approved/published/rejected)
published_at
created_at
updated_at
```

### submissions table (existing)
```
submittable_type  → 'App\Models\Event'
submittable_id    → event.id
submitted_by
reviewed_by
status
reviewer_notes
allow_resubmission
parent_submission_id
submitted_at
reviewed_at
```

## Next Steps

1. **Run migration**:
   ```bash
   php artisan migrate
   ```

2. **Clear caches**:
   ```bash
   php artisan view:clear
   php artisan route:clear
   php artisan config:clear
   ```

3. **Test the flow**:
   - Login as org editor
   - Create a new event
   - Verify it appears in secretary submission queue
   - Secretary approves it
   - Verify it appears on public events page

## Files Created
- `database/migrations/2026_05_31_053547_add_organization_id_to_events_table.php`
- `app/Http/Controllers/OrgEditor/EventController.php`
- `resources/views/org-editor/events/index.blade.php`
- `resources/views/org-editor/events/create.blade.php`
- `resources/views/org-editor/events/edit.blade.php`

## Files Modified
- `app/Models/Event.php` (added organization_id, relationships)
- `routes/web.php` (added org editor event routes)
- `resources/views/org-editor/dashboard.blade.php` (added event cards)

## Notes
- Events created by secretary before this update will have `organization_id = NULL`
- The migration makes `organization_id` nullable to support this
- Secretary can still create events directly (bypassing submission workflow)
- Org editor events MUST go through submission workflow
