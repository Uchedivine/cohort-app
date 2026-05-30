<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\Tag;
use Illuminate\Http\Request;

class OrganizationController extends Controller
{
    public function index(Request $request)
    {
        $query = Organization::with(['tags', 'user'])
            ->published();

        // Filter by location
        if ($request->filled('location')) {
            $query->where('location', 'like', '%' . $request->location . '%');
        }

        // Filter by thematic focus
        if ($request->filled('thematic_focus')) {
            $query->where('thematic_focus', $request->thematic_focus);
        }

        // Filter by tag
        if ($request->filled('tag')) {
            $query->whereHas('tags', function ($q) use ($request) {
                $q->where('slug', $request->tag);
            });
        }

        // Search by name
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('short_description', 'like', '%' . $request->search . '%');
        }

        $organizations = $query->paginate(12);
        $tags = Tag::thematic()->get();

        return view('public.organizations.index', compact('organizations', 'tags'));
    }

    public function show($slug)
    {
        $organization = Organization::with(['tags', 'stories' => function ($q) {
            $q->published()->latest()->take(3);
        }, 'resources' => function ($q) {
            $q->published()->latest()->take(3);
        }])
        ->published()
        ->where('slug', $slug)
        ->firstOrFail();

        return view('public.organizations.show', compact('organization'));
    }
}