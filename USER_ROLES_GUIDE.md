# Cohort Web App - User Roles Guide

**Date**: May 30, 2026  
**Status**: ✅ Fully Implemented

---

## 📋 Overview

The Cohort Web App has **3 user roles** with different responsibilities and access levels:

1. **Public User** (No login required)
2. **Organization Editor** (org_editor role)
3. **Secretary** (secretary role)

---

## 👥 Role Breakdown

### 1. Public User (No Authentication Required)

**Access Level**: Read-only access to published content

**Capabilities**:
- ✅ Browse organizations
- ✅ Read stories
- ✅ View resources
- ✅ See events
- ✅ Search content
- ❌ Cannot create or edit content
- ❌ Cannot submit content

**Routes**:
- `/` - Home page
- `/organizations` - Browse organizations
- `/organizations/{slug}` - View organization profile
- `/stories` - Browse stories
- `/stories/{slug}` - Read story
- `/resources` - Browse resources
- `/resources/{slug}` - View resource
- `/events` - View events
- `/events/{slug}` - View event details
- `/search` - Search content

**No Login Required** - Anyone can access these pages

---

### 2. Organization Editor (org_editor)

**Access Level**: Can manage their organization's content

**Capabilities**:
- ✅ Update organization profile
- ✅ Create stories
- ✅ Edit stories (own organization only)
- ✅ Upload resources
- ✅ Submit content for approval
- ✅ Save drafts
- ✅ View submission status
- ❌ Cannot approve/reject submissions
- ❌ Cannot manage users
- ❌ Cannot create events
- ❌ Cannot manage tags

**Dashboard**: `/org-editor/dashboard`

**Routes**:
```
/org-editor/dashboard                    - Dashboard
/org-editor/organization/edit            - Edit organization profile
/org-editor/stories                      - List stories
/org-editor/stories/create               - Create new story
/org-editor/stories/{id}/edit            - Edit story
/org-editor/resources                    - List resources
/org-editor/resources/create             - Upload resource
```

**Requirements**:
- Must be logged in
- Must have `org_editor` role
- Must be assigned to an organization

---

### 3. Secretary (secretary)

**Access Level**: Full administrative access

**Capabilities**:
- ✅ Review all submissions
- ✅ Approve submissions (publishes content)
- ✅ Reject submissions
- ✅ Request changes on submissions
- ✅ Create and manage users
- ✅ Assign roles to users
- ✅ Create and manage events
- ✅ Upload event media
- ✅ Create and manage tags
- ✅ View activity logs
- ✅ Access all content

**Dashboard**: `/secretary/dashboard`

**Routes**:
```
/secretary/dashboard                     - Dashboard
/secretary/submissions                   - Review submissions
/secretary/submissions/{id}              - View submission details
/secretary/submissions/{id}/approve      - Approve submission
/secretary/submissions/{id}/reject       - Reject submission
/secretary/submissions/{id}/request-changes - Request changes
/secretary/users                         - Manage users
/secretary/users/create                  - Create new user
/secretary/users/{id}/edit               - Edit user
/secretary/events                        - Manage events
/secretary/events/create                 - Create event
/secretary/events/{id}/edit              - Edit event
/secretary/tags                          - Manage tags
```

**Requirements**:
- Must be logged in
- Must have `secretary` role

---

## 🔐 How to Access Different Roles

### Option 1: Use Default Secretary Account (Already Created)

A default secretary account was created during database seeding:

**Email**: `secretary@cohortapp.com`  
**Password**: `Secretary@2024!`

**Steps**:
1. Start the application: `php artisan serve`
2. Visit: `http://localhost:8000/login`
3. Login with the credentials above
4. You'll be redirected to: `/secretary/dashboard`

---

### Option 2: Create Test Accounts

#### Step 1: Run Migrations and Seeders

```bash
# Run migrations
php artisan migrate

# Run seeders (creates roles and default secretary)
php artisan db:seed --class=RoleSeeder
```

#### Step 2: Create Organization Editor Account

**Option A: Via Secretary Dashboard** (Recommended)
1. Login as secretary
2. Go to `/secretary/users/create`
3. Fill in the form:
   - Name: `Test Editor`
   - Email: `editor@test.com`
   - Password: `Editor@2024!`
   - Role: `Organization Editor`
   - Organization: Select an organization
4. Click "Create User"

**Option B: Via Tinker**
```bash
php artisan tinker
```

