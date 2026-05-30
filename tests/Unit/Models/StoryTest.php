<?php

namespace Tests\Unit\Models;

use App\Models\Organization;
use App\Models\Story;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoryTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_belongs_to_an_organization()
    {
        $organization = Organization::factory()->create();
        $story = Story::factory()->create([
            'organization_id' => $organization->id,
        ]);

        $this->assertInstanceOf(Organization::class, $story->organization);
        $this->assertEquals($organization->id, $story->organization->id);
    }

    /** @test */
    public function it_belongs_to_a_user()
    {
        $user = User::factory()->create();
        $story = Story::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->assertInstanceOf(User::class, $story->user);
        $this->assertEquals($user->id, $story->user->id);
    }

    /** @test */
    public function it_can_have_tags()
    {
        $story = Story::factory()->create();
        $tag = Tag::factory()->create();

        $story->tags()->attach($tag);

        $this->assertCount(1, $story->tags);
        $this->assertEquals($tag->id, $story->tags->first()->id);
    }

    /** @test */
    public function published_scope_only_returns_published_stories()
    {
        Story::factory()->create(['status' => 'published']);
        Story::factory()->create(['status' => 'published']);
        Story::factory()->create(['status' => 'draft']);
        Story::factory()->create(['status' => 'submitted']);

        $published = Story::published()->get();

        $this->assertCount(2, $published);
    }

    /** @test */
    public function it_should_be_searchable_only_when_published()
    {
        $published = Story::factory()->create(['status' => 'published']);
        $draft = Story::factory()->create(['status' => 'draft']);

        $this->assertTrue($published->shouldBeSearchable());
        $this->assertFalse($draft->shouldBeSearchable());
    }

    /** @test */
    public function searchable_array_includes_relevant_fields()
    {
        $organization = Organization::factory()->create(['name' => 'Test Org']);
        $story = Story::factory()->create([
            'organization_id' => $organization->id,
            'title' => 'Test Story',
            'summary' => 'Test Summary',
        ]);

        $searchable = $story->toSearchableArray();

        $this->assertArrayHasKey('title', $searchable);
        $this->assertArrayHasKey('summary', $searchable);
        $this->assertArrayHasKey('organization_name', $searchable);
        $this->assertEquals('Test Org', $searchable['organization_name']);
    }
}
