<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Resource;
use App\Models\Tag;
use App\Models\Organization;
use Illuminate\Http\Request;

class ResourceController extends Controller
{
    public function index(Request $request)
    {
        $query = Resource::with(['organization', 'tags'])->published();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                    ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('type')) {
            $query->ofType($request->type);
        }

        if ($request->filled('theme')) {
            $query->ofTheme($request->theme);
        }

        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }

        if ($request->filled('organization')) {
            $query->where('organization_id', $request->organization);
        }

        $resources = $query->latest()->paginate(12);
        $tags = Tag::general()->get();
        $organizations = Organization::published()->get();

        return view('public.resources.index', compact('resources', 'tags', 'organizations'));
    }

    public function show($slug)
    {
        $resource = Resource::with(['organization', 'tags', 'user'])
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        return view('public.resources.show', compact('resource'));
    }
}