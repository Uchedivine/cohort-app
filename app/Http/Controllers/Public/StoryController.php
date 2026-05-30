<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Story;
use App\Models\Tag;
use App\Models\Organization;
use Illuminate\Http\Request;

class StoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Story::with(['organization', 'tags'])->published();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                    ->orWhere('summary', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('organization')) {
            $query->where('organization_id', $request->organization);
        }

        if ($request->filled('tag')) {
            $query->whereHas('tags', function ($q) use ($request) {
                $q->where('slug', $request->tag);
            });
        }

        $stories = $query->latest()->paginate(12);
        $tags = Tag::general()->get();
        $organizations = Organization::published()->get();

        return view('public.stories.index', compact('stories', 'tags', 'organizations'));
    }

    public function show($slug)
    {
        $story = Story::with(['organization', 'tags', 'user'])
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        return view('public.stories.show', compact('story'));
    }
}