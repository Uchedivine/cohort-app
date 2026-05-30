<?php

namespace App\Services;

use App\Models\Organization;
use App\Models\Story;
use App\Models\Resource;
use App\Models\Event;
use Illuminate\Support\Collection;

class SearchService
{
    /**
     * Search across all content types
     *
     * @param string $query
     * @param array $types
     * @param int $limit
     * @return array
     */
    public function searchAll(string $query, array $types = [], int $limit = 10): array
    {
        $results = [
            'organizations' => collect(),
            'stories' => collect(),
            'resources' => collect(),
            'events' => collect(),
            'total' => 0,
        ];

        if (empty($query)) {
            return $results;
        }

        // Determine which types to search
        $searchTypes = empty($types) ? ['organizations', 'stories', 'resources', 'events'] : $types;

        if (in_array('organizations', $searchTypes)) {
            $results['organizations'] = $this->searchOrganizations($query, $limit);
        }

        if (in_array('stories', $searchTypes)) {
            $results['stories'] = $this->searchStories($query, $limit);
        }

        if (in_array('resources', $searchTypes)) {
            $results['resources'] = $this->searchResources($query, $limit);
        }

        if (in_array('events', $searchTypes)) {
            $results['events'] = $this->searchEvents($query, $limit);
        }

        $results['total'] = $results['organizations']->count() +
                           $results['stories']->count() +
                           $results['resources']->count() +
                           $results['events']->count();

        return $results;
    }

    /**
     * Search organizations
     *
     * @param string $query
     * @param int $limit
     * @return Collection
     */
    public function searchOrganizations(string $query, int $limit = 10): Collection
    {
        return Organization::search($query)
            ->query(fn ($builder) => $builder->with('tags'))
            ->take($limit)
            ->get();
    }

    /**
     * Search stories
     *
     * @param string $query
     * @param int $limit
     * @return Collection
     */
    public function searchStories(string $query, int $limit = 10): Collection
    {
        return Story::search($query)
            ->query(fn ($builder) => $builder->with(['organization', 'tags']))
            ->take($limit)
            ->get();
    }

    /**
     * Search resources
     *
     * @param string $query
     * @param int $limit
     * @return Collection
     */
    public function searchResources(string $query, int $limit = 10): Collection
    {
        return Resource::search($query)
            ->query(fn ($builder) => $builder->with(['organization', 'tags']))
            ->take($limit)
            ->get();
    }

    /**
     * Search events
     *
     * @param string $query
     * @param int $limit
     * @return Collection
     */
    public function searchEvents(string $query, int $limit = 10): Collection
    {
        return Event::search($query)
            ->query(fn ($builder) => $builder->with('tags'))
            ->take($limit)
            ->get();
    }

    /**
     * Get search suggestions
     *
     * @param string $query
     * @param int $limit
     * @return array
     */
    public function getSuggestions(string $query, int $limit = 5): array
    {
        if (strlen($query) < 2) {
            return [];
        }

        $suggestions = [];

        // Get organization names
        $orgs = Organization::published()
            ->where('name', 'like', "%{$query}%")
            ->limit($limit)
            ->pluck('name')
            ->toArray();

        // Get story titles
        $stories = Story::published()
            ->where('title', 'like', "%{$query}%")
            ->limit($limit)
            ->pluck('title')
            ->toArray();

        // Get resource titles
        $resources = Resource::published()
            ->where('title', 'like', "%{$query}%")
            ->limit($limit)
            ->pluck('title')
            ->toArray();

        // Merge and deduplicate
        $suggestions = array_unique(array_merge($orgs, $stories, $resources));

        return array_slice($suggestions, 0, $limit);
    }

    /**
     * Get popular search terms
     *
     * @param int $limit
     * @return array
     */
    public function getPopularSearches(int $limit = 10): array
    {
        // This would require a search_logs table to track searches
        // For now, return common terms based on tags
        return [
            'education',
            'health',
            'environment',
            'governance',
            'agriculture',
        ];
    }

    /**
     * Advanced search with filters
     *
     * @param string $query
     * @param array $filters
     * @return array
     */
    public function advancedSearch(string $query, array $filters = []): array
    {
        $results = [
            'organizations' => collect(),
            'stories' => collect(),
            'resources' => collect(),
            'events' => collect(),
        ];

        // Organizations
        if (!isset($filters['type']) || $filters['type'] === 'organizations') {
            $orgQuery = Organization::search($query);
            
            if (isset($filters['location'])) {
                $orgQuery->where('location', $filters['location']);
            }
            
            if (isset($filters['thematic_focus'])) {
                $orgQuery->where('thematic_focus', $filters['thematic_focus']);
            }

            $results['organizations'] = $orgQuery
                ->query(fn ($builder) => $builder->with('tags'))
                ->paginate($filters['per_page'] ?? 12);
        }

        // Stories
        if (!isset($filters['type']) || $filters['type'] === 'stories') {
            $storyQuery = Story::search($query);
            
            if (isset($filters['organization_id'])) {
                $storyQuery->where('organization_id', $filters['organization_id']);
            }

            $results['stories'] = $storyQuery
                ->query(fn ($builder) => $builder->with(['organization', 'tags']))
                ->paginate($filters['per_page'] ?? 12);
        }

        // Resources
        if (!isset($filters['type']) || $filters['type'] === 'resources') {
            $resourceQuery = Resource::search($query);
            
            if (isset($filters['resource_type'])) {
                $resourceQuery->where('resource_type', $filters['resource_type']);
            }
            
            if (isset($filters['theme'])) {
                $resourceQuery->where('theme', $filters['theme']);
            }
            
            if (isset($filters['year'])) {
                $resourceQuery->where('year', $filters['year']);
            }

            $results['resources'] = $resourceQuery
                ->query(fn ($builder) => $builder->with(['organization', 'tags']))
                ->paginate($filters['per_page'] ?? 12);
        }

        // Events
        if (!isset($filters['type']) || $filters['type'] === 'events') {
            $eventQuery = Event::search($query);
            
            if (isset($filters['upcoming'])) {
                $eventQuery->where('start_date', '>=', now());
            }

            $results['events'] = $eventQuery
                ->query(fn ($builder) => $builder->with('tags'))
                ->paginate($filters['per_page'] ?? 12);
        }

        return $results;
    }
}
