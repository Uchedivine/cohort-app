<?php

namespace App\Http\Controllers;

use App\Services\SearchService;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function __construct(
        private SearchService $searchService
    ) {}

    /**
     * Global search page
     */
    public function index(Request $request)
    {
        $query = $request->input('q', '');
        $type = $request->input('type', 'all');
        
        if (empty($query)) {
            return view('search.index', [
                'query' => '',
                'results' => [],
                'type' => $type,
            ]);
        }

        // Determine which types to search
        $types = $type === 'all' ? [] : [$type];

        // Perform search
        $results = $this->searchService->searchAll($query, $types, 20);

        return view('search.index', [
            'query' => $query,
            'results' => $results,
            'type' => $type,
        ]);
    }

    /**
     * Search suggestions API endpoint
     */
    public function suggestions(Request $request)
    {
        $query = $request->input('q', '');
        
        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $suggestions = $this->searchService->getSuggestions($query, 5);

        return response()->json($suggestions);
    }

    /**
     * Advanced search page
     */
    public function advanced(Request $request)
    {
        $query = $request->input('q', '');
        
        $filters = [
            'type' => $request->input('type'),
            'location' => $request->input('location'),
            'thematic_focus' => $request->input('thematic_focus'),
            'resource_type' => $request->input('resource_type'),
            'theme' => $request->input('theme'),
            'year' => $request->input('year'),
            'organization_id' => $request->input('organization_id'),
            'upcoming' => $request->boolean('upcoming'),
            'per_page' => $request->input('per_page', 12),
        ];

        // Remove null filters
        $filters = array_filter($filters, fn($value) => $value !== null);

        $results = $this->searchService->advancedSearch($query, $filters);

        return view('search.advanced', [
            'query' => $query,
            'results' => $results,
            'filters' => $filters,
        ]);
    }
}
