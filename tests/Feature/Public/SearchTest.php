<?php

namespace Tests\Feature\Public;

use App\Models\Organization;
use App\Models\Story;
use App\Models\Resource;
use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function users_can_view_search_page()
    {
        $response = $this->get(route('search.index'));

        $response->assertStatus(200);
        $response->assertViewIs('search.index');
    }

    /** @test */
    public function users_can_search_for_stories()
    {
        Story::factory()->create([
            'title' => 'Education Initiative in Rural Areas',
            'status' => 'published',
        ]);

        Story::factory()->create([
            'title' => 'Healthcare Program',
            'status' => 'published',
        ]);

        $response = $this->get(route('search.index', ['q' => 'education']));

        $response->assertStatus(200);
        $response->assertSee('Education Initiative');
        $response->assertDontSee('Healthcare Program');
    }

    /** @test */
    public function search_only_returns_published_content()
    {
        Story::factory()->create([
            'title' => 'Published Story',
            'status' => 'published',
        ]);

        Story::factory()->create([
            'title' => 'Draft Story',
            'status' => 'draft',
        ]);

        $response = $this->get(route('search.index', ['q' => 'story']));

        $response->assertSee('Published Story');
        $response->assertDontSee('Draft Story');
    }

    /** @test */
    public function users_can_filter_search_by_type()
    {
        Story::factory()->create([
            'title' => 'Test Story',
            'status' => 'published',
        ]);

        Organization::factory()->create([
            'name' => 'Test Organization',
            'status' => 'published',
        ]);

        $response = $this->get(route('search.index', [
            'q' => 'test',
            'type' => 'stories',
        ]));

        $response->assertSee('Test Story');
        $response->assertDontSee('Test Organization');
    }

    /** @test */
    public function empty_search_shows_empty_state()
    {
        $response = $this->get(route('search.index', ['q' => '']));

        $response->assertSee('Start searching');
    }

    /** @test */
    public function no_results_shows_appropriate_message()
    {
        $response = $this->get(route('search.index', ['q' => 'nonexistentquery123']));

        $response->assertSee('No results found');
    }
}
