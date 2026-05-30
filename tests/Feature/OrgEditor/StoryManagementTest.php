<?php

namespace Tests\Feature\OrgEditor;

use App\Models\Organization;
use App\Models\Story;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StoryManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $orgEditor;
    private Organization $organization;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->organization = Organization::factory()->create();
        $this->orgEditor = User::factory()->create([
            'organization_id' => $this->organization->id,
        ]);
        $this->orgEditor->assignRole('org_editor');
    }

    /** @test */
    public function org_editor_can_view_stories_index()
    {
        $response = $this->actingAs($this->orgEditor)
            ->get(route('org-editor.stories.index'));

        $response->assertStatus(200);
        $response->assertViewIs('org-editor.stories.index');
    }

    /** @test */
    public function org_editor_can_view_create_story_form()
    {
        $response = $this->actingAs($this->orgEditor)
            ->get(route('org-editor.stories.create'));

        $response->assertStatus(200);
        $response->assertViewIs('org-editor.stories.create');
    }

    /** @test */
    public function org_editor_can_create_a_story()
    {
        $data = [
            'title' => 'Test Story',
            'summary' => 'This is a test story summary',
            'full_story' => 'This is the full story content',
            'author' => 'John Doe',
            'action' => 'submit',
        ];

        $response = $this->actingAs($this->orgEditor)
            ->post(route('org-editor.stories.store'), $data);

        $response->assertRedirect(route('org-editor.stories.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('stories', [
            'title' => 'Test Story',
            'organization_id' => $this->organization->id,
            'user_id' => $this->orgEditor->id,
        ]);
    }

    /** @test */
    public function org_editor_can_upload_featured_image()
    {
        $file = UploadedFile::fake()->image('featured.jpg', 800, 600);

        $data = [
            'title' => 'Story with Image',
            'summary' => 'Test summary',
            'featured_image' => $file,
            'action' => 'submit',
        ];

        $response = $this->actingAs($this->orgEditor)
            ->post(route('org-editor.stories.store'), $data);

        $response->assertRedirect();
        
        $story = Story::where('title', 'Story with Image')->first();
        $this->assertNotNull($story->featured_image);
        Storage::disk('public')->assertExists($story->featured_image);
    }

    /** @test */
    public function org_editor_can_save_story_as_draft()
    {
        $data = [
            'title' => 'Draft Story',
            'summary' => 'Draft summary',
            'action' => 'draft',
        ];

        $response = $this->actingAs($this->orgEditor)
            ->post(route('org-editor.stories.store'), $data);

        $response->assertRedirect();

        $this->assertDatabaseHas('stories', [
            'title' => 'Draft Story',
            'status' => 'draft',
        ]);
    }

    /** @test */
    public function org_editor_cannot_edit_other_organizations_stories()
    {
        $otherOrg = Organization::factory()->create();
        $otherStory = Story::factory()->create([
            'organization_id' => $otherOrg->id,
        ]);

        $response = $this->actingAs($this->orgEditor)
            ->get(route('org-editor.stories.edit', $otherStory));

        $response->assertStatus(403);
    }

    /** @test */
    public function story_title_is_required()
    {
        $data = [
            'summary' => 'Test summary',
            'action' => 'submit',
        ];

        $response = $this->actingAs($this->orgEditor)
            ->post(route('org-editor.stories.store'), $data);

        $response->assertSessionHasErrors('title');
    }

    /** @test */
    public function featured_image_must_be_valid_image()
    {
        $file = UploadedFile::fake()->create('document.pdf', 100);

        $data = [
            'title' => 'Test Story',
            'summary' => 'Test summary',
            'featured_image' => $file,
            'action' => 'submit',
        ];

        $response = $this->actingAs($this->orgEditor)
            ->post(route('org-editor.stories.store'), $data);

        $response->assertSessionHasErrors('featured_image');
    }
}
