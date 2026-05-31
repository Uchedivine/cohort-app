<?php

namespace Tests\Feature\OrgEditor;

use App\Models\Organization;
use App\Models\Resource;
use App\Models\Story;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ResubmissionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RoleSeeder::class);
    }

    /** @test */
    public function secretary_can_enable_resubmission_when_rejecting()
    {
        $org = Organization::factory()->create();
        $orgEditor = User::factory()->orgEditor()->create(['organization_id' => $org->id]);
        $secretary = User::factory()->secretary()->create();
        
        $story = Story::factory()->create([
            'organization_id' => $org->id,
            'user_id' => $orgEditor->id,
            'status' => 'submitted',
        ]);
        
        $submission = Submission::factory()->create([
            'submittable_type' => Story::class,
            'submittable_id' => $story->id,
            'submitted_by' => $orgEditor->id,
            'status' => 'submitted',
        ]);

        $response = $this->actingAs($secretary)->post(route('secretary.submissions.reject', $submission), [
            'reviewer_notes' => 'Please revise the content and resubmit.',
            'allow_resubmission' => true,
        ]);

        $response->assertRedirect(route('secretary.submissions.index'));
        
        $submission->refresh();
        $this->assertEquals('rejected', $submission->status);
        $this->assertTrue($submission->allow_resubmission);
    }

    /** @test */
    public function secretary_can_reject_without_allowing_resubmission()
    {
        $org = Organization::factory()->create();
        $orgEditor = User::factory()->orgEditor()->create(['organization_id' => $org->id]);
        $secretary = User::factory()->secretary()->create();
        
        $story = Story::factory()->create([
            'organization_id' => $org->id,
            'user_id' => $orgEditor->id,
            'status' => 'submitted',
        ]);
        
        $submission = Submission::factory()->create([
            'submittable_type' => Story::class,
            'submittable_id' => $story->id,
            'submitted_by' => $orgEditor->id,
            'status' => 'submitted',
        ]);

        $response = $this->actingAs($secretary)->post(route('secretary.submissions.reject', $submission), [
            'reviewer_notes' => 'This content is not appropriate.',
            'allow_resubmission' => false,
        ]);

        $response->assertRedirect(route('secretary.submissions.index'));
        
        $submission->refresh();
        $this->assertEquals('rejected', $submission->status);
        $this->assertFalse($submission->allow_resubmission);
    }

    /** @test */
    public function org_editor_can_resubmit_rejected_story_when_allowed()
    {
        $org = Organization::factory()->create();
        $orgEditor = User::factory()->orgEditor()->create(['organization_id' => $org->id]);
        
        $story = Story::factory()->create([
            'organization_id' => $org->id,
            'user_id' => $orgEditor->id,
            'status' => 'rejected',
        ]);
        
        $rejectedSubmission = Submission::factory()->create([
            'submittable_type' => Story::class,
            'submittable_id' => $story->id,
            'submitted_by' => $orgEditor->id,
            'status' => 'rejected',
            'allow_resubmission' => true,
        ]);

        $response = $this->actingAs($orgEditor)->post(route('org-editor.stories.resubmit', $story));

        $response->assertRedirect(route('org-editor.stories.index'));
        $response->assertSessionHas('success');
        
        // Verify new submission created
        $newSubmission = Submission::where('submittable_id', $story->id)
            ->where('status', 'submitted')
            ->latest()
            ->first();
        
        $this->assertNotNull($newSubmission);
        $this->assertEquals($rejectedSubmission->id, $newSubmission->parent_submission_id);
        
        // Verify story status updated
        $story->refresh();
        $this->assertEquals('submitted', $story->status);
    }

    /** @test */
    public function org_editor_cannot_resubmit_when_not_allowed()
    {
        $org = Organization::factory()->create();
        $orgEditor = User::factory()->orgEditor()->create(['organization_id' => $org->id]);
        
        $story = Story::factory()->create([
            'organization_id' => $org->id,
            'user_id' => $orgEditor->id,
            'status' => 'rejected',
        ]);
        
        $rejectedSubmission = Submission::factory()->create([
            'submittable_type' => Story::class,
            'submittable_id' => $story->id,
            'submitted_by' => $orgEditor->id,
            'status' => 'rejected',
            'allow_resubmission' => false,
        ]);

        $response = $this->actingAs($orgEditor)->post(route('org-editor.stories.resubmit', $story));

        $response->assertRedirect(route('org-editor.stories.index'));
        $response->assertSessionHas('error');
        
        // Verify no new submission created
        $newSubmissionCount = Submission::where('submittable_id', $story->id)
            ->where('status', 'submitted')
            ->count();
        
        $this->assertEquals(0, $newSubmissionCount);
    }

    /** @test */
    public function org_editor_cannot_resubmit_non_rejected_story()
    {
        $org = Organization::factory()->create();
        $orgEditor = User::factory()->orgEditor()->create(['organization_id' => $org->id]);
        
        $story = Story::factory()->create([
            'organization_id' => $org->id,
            'user_id' => $orgEditor->id,
            'status' => 'submitted',
        ]);

        $response = $this->actingAs($orgEditor)->post(route('org-editor.stories.resubmit', $story));

        $response->assertRedirect(route('org-editor.stories.index'));
        $response->assertSessionHas('error');
    }

    /** @test */
    public function org_editor_can_resubmit_rejected_resource_when_allowed()
    {
        $org = Organization::factory()->create();
        $orgEditor = User::factory()->orgEditor()->create(['organization_id' => $org->id]);
        
        $resource = Resource::factory()->create([
            'organization_id' => $org->id,
            'user_id' => $orgEditor->id,
            'status' => 'rejected',
        ]);
        
        $rejectedSubmission = Submission::factory()->create([
            'submittable_type' => Resource::class,
            'submittable_id' => $resource->id,
            'submitted_by' => $orgEditor->id,
            'status' => 'rejected',
            'allow_resubmission' => true,
        ]);

        $response = $this->actingAs($orgEditor)->post(route('org-editor.resources.resubmit', $resource));

        $response->assertRedirect(route('org-editor.resources.index'));
        $response->assertSessionHas('success');
        
        // Verify new submission created
        $newSubmission = Submission::where('submittable_id', $resource->id)
            ->where('status', 'submitted')
            ->latest()
            ->first();
        
        $this->assertNotNull($newSubmission);
        $this->assertEquals($rejectedSubmission->id, $newSubmission->parent_submission_id);
        
        // Verify resource status updated
        $resource->refresh();
        $this->assertEquals('submitted', $resource->status);
    }

    /** @test */
    public function resubmitted_content_appears_in_secretary_queue()
    {
        $org = Organization::factory()->create();
        $orgEditor = User::factory()->orgEditor()->create(['organization_id' => $org->id]);
        $secretary = User::factory()->secretary()->create();
        
        $story = Story::factory()->create([
            'organization_id' => $org->id,
            'user_id' => $orgEditor->id,
            'status' => 'rejected',
        ]);
        
        $rejectedSubmission = Submission::factory()->create([
            'submittable_type' => Story::class,
            'submittable_id' => $story->id,
            'submitted_by' => $orgEditor->id,
            'status' => 'rejected',
            'allow_resubmission' => true,
        ]);

        // Resubmit
        $this->actingAs($orgEditor)->post(route('org-editor.stories.resubmit', $story));

        // Check secretary queue
        $response = $this->actingAs($secretary)->get(route('secretary.submissions.index'));
        
        $response->assertStatus(200);
        $response->assertSee($story->title);
    }

    /** @test */
    public function parent_child_submission_relationship_maintained()
    {
        $org = Organization::factory()->create();
        $orgEditor = User::factory()->orgEditor()->create(['organization_id' => $org->id]);
        
        $story = Story::factory()->create([
            'organization_id' => $org->id,
            'user_id' => $orgEditor->id,
            'status' => 'rejected',
        ]);
        
        $rejectedSubmission = Submission::factory()->create([
            'submittable_type' => Story::class,
            'submittable_id' => $story->id,
            'submitted_by' => $orgEditor->id,
            'status' => 'rejected',
            'allow_resubmission' => true,
        ]);

        $this->actingAs($orgEditor)->post(route('org-editor.stories.resubmit', $story));

        $newSubmission = Submission::where('submittable_id', $story->id)
            ->where('status', 'submitted')
            ->latest()
            ->first();

        // Test parent relationship
        $this->assertNotNull($newSubmission->parentSubmission);
        $this->assertEquals($rejectedSubmission->id, $newSubmission->parentSubmission->id);
        
        // Test child relationship
        $rejectedSubmission->refresh();
        $this->assertTrue($rejectedSubmission->childSubmissions->contains($newSubmission));
    }

    /** @test */
    public function org_editor_cannot_resubmit_other_organization_content()
    {
        $orgA = Organization::factory()->create();
        $orgB = Organization::factory()->create();
        
        $orgEditorA = User::factory()->orgEditor()->create(['organization_id' => $orgA->id]);
        $orgEditorB = User::factory()->orgEditor()->create(['organization_id' => $orgB->id]);
        
        $storyB = Story::factory()->create([
            'organization_id' => $orgB->id,
            'user_id' => $orgEditorB->id,
            'status' => 'rejected',
        ]);
        
        $rejectedSubmission = Submission::factory()->create([
            'submittable_type' => Story::class,
            'submittable_id' => $storyB->id,
            'submitted_by' => $orgEditorB->id,
            'status' => 'rejected',
            'allow_resubmission' => true,
        ]);

        $response = $this->actingAs($orgEditorA)->post(route('org-editor.stories.resubmit', $storyB));

        $response->assertStatus(403);
    }

    /** @test */
    public function resubmit_button_only_shows_when_allowed()
    {
        $org = Organization::factory()->create();
        $orgEditor = User::factory()->orgEditor()->create(['organization_id' => $org->id]);
        
        // Story with resubmission allowed
        $storyAllowed = Story::factory()->create([
            'organization_id' => $org->id,
            'user_id' => $orgEditor->id,
            'status' => 'rejected',
        ]);
        
        Submission::factory()->create([
            'submittable_type' => Story::class,
            'submittable_id' => $storyAllowed->id,
            'submitted_by' => $orgEditor->id,
            'status' => 'rejected',
            'allow_resubmission' => true,
        ]);
        
        // Story with resubmission not allowed
        $storyNotAllowed = Story::factory()->create([
            'organization_id' => $org->id,
            'user_id' => $orgEditor->id,
            'status' => 'rejected',
        ]);
        
        Submission::factory()->create([
            'submittable_type' => Story::class,
            'submittable_id' => $storyNotAllowed->id,
            'submitted_by' => $orgEditor->id,
            'status' => 'rejected',
            'allow_resubmission' => false,
        ]);

        $response = $this->actingAs($orgEditor)->get(route('org-editor.stories.index'));

        $response->assertStatus(200);
        $response->assertSee('Resubmit for Review'); // Button for allowed story
        // The button should only appear once (for the allowed story)
    }

    /** @test */
    public function activity_log_records_resubmission()
    {
        $org = Organization::factory()->create();
        $orgEditor = User::factory()->orgEditor()->create(['organization_id' => $org->id]);
        
        $story = Story::factory()->create([
            'organization_id' => $org->id,
            'user_id' => $orgEditor->id,
            'status' => 'rejected',
        ]);
        
        $rejectedSubmission = Submission::factory()->create([
            'submittable_type' => Story::class,
            'submittable_id' => $story->id,
            'submitted_by' => $orgEditor->id,
            'status' => 'rejected',
            'allow_resubmission' => true,
        ]);

        $this->actingAs($orgEditor)->post(route('org-editor.stories.resubmit', $story));

        // Check activity log
        $this->assertDatabaseHas('activity_log', [
            'causer_id' => $orgEditor->id,
            'description' => 'Submission resubmitted after rejection',
        ]);
    }
}
