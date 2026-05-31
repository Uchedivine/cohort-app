<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Public\OrganizationController as PublicOrganizationController;
use App\Http\Controllers\Public\StoryController as PublicStoryController;
use App\Http\Controllers\Public\ResourceController as PublicResourceController;
use App\Http\Controllers\Public\EventController as PublicEventController;
use App\Http\Controllers\OrgEditor\OrganizationController as EditorOrganizationController;
use App\Http\Controllers\OrgEditor\StoryController as EditorStoryController;
use App\Http\Controllers\OrgEditor\ResourceController as EditorResourceController;
use App\Http\Controllers\Secretary\SubmissionController;
use App\Http\Controllers\Secretary\UserController;
use App\Http\Controllers\Secretary\EventController as SecretaryEventController;
use App\Http\Controllers\Secretary\TagController;
use App\Http\Controllers\SearchController;

// -------------------------------------------------------
// Public Routes
// -------------------------------------------------------
Route::get('/', function () {
    $latestStories = \App\Models\Story::with('tags')
        ->published()
        ->latest()
        ->take(4)
        ->get();

    $upcomingEvents = \App\Models\Event::with('tags')
        ->published()
        ->upcoming()
        ->orderBy('start_date')
        ->take(3)
        ->get();

    return view('public.home', compact('latestStories', 'upcomingEvents'));
})->name('home');

Route::prefix('organizations')->name('organizations.')->group(function () {
    Route::get('/', [PublicOrganizationController::class, 'index'])->name('index');
    Route::get('/{slug}', [PublicOrganizationController::class, 'show'])->name('show');
});

Route::prefix('stories')->name('stories.')->group(function () {
    Route::get('/', [PublicStoryController::class, 'index'])->name('index');
    Route::get('/{slug}', [PublicStoryController::class, 'show'])->name('show');
});

Route::prefix('resources')->name('resources.')->group(function () {
    Route::get('/', [PublicResourceController::class, 'index'])->name('index');
    Route::get('/{slug}', [PublicResourceController::class, 'show'])->name('show');
});

Route::prefix('events')->name('events.')->group(function () {
    Route::get('/', [PublicEventController::class, 'index'])->name('index');
    Route::get('/calendar-data', [PublicEventController::class, 'calendarData'])->name('calendar-data');
    Route::get('/{slug}', [PublicEventController::class, 'show'])->name('show');
});

// Organisation Self-Registration
Route::get('/register/organisation', [App\Http\Controllers\Auth\OrganisationRegisterController::class, 'showForm'])->name('organisation.register.form');
Route::post('/register/organisation', [App\Http\Controllers\Auth\OrganisationRegisterController::class, 'register'])->name('organisation.register');

// Search Routes
Route::prefix('search')->name('search.')->group(function () {
    Route::get('/', [SearchController::class, 'index'])->name('index');
    Route::get('/advanced', [SearchController::class, 'advanced'])->name('advanced');
    Route::get('/suggestions', [SearchController::class, 'suggestions'])->name('suggestions');
});

// -------------------------------------------------------
// Auth Routes (Breeze)
// -------------------------------------------------------
require __DIR__.'/auth.php';

// -------------------------------------------------------
// Org Editor Routes
// -------------------------------------------------------

// Org editor holding pages - auth only, no org_editor middleware
Route::middleware(['auth'])->group(function () {
    Route::get('/org-editor/no-organization', function () {
        return view('org-editor.no-org');
    })->name('org-editor.no-org');

    Route::get('/org-editor/pending', function () {
        return view('org-editor.pending');
    })->name('org-editor.pending');

    Route::get('/org-editor/rejected', function () {
        return view('org-editor.rejected');
    })->name('org-editor.rejected');

    Route::post('/org-editor/reapply', [App\Http\Controllers\Auth\OrganisationRegisterController::class, 'reapply'])->name('org-editor.reapply');
});

Route::prefix('org-editor')->name('org-editor.')->middleware(['auth', 'org_editor'])->group(function () {

    Route::get('/dashboard', function () {
        $organizationId = auth()->user()->organization_id;

        $stats = [
            'pending' => \App\Models\Submission::where('submitted_by', auth()->id())
                ->where('status', 'submitted')
                ->count(),
            'approved' => \App\Models\Submission::where('submitted_by', auth()->id())
                ->where('status', 'approved')
                ->count(),
            'needs_changes' => \App\Models\Submission::where('submitted_by', auth()->id())
                ->where('status', 'needs_changes')
                ->count(),
            'published_stories' => \App\Models\Story::where('organization_id', $organizationId)
                ->where('status', 'published')
                ->count(),
        ];

        $recentSubmissions = \App\Models\Submission::with('submittable')
            ->where('submitted_by', auth()->id())
            ->latest()
            ->take(10)
            ->get();

        return view('org-editor.dashboard', compact('stats', 'recentSubmissions'));
    })->name('dashboard');

    // Organization profile
    Route::get('/organization/edit', [EditorOrganizationController::class, 'edit'])->name('organization.edit');
    Route::put('/organization/update', [EditorOrganizationController::class, 'update'])->name('organization.update');

    // Stories
    Route::prefix('stories')->name('stories.')->group(function () {
        Route::get('/', [EditorStoryController::class, 'index'])->name('index');
        Route::get('/create', [EditorStoryController::class, 'create'])->name('create');
        Route::post('/', [EditorStoryController::class, 'store'])->name('store');
        Route::get('/{story}/edit', [EditorStoryController::class, 'edit'])->name('edit');
        Route::put('/{story}', [EditorStoryController::class, 'update'])->name('update');
        Route::post('/{story}/resubmit', [EditorStoryController::class, 'resubmit'])->name('resubmit');
    });

    // Resources
    Route::prefix('resources')->name('resources.')->group(function () {
        Route::get('/', [EditorResourceController::class, 'index'])->name('index');
        Route::get('/create', [EditorResourceController::class, 'create'])->name('create');
        Route::post('/', [EditorResourceController::class, 'store'])->name('store');
        Route::get('/{resource}/edit', [EditorResourceController::class, 'edit'])->name('edit');
        Route::put('/{resource}', [EditorResourceController::class, 'update'])->name('update');
        Route::delete('/{resource}', [EditorResourceController::class, 'destroy'])->name('destroy');
        Route::post('/{resource}/resubmit', [EditorResourceController::class, 'resubmit'])->name('resubmit');
    });

    // Messages
    Route::prefix('messages')->name('messages.')->group(function () {
        Route::get('/', [App\Http\Controllers\OrgEditor\MessageController::class, 'index'])->name('index');
        Route::get('/{message}', [App\Http\Controllers\OrgEditor\MessageController::class, 'show'])->name('show');
        Route::post('/{message}/reply', [App\Http\Controllers\OrgEditor\MessageController::class, 'reply'])->name('reply');
    });
});

