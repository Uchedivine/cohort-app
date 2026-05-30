<?php

namespace App\Http\Controllers\Secretary;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tag\StoreTagRequest;
use App\Models\Tag;
use Illuminate\Support\Str;

class TagController extends Controller
{
    public function index()
    {
        $tags = Tag::withCount(['stories', 'resources', 'events'])
            ->latest()
            ->paginate(20);

        return view('secretary.tags.index', compact('tags'));
    }

    public function store(StoreTagRequest $request)
    {
        $validated = $request->validated();

        // Generate slug
        $validated['slug'] = Str::slug($validated['name']);

        // Create tag
        $tag = Tag::create($validated);

        // Log activity
        activity()
            ->performedOn($tag)
            ->causedBy(auth()->user())
            ->withProperties(['type' => $validated['type']])
            ->log('Tag created');

        return redirect()->route('secretary.tags.index')
            ->with('success', 'Tag created successfully.');
    }

    public function destroy(Tag $tag)
    {
        // Check if tag is in use
        $usageCount = $tag->stories()->count() + 
                      $tag->resources()->count() + 
                      $tag->events()->count();

        if ($usageCount > 0) {
            return back()->with('error', "Cannot delete tag. It is currently used by {$usageCount} item(s).");
        }

        // Log activity before deletion
        activity()
            ->performedOn($tag)
            ->causedBy(auth()->user())
            ->withProperties(['tag_name' => $tag->name])
            ->log('Tag deleted');

        $tag->delete();

        return redirect()->route('secretary.tags.index')
            ->with('success', 'Tag deleted successfully.');
    }
}