```php
// Create an organization first
$org = App\Models\Organization::create([
    'name' => 'Test Organization',
    'slug' => 'test-organization',
    'short_description' => 'A test organization',
    'full_profile' => 'This is a test organization for development.',
    'status' => 'published',
    'published_at' => now(),
]);

// Create org editor user
$editor = App\Models\User::create([
    'name' => 'Test Editor',
    'email' => 'editor@test.com',
    'password' => Hash::make('Editor@2024!'),
    'organization_id' => $org->id,
]);

// Assign role
$editor->assignRole('org_editor');

echo "Org Editor created: editor@test.com / Editor@2024!\n";
```

#### Step 3: Create Additional Secretary Account

```bash
php artisan tinker
```

```php
$secretary = App\Models\User::create([
    'name' => 'Admin Secretary',
    'email' => 'admin@cohortapp.com',
    'password' => Hash::make('Admin@2024!'),
]);

$secretary->assignRole('secretary');

echo "Secretary created: admin@cohortapp.com / Admin@2024!\n";
```

---

## 🚀 Quick Start Guide

### For Secretaries

1. **Login**
   ```
   http://localhost:8000/login
   Email: secretary@cohortapp.com
   Password: Secretary@2024!
   ```

2. **Create Organizations**
   - Organizations are created via database seeder or tinker
   - Or create via migration/seeder

3. **Create Org Editor Users**
   - Go to `/secretary/users/create`
   - Assign them to an organization
   - They'll receive a welcome email with credentials

4. **Review Submissions**
   - Go to `/secretary/submissions`
   - View pending submissions
   - Approve, reject, or request changes

5. **Create Events**
   - Go to `/secretary/events/create`
   - Fill in event details
   - Upload banner and media
   - Event is published immediately

6. **Manage Tags**
   - Go to `/secretary/tags`
   - Create tags for categorization

---

### For Organization Editors

1. **Login**
   ```
   http://localhost:8000/login
   Email: [your email]
   Password: [your password]
   ```

2. **Update Organization Profile**
   - Go to `/org-editor/organization/edit`
   - Update profile, logo, social links
   - Submit for secretary approval

3. **Create Stories**
   - Go to `/org-editor/stories/create`
   - Fill in story details
   - Upload featured image
   - Add tags
   - Choose "Save as Draft" or "Submit for Review"

4. **Upload Resources**
   - Go to `/org-editor/resources/create`
   - Upload files (PDF, DOC, PPT)
   - Or add external links
   - Or add video URLs (YouTube/Vimeo)
   - Submit for approval

5. **Check Submission Status**
   - Go to `/org-editor/stories`
   - See status: Draft, Submitted, Approved, Rejected, Needs Changes
   - Edit and resubmit if changes requested

---

## 🔒 Permission Matrix

| Feature | Public | Org Editor | Secretary |
|---------|--------|------------|-----------|
| **View Published Content** | ✅ | ✅ | ✅ |
| **Search Content** | ✅ | ✅ | ✅ |
| **Update Organization Profile** | ❌ | ✅ (own org) | ✅ (all) |
| **Create Stories** | ❌ | ✅ | ✅ |
| **Edit Stories** | ❌ | ✅ (own org) | ✅ (all) |
| **Upload Resources** | ❌ | ✅ | ✅ |
| **Submit Content** | ❌ | ✅ | N/A |
| **Approve/Reject Submissions** | ❌ | ❌ | ✅ |
| **Create Events** | ❌ | ❌ | ✅ |
| **Manage Users** | ❌ | ❌ | ✅ |
| **Manage Tags** | ❌ | ❌ | ✅ |
| **View Activity Logs** | ❌ | ❌ | ✅ |

---

## 🔄 Content Workflow

### Story Submission Workflow

```
1. Org Editor creates story
   ↓
2. Org Editor submits for review
   ↓
3. Secretary receives notification
   ↓
4. Secretary reviews submission
   ↓
   ├─→ APPROVE → Story published → Org Editor notified
   ├─→ REJECT → Story rejected → Org Editor notified
   └─→ REQUEST CHANGES → Org Editor notified → Edit & Resubmit
```

### Organization Update Workflow

```
1. Org Editor updates profile
   ↓
2. System creates revision snapshot
   ↓
3. Submission created for approval
   ↓
4. Secretary reviews changes (before/after diff)
   ↓
   ├─→ APPROVE → Changes applied → Profile updated
   └─→ REJECT → Changes discarded → Profile unchanged
```

---

## 🎨 Dashboard Features

