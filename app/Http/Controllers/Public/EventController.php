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
}