// -------------------------------------------------------
// Secretary Routes
// -------------------------------------------------------
Route::prefix('secretary')->name('secretary.')->middleware(['auth', 'secretary'])->group(function () {

    Route::get('/dashboard', function () {
        $stats = [
            'pending_submissions' => \App\Models\Submission::where('status', 'submitted')->count(),
            'total_organizations' => \App\Models\Organization::where('status', 'published')->count(),
            'published_stories' => \App\Models\Story::where('status', 'published')->count(),
            'published_resources' => \App\Models\Resource::where('status', 'published')->count(),
            'upcoming_events' => \App\Models\Event::where('start_date', '>=', now())->count(),
            'total_users' => \App\Models\User::count(),
        ];

        $pendingSubmissions = \App\Models\Submission::with(['submittable', 'submittedBy'])
            ->where('status', 'submitted')
            ->latest()
            ->take(5)
            ->get();

        $recentActivity = \Spatie\Activitylog\Models\Activity::with('causer')
            ->latest()
            ->take(10)
            ->get();

        return view('secretary.dashboard', compact('stats', 'pendingSubmissions', 'recentActivity'));
    })->name('dashboard');

    // Submissions
    Route::prefix('submissions')->name('submissions.')->group(function () {
        Route::get('/', [SubmissionController::class, 'index'])->name('index');
        Route::get('/{submission}', [SubmissionController::class, 'show'])->name('show');
        Route::post('/{submission}/approve', [SubmissionController::class, 'approve'])->name('approve');
        Route::post('/{submission}/reject', [SubmissionController::class, 'reject'])->name('reject');
        Route::post('/{submission}/request-changes', [SubmissionController::class, 'requestChanges'])->name('request-changes');
    });

    // Users
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::get('/{user}', [UserController::class, 'show'])->name('show');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
        Route::put('/{user}', [UserController::class, 'update'])->name('update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
    });

    // Events
    Route::prefix('events')->name('events.')->group(function () {
        Route::get('/', [SecretaryEventController::class, 'index'])->name('index');
        Route::get('/create', [SecretaryEventController::class, 'create'])->name('create');
        Route::post('/', [SecretaryEventController::class, 'store'])->name('store');
        Route::get('/{event}/edit', [SecretaryEventController::class, 'edit'])->name('edit');
        Route::put('/{event}', [SecretaryEventController::class, 'update'])->name('update');
        Route::delete('/{event}', [SecretaryEventController::class, 'destroy'])->name('destroy');
        Route::post('/{event}/media', [SecretaryEventController::class, 'uploadMedia'])->name('media.upload');
    });

    // Tags
    Route::prefix('tags')->name('tags.')->group(function () {
        Route::get('/', [TagController::class, 'index'])->name('index');
        Route::post('/', [TagController::class, 'store'])->name('store');
        Route::delete('/{tag}', [TagController::class, 'destroy'])->name('destroy');
    });

    // Organisation Applications
    Route::prefix('applications')->name('applications.')->group(function () {
        Route::get('/', [App\Http\Controllers\Secretary\OrganisationApplicationController::class, 'index'])->name('index');
        Route::get('/{organization}', [App\Http\Controllers\Secretary\OrganisationApplicationController::class, 'show'])->name('show');
        Route::post('/{organization}/approve', [App\Http\Controllers\Secretary\OrganisationApplicationController::class, 'approve'])->name('approve');
        Route::post('/{organization}/reject', [App\Http\Controllers\Secretary\OrganisationApplicationController::class, 'reject'])->name('reject');
        Route::post('/{organization}/request-changes', [App\Http\Controllers\Secretary\OrganisationApplicationController::class, 'requestChanges'])->name('request-changes');
    });

    // Messages
    Route::prefix('messages')->name('messages.')->group(function () {
        Route::get('/', [App\Http\Controllers\Secretary\MessageController::class, 'index'])->name('index');
        Route::get('/compose', [App\Http\Controllers\Secretary\MessageController::class, 'compose'])->name('compose');
        Route::post('/', [App\Http\Controllers\Secretary\MessageController::class, 'store'])->name('store');
        Route::get('/{message}', [App\Http\Controllers\Secretary\MessageController::class, 'show'])->name('show');
        Route::post('/{message}/reply', [App\Http\Controllers\Secretary\MessageController::class, 'reply'])->name('reply');
    });
});