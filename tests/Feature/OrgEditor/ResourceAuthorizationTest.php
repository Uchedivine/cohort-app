<?php

namespace Tests\Feature\OrgEditor;

use App\Models\Organization;
use App\Models\Resource;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ResourceAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        $this->seed(\Database\Seeders\RoleSeeder::class);
    }

    /** @test */
    public function org_editor_can_view_own_resources()
    {
        $org = Organization::factory()->create();
        $user = User::factory()->orgEditor()->create(['organization_id' => $org->id]);
        $resource = Resource::factory()->create([
            'organization_id' => $org->id,
            'user_id' => $user->id,
            'status' => 'draft',
        ]);

        $response = $this->actingAs($user)->get(route('org-editor.resources.index'));

        $response->assertStatus(200);
        $response->assertSee($resource->title);
    }

    /** @test */
    public function org_editor_cannot_view_other_organization_resources()
    {
        $orgA = Organization::factory()->create();
        $orgB = Organization::factory()->create();
        
        $userA = User::factory()->orgEditor()->create(['organization_id' => $orgA->id]);
        $userB = User::factory()->orgEditor()->create(['organization_id' => $orgB->id]);
        
        $resourceB = Resource::factory()->create([
            'organization_id' => $orgB->id,
            'user_id' => $userB->id,
            'status' => 'draft',
        ]);

        $response = $this->actingAs($userA)->get(route('org-editor.resources.index'));

        $response->assertStatus(200);
        $response->assertDontSee($resourceB->title);
    }

    /** @test */
    public function org_editor_can_edit_own_draft_resource()
    {
        $org = Organization::factory()->create();
        $user = User::factory()->orgEditor()->create(['organization_id' => $org->id]);
        $resource = Resource::factory()->create([
            'organization_id' => $org->id,
            'user_id' => $user->id,
            'status' => 'draft',
        ]);

        $response = $this->actingAs($user)->get(route('org-editor.resources.edit', $resource));

        $response->assertStatus(200);
        $response->assertSee($resource->title);
    }

    /** @test */
    public function org_editor_cannot_edit_other_organization_resource()
    {
        $orgA = Organization::factory()->create();
        $orgB = Organization::factory()->create();
        
        $userA = User::factory()->orgEditor()->create(['organization_id' => $orgA->id]);
        $userB = User::factory()->orgEditor()->create(['organization_id' => $orgB->id]);
        
        $resourceB = Resource::factory()->create([
            'organization_id' => $orgB->id,
            'user_id' => $userB->id,
            'status' => 'draft',
        ]);

        $response = $this->actingAs($userA)->get(route('org-editor.resources.edit', $resourceB));

        $response->assertStatus(403);
    }

    /** @test */
    public function org_editor_can_update_own_draft_resource()
    {
        $org = Organization::factory()->create();
        $user = User::factory()->orgEditor()->create(['organization_id' => $org->id]);
        $resource = Resource::factory()->create([
            'organization_id' => $org->id,
            'user_id' => $user->id,
            'status' => 'draft',
            'resource_type' => 'external_link',
        ]);

        $response = $this->actingAs($user)->put(route('org-editor.resources.update', $resource), [
            'title' => 'Updated Title',
            'description' => 'Updated description',
            'type' => 'link',
            'external_url' => 'https://example.com/updated',
        ]);

        $response->assertRedirect(route('org-editor.resources.index'));
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('resources', [
            'id' => $resource->id,
            'title' => 'Updated Title',
            'external_url' => 'https://example.com/updated',
        ]);
    }

    /** @test */
    public function org_editor_cannot_update_other_organization_resource()
    {
        $orgA = Organization::factory()->create();
        $orgB = Organization::factory()->create();
        
        $userA = User::factory()->orgEditor()->create(['organization_id' => $orgA->id]);
        $userB = User::factory()->orgEditor()->create(['organization_id' => $orgB->id]);
        
        $resourceB = Resource::factory()->create([
            'organization_id' => $orgB->id,
            'user_id' => $userB->id,
            'status' => 'draft',
            'resource_type' => 'external_link',
        ]);

        $response = $this->actingAs($userA)->put(route('org-editor.resources.update', $resourceB), [
            'title' => 'Hacked Title',
            'description' => 'Hacked description',
            'type' => 'link',
            'external_url' => 'https://evil.com',
        ]);

        $response->assertStatus(403);
        
        $this->assertDatabaseMissing('resources', [
            'id' => $resourceB->id,
            'title' => 'Hacked Title',
        ]);
    }

    /** @test */
    public function org_editor_cannot_edit_submitted_resource()
    {
        $org = Organization::factory()->create();
        $user = User::factory()->orgEditor()->create(['organization_id' => $org->id]);
        $resource = Resource::factory()->create([
            'organization_id' => $org->id,
            'user_id' => $user->id,
            'status' => 'submitted',
        ]);

        $response = $this->actingAs($user)->get(route('org-editor.resources.edit', $resource));

        $response->assertRedirect(route('org-editor.resources.index'));
        $response->assertSessionHas('error');
    }

    /** @test */
    public function org_editor_cannot_update_submitted_resource()
    {
        $org = Organization::factory()->create();
        $user = User::factory()->orgEditor()->create(['organization_id' => $org->id]);
        $resource = Resource::factory()->create([
            'organization_id' => $org->id,
            'user_id' => $user->id,
            'status' => 'submitted',
            'resource_type' => 'external_link',
        ]);

        $response = $this->actingAs($user)->put(route('org-editor.resources.update', $resource), [
            'title' => 'Updated Title',
            'description' => 'Updated description',
            'type' => 'link',
            'external_url' => 'https://example.com/updated',
        ]);

        $response->assertRedirect(route('org-editor.resources.index'));
        $response->assertSessionHas('error');
        
        $this->assertDatabaseMissing('resources', [
            'id' => $resource->id,
            'title' => 'Updated Title',
        ]);
    }

    /** @test */
    public function org_editor_can_edit_needs_changes_resource()
    {
        $org = Organization::factory()->create();
        $user = User::factory()->orgEditor()->create(['organization_id' => $org->id]);
        $resource = Resource::factory()->create([
            'organization_id' => $org->id,
            'user_id' => $user->id,
            'status' => 'needs_changes',
        ]);

        $response = $this->actingAs($user)->get(route('org-editor.resources.edit', $resource));

        $response->assertStatus(200);
        $response->assertSee($resource->title);
    }

    /** @test */
    public function org_editor_can_delete_own_draft_resource()
    {
        $org = Organization::factory()->create();
        $user = User::factory()->orgEditor()->create(['organization_id' => $org->id]);
        $resource = Resource::factory()->create([
            'organization_id' => $org->id,
            'user_id' => $user->id,
            'status' => 'draft',
        ]);

        $response = $this->actingAs($user)->delete(route('org-editor.resources.destroy', $resource));

        $response->assertRedirect(route('org-editor.resources.index'));
        $response->assertSessionHas('success');
        
        $this->assertDatabaseMissing('resources', [
            'id' => $resource->id,
        ]);
    }

    /** @test */
    public function org_editor_cannot_delete_other_organization_resource()
    {
        $orgA = Organization::factory()->create();
        $orgB = Organization::factory()->create();
        
        $userA = User::factory()->orgEditor()->create(['organization_id' => $orgA->id]);
        $userB = User::factory()->orgEditor()->create(['organization_id' => $orgB->id]);
        
        $resourceB = Resource::factory()->create([
            'organization_id' => $orgB->id,
            'user_id' => $userB->id,
            'status' => 'draft',
        ]);

        $response = $this->actingAs($userA)->delete(route('org-editor.resources.destroy', $resourceB));

        $response->assertStatus(403);
        
        $this->assertDatabaseHas('resources', [
            'id' => $resourceB->id,
        ]);
    }

    /** @test */
    public function org_editor_cannot_delete_submitted_resource()
    {
        $org = Organization::factory()->create();
        $user = User::factory()->orgEditor()->create(['organization_id' => $org->id]);
        $resource = Resource::factory()->create([
            'organization_id' => $org->id,
            'user_id' => $user->id,
            'status' => 'submitted',
        ]);

        $response = $this->actingAs($user)->delete(route('org-editor.resources.destroy', $resource));

        $response->assertRedirect(route('org-editor.resources.index'));
        $response->assertSessionHas('error');
        
        $this->assertDatabaseHas('resources', [
            'id' => $resource->id,
        ]);
    }

    /** @test */
    public function org_editor_cannot_delete_published_resource()
    {
        $org = Organization::factory()->create();
        $user = User::factory()->orgEditor()->create(['organization_id' => $org->id]);
        $resource = Resource::factory()->create([
            'organization_id' => $org->id,
            'user_id' => $user->id,
            'status' => 'published',
        ]);

        $response = $this->actingAs($user)->delete(route('org-editor.resources.destroy', $resource));

        $response->assertRedirect(route('org-editor.resources.index'));
        $response->assertSessionHas('error');
        
        $this->assertDatabaseHas('resources', [
            'id' => $resource->id,
        ]);
    }

    /** @test */
    public function file_upload_replaces_existing_file()
    {
        $org = Organization::factory()->create();
        $user = User::factory()->orgEditor()->create(['organization_id' => $org->id]);
        
        $oldFile = UploadedFile::fake()->create('old-document.pdf', 100);
        $resource = Resource::factory()->create([
            'organization_id' => $org->id,
            'user_id' => $user->id,
            'status' => 'draft',
            'resource_type' => 'file',
            'file_path' => 'resources/old-document.pdf',
        ]);

        $newFile = UploadedFile::fake()->create('new-document.pdf', 150);

        $response = $this->actingAs($user)->put(route('org-editor.resources.update', $resource), [
            'title' => $resource->title,
            'description' => $resource->description,
            'type' => 'file',
            'file_path' => $newFile,
        ]);

        $response->assertRedirect(route('org-editor.resources.index'));
        
        $resource->refresh();
        $this->assertNotEquals('resources/old-document.pdf', $resource->file_path);
        $this->assertStringContainsString('.pdf', $resource->file_path);
    }

    /** @test */
    public function validation_requires_correct_fields_based_on_type()
    {
        $org = Organization::factory()->create();
        $user = User::factory()->orgEditor()->create(['organization_id' => $org->id]);
        $resource = Resource::factory()->create([
            'organization_id' => $org->id,
            'user_id' => $user->id,
            'status' => 'draft',
            'resource_type' => 'external_link',
        ]);

        // Test link type requires external_url
        $response = $this->actingAs($user)->put(route('org-editor.resources.update', $resource), [
            'title' => 'Test',
            'description' => 'Test description',
            'type' => 'link',
            // Missing external_url
        ]);

        $response->assertSessionHasErrors('external_url');
    }
}