### Org Editor Dashboard
- **My Stories**: List of all stories (draft, submitted, published)
- **My Resources**: List of uploaded resources
- **Pending Submissions**: Content awaiting approval
- **Quick Actions**: Create story, upload resource, edit profile

### Secretary Dashboard
- **Pending Submissions**: Count and list
- **Recent Activity**: Latest submissions and approvals
- **Statistics**: Total organizations, stories, resources, events
- **Quick Actions**: Review submissions, create user, create event

---

## 🔧 Technical Implementation

### Roles & Permissions

Using **Spatie Laravel Permission** package:

**Roles**:
- `secretary` - Full access
- `org_editor` - Limited to own organization

**Permissions** (examples):
- `view stories`
- `create stories`
- `edit stories`
- `publish stories`
- `review submissions`
- `approve submissions`
- `manage users`
- `manage tags`

### Middleware

**SecretaryMiddleware**:
- Checks if user has `secretary` role
- Returns 403 if not authorized

**OrgEditorMiddleware**:
- Checks if user has `org_editor` role
- Checks if user has organization assigned
- Returns 403 if not authorized

### Route Protection

```php
// Org Editor routes
Route::middleware(['auth', 'org_editor'])->group(function () {
    // Protected routes
});

// Secretary routes
Route::middleware(['auth', 'secretary'])->group(function () {
    // Protected routes
});
```

---

## 📝 Testing Roles

### Test Secretary Access

```bash
# Login as secretary
Email: secretary@cohortapp.com
Password: Secretary@2024!

# Try accessing:
http://localhost:8000/secretary/dashboard ✅ Should work
http://localhost:8000/secretary/submissions ✅ Should work
http://localhost:8000/org-editor/dashboard ❌ Should get 403
```

### Test Org Editor Access

```bash
# Login as org editor
Email: editor@test.com
Password: Editor@2024!

# Try accessing:
http://localhost:8000/org-editor/dashboard ✅ Should work
http://localhost:8000/org-editor/stories/create ✅ Should work
http://localhost:8000/secretary/dashboard ❌ Should get 403
```

### Test Public Access

```bash
# No login required

# Try accessing:
http://localhost:8000/ ✅ Should work
http://localhost:8000/stories ✅ Should work
http://localhost:8000/org-editor/dashboard ❌ Should redirect to login
http://localhost:8000/secretary/dashboard ❌ Should redirect to login
```

---

## 🐛 Troubleshooting

### "Access denied. Secretary role required."

**Problem**: User doesn't have secretary role

**Solution**:
```bash
php artisan tinker
```
```php
$user = User::where('email', 'your@email.com')->first();
$user->assignRole('secretary');
```

### "Access denied. Organization Editor role required."

**Problem**: User doesn't have org_editor role

**Solution**:
```bash
php artisan tinker
```
```php
$user = User::where('email', 'your@email.com')->first();
$user->assignRole('org_editor');
```

### "No organization assigned to your account."

**Problem**: Org editor doesn't have organization assigned

**Solution**:
```bash
php artisan tinker
```
```php
$user = User::where('email', 'your@email.com')->first();
$org = Organization::first(); // Or find specific org
$user->organization_id = $org->id;
$user->save();
```

### Roles not working after seeding

**Problem**: Permission cache not cleared

**Solution**:
```bash
php artisan cache:clear
php artisan config:clear
```

---

## 📚 Quick Reference

### Default Accounts

| Role | Email | Password |
|------|-------|----------|
| Secretary | secretary@cohortapp.com | Secretary@2024! |

### Dashboard URLs

| Role | URL |
|------|-----|
| Org Editor | /org-editor/dashboard |
| Secretary | /secretary/dashboard |

### Key Commands

```bash
# Run migrations
php artisan migrate

# Seed roles and permissions
php artisan db:seed --class=RoleSeeder

# Create user via tinker
php artisan tinker

# Clear cache
php artisan cache:clear
php artisan config:clear
```

---

## ✅ Summary

**Roles Implemented**: ✅ Yes (3 roles)

1. **Public User** - Browse published content
2. **Organization Editor** - Submit content for approval
3. **Secretary** - Review and approve content

**Access Method**: Login at `/login` with role-specific credentials

**Default Account**: 
- Email: `secretary@cohortapp.com`
- Password: `Secretary@2024!`

**Status**: ✅ Fully functional and ready to use

---

**Guide Created**: May 30, 2026  
**Status**: ✅ Complete

