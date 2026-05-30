<?php

namespace App\Http\Controllers\Secretary;

use App\Http\Controllers\Controller;
use App\Http\Requests\Event\StoreEventRequest;
use App\Http\Requests\Event\UpdateEventRequest;
use App\Models\Event;
use App\Models\EventMedia;
use App\Models\Tag;
use App\Services\FileUploadService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EventController extends Controller
{
    public function __construct(
        private FileUploadService $fileUploadService,
        private NotificationService $notificationService
    ) {}

    public function index()
    {
        $events = Event::with(['tags', 'media'])
            ->latest('start_date')
            ->paginate(15);

        return view('secretary.events.index', compact('events'));
    }

    public function create()
    {
        $tags = Tag::all();
        return view('secretary.events.create', compact('tags'));
    }

    public function store(StoreEventRequest $request)
    {
        $validated = $request->validated();

        // Handle banner image upload using FileUploadService
        if ($request->hasFile('banner_image')) {
            try {
                $uploadResult = $this->fileUploadService->uploadImage(
                    $request->file('banner_image'),
                    'events',
                    false
                );
                $validated['banner_image'] = $uploadResult['path'];
            } catch (\InvalidArgumentException $e) {
                return back()->withErrors(['banner_image' => $e->getMessage()])->withInput();
            }
        }

        // Set metadata
        $validated['user_id'] = auth()->id();
        $validated['slug'] = Str::slug($validated['title']) . '-' . Str::random(5);
        $validated['status'] = 'published';
        $validated['published_at'] = now();

        // Create event
        $event = Event::create($validated);

        // Sync tags
        if ($request->filled('tags')) {
            $event->tags()->sync($request->tags);
        }

        // Log activity
        activity()
            ->performedOn($event)
            ->causedBy(auth()->user())
            ->log('Event created and published');

        // Send notification to all org editors
        event(new \App\Events\EventPublished($event));

        return redirect()->route('secretary.events.index')
            ->with('success', 'Event created and published successfully.');
    }

    public function edit(Event $event)
    {
        $tags = Tag::all();
        return view('secretary.events.edit', compact('event', 'tags'));
    }

    public function update(UpdateEventRequest $request, Event $event)
    {
        $validated = $request->validated();

        // Handle banner image upload
        if ($request->hasFile('banner_image')) {
            try {
                $uploadResult = $this->fileUploadService->uploadImage(
                    $request->file('banner_image'),
                    'events',
                    false
                );
                $validated['banner_image'] = $uploadResult['path'];

                // Delete old banner
                if ($event->banner_image) {
                    $this->fileUploadService->deleteFile($event->banner_image);
                }
            } catch (\InvalidArgumentException $e) {
                return back()->withErrors(['banner_image' => $e->getMessage()])->withInput();
            }
        }

        // Update event
        $event->update($validated);

        // Sync tags
        if ($request->filled('tags')) {
            $event->tags()->sync($request->tags);
        }

        // Log activity
        activity()
            ->performedOn($event)
            ->causedBy(auth()->user())
            ->log('Event updated');

        return redirect()->route('secretary.events.index')
            ->with('success', 'Event updated successfully.');
    }

    public function destroy(Event $event)
    {
        // Delete associated media files
        if ($event->banner_image) {
            $this->fileUploadService->deleteFile($event->banner_image);
        }

        foreach ($event->media as $media) {
            if ($media->file_path) {
                $this->fileUploadService->deleteFile($media->file_path);
            }
        }

        // Log activity before deletion
        activity()
            ->performedOn($event)
            ->causedBy(auth()->user())
            ->withProperties(['event_title' => $event->title])
            ->log('Event deleted');

        $event->delete();

        return redirect()->route('secretary.events.index')
            ->with('success', 'Event deleted successfully.');
    }

    public function uploadMedia(Request $request, Event $event)
    {
        $request->validate([
            'media_type' => 'required|in:image,video',
            'file_path' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'video_url' => 'nullable|url',
            'caption' => 'nullable|string|max:255',
        ]);

        $data = [
            'event_id' => $event->id,
            'user_id' => auth()->id(),
            'media_type' => $request->media_type,
            'caption' => $request->caption,
        ];

        // Handle image upload
        if ($request->hasFile('file_path')) {
            try {
                $uploadResult = $this->fileUploadService->uploadImage(
                    $request->file('file_path'),
                    'event-media',
                    true // Create thumbnail
                );
                $data['file_path'] = $uploadResult['path'];
            } catch (\InvalidArgumentException $e) {
                return back()->withErrors(['file_path' => $e->getMessage()]);
            }
        }

        // Handle video URL
        if ($request->filled('video_url')) {
            $data['video_url'] = $request->video_url;
        }

        EventMedia::create($data);

        // Log activity
        activity()
            ->performedOn($event)
            ->causedBy(auth()->user())
            ->withProperties(['media_type' => $request->media_type])
            ->log('Event media uploaded');

        return redirect()->route('secretary.events.edit', $event)
            ->with('success', 'Media uploaded successfully.');
    }
}