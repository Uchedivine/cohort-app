<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Tag;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $upcomingEvents = Event::with(['tags', 'media'])
            ->published()
            ->upcoming()
            ->orderBy('start_date')
            ->get();

        $pastEvents = Event::with(['tags', 'media'])
            ->published()
            ->past()
            ->latest('start_date')
            ->paginate(9);

        $tags = Tag::general()->get();

        return view('public.events.index', compact('upcomingEvents', 'pastEvents', 'tags'));
    }

    public function show($slug)
    {
        $event = Event::with(['tags', 'media'])
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        return view('public.events.show', compact('event'));
    }

    /**
     * Return events data in FullCalendar JSON format
     */
    public function calendarData(Request $request)
    {
        $events = Event::published()
            ->select('id', 'title', 'slug', 'start_date', 'end_date', 'location', 'virtual_link')
            ->get()
            ->map(function ($event) {
                return [
                    'id' => $event->id,
                    'title' => $event->title,
                    'start' => $event->start_date->toIso8601String(),
                    'end' => $event->end_date ? $event->end_date->toIso8601String() : null,
                    'url' => route('events.show', $event->slug),
                    'extendedProps' => [
                        'location' => $event->location,
                        'virtual' => !empty($event->virtual_link),
                    ],
                ];
            });

        return response()->json($events);
    }